#!/usr/bin/env bash
#
# check-php.sh — rede de segurança antes de mexer no tema ipcn-fse.
#
# Invocação:
#   bash scripts/check-php.sh                  # verifica todo o PHP do tema e dos mu-plugins
#   bash scripts/check-php.sh <caminho> [...]  # verifica só esses ficheiros (ou pastas)
#   bash scripts/check-php.sh --help           # mostra esta ajuda
#
# Três verificações, sem alterar ficheiros e sem fazer deploy:
#   1. sintaxe       — `php -l` em cada ficheiro .php;
#   2. block markup  — `<!-- wp:` em qualquer .php sob inc/ (o markup vive nos templates .html);
#   3. <style>       — `<style` em qualquer .php sob inc/ (o CSS vive no style.css).
#
# As verificações 2 e 3 limitam-se a `inc/` de propósito: os templates .html contêm block markup
# legítimo e o functions.php actual ainda tem `<!-- wp:` nas suas strings (defeito a corrigir na 1.2).
# "Sob inc/" é medido a partir da raiz do repositório: um checkout cujo caminho contenha um
# directório chamado `inc` não fica permanentemente vermelho por causa disso.
#
# Lista sempre os ficheiros verificados e diz quais das três verificações correram, para não dar
# sensação falsa de cobertura: sem nenhum ficheiro sob inc/, as verificações 2 e 3 não correm.
# Pode correr-se de qualquer directório: as raízes por omissão derivam da posição do script.
#
# Código de saída:
#   0 — tudo ok (ou nenhum PHP encontrado nas raízes que existem);
#   1 — pelo menos um problema (sintaxe, block markup ou <style>) — tem precedência;
#   2 — erro de ambiente: `php` ausente, raiz por omissão ausente, ou caminho inexistente.
#
# Sem build step e sem dependências: só bash >= 4, `php` e coreutils.
# Os testes deste próprio script estão em `scripts/check-php.test.sh`.

set -u

if [ "${BASH_VERSINFO[0]:-0}" -lt 4 ]; then
	printf 'check-php: preciso de bash >= 4 (encontrei %s).\n' "${BASH_VERSION:-desconhecido}" >&2
	exit 2
fi

for tool in php find grep sed sort head; do
	if ! command -v "$tool" >/dev/null 2>&1; then
		if [ "$tool" = php ]; then
			printf 'check-php: não encontrei o `php` no PATH.\n' >&2
			printf 'check-php: instala o PHP CLI (ex.: `sudo apt install php-cli`) e volta a correr.\n' >&2
		else
			printf 'check-php: não encontrei o `%s` no PATH; sem ele a verificação não é fiável.\n' "$tool" >&2
		fi
		exit 2
	fi
done

usage() {
	sed -n '3,28p' "$0" | sed 's/^# \{0,1\}//'
}

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_DIR="wp-content/themes/ipcn-fse"
MU_DIR="wp-content/mu-plugins"

is_php() {
	case "$1" in
		*.php) return 0 ;;
		*) return 1 ;;
	esac
}

# Mede a localização a partir da raiz do repositório: um caminho fora dela fica como está.
rel_to_repo() {
	case "$1" in
		"$REPO_ROOT"/*) printf '%s' "${1#"$REPO_ROOT"/}" ;;
		*) printf '%s' "$1" ;;
	esac
}

is_under_inc() {
	case "$1" in
		inc/*|*/inc/*|inc) return 0 ;;
		*) return 1 ;;
	esac
}

# ---------------------------------------------------------------------------
# Recolher os ficheiros a verificar: só os caminhos dados, ou o tema + mu-plugins.
# Guardamos o caminho absoluto (para `php -l`), a localização relativa (para decidir
# se é `inc/`) e o rótulo a mostrar.
# ---------------------------------------------------------------------------
targets=() # caminhos absolutos, para operar
rels=()    # localizações relativas à raiz, para classificar
labels=()  # como mostrar cada ficheiro
declare -A seen=()
missing=0

add_target() {
	local abs="$1" label="$2"
	if [ -z "${seen[$abs]:-}" ]; then
		seen[$abs]=1
		targets+=("$abs")
		rels+=("$(rel_to_repo "$abs")")
		labels+=("$label")
	fi
}

absolute() {
	case "$1" in
		/*) printf '%s' "$1" ;;
		*) printf '%s/%s' "$PWD" "$1" ;;
	esac
}

args=()
end_of_opts=0
for arg in "$@"; do
	if [ "$end_of_opts" -eq 0 ]; then
		case "$arg" in
			--help|-h)
				usage
				exit 0
				;;
			--)
				end_of_opts=1
				continue
				;;
		esac
	fi
	args+=("$arg")
done

if [ "${#args[@]}" -gt 0 ]; then
	for arg in "${args[@]}"; do
		if [ -f "$arg" ]; then
			if is_php "$arg"; then
				add_target "$(absolute "$arg")" "$arg"
			else
				printf 'check-php: ignorado, não é PHP: %s\n' "$arg" >&2
			fi
		elif [ -d "$arg" ]; then
			while IFS= read -r -d '' found; do
				add_target "$found" "$(rel_to_repo "$found")"
			done < <(find "$arg" -type f -name '*.php' -print0 2>/dev/null | sort -z)
		else
			printf 'check-php: caminho inexistente: %s\n' "$arg" >&2
			missing=1
		fi
	done
else
	for root in "$THEME_DIR" "$MU_DIR"; do
		if [ ! -d "$REPO_ROOT/$root" ]; then
			printf 'check-php: raiz por omissão ausente: %s\n' "$root" >&2
			missing=1
			continue
		fi
		while IFS= read -r -d '' found; do
			add_target "$found" "${found#"$REPO_ROOT"/}"
		done < <(find "$REPO_ROOT/$root" -type f -name '*.php' -print0 2>/dev/null | sort -z)
	done
fi

if [ "${#targets[@]}" -eq 0 ]; then
	if [ "$missing" -eq 1 ]; then
		printf 'check-php: nenhum ficheiro verificável.\n' >&2
		exit 2
	fi
	printf 'check-php: nenhum ficheiro PHP encontrado para verificar.\n'
	exit 0
fi

printf 'check-php: a verificar %d ficheiro(s) PHP\n' "${#targets[@]}"
for label in "${labels[@]}"; do
	printf '  - %s\n' "$label"
done

# ---------------------------------------------------------------------------
# Verificar. Acumular problemas para reportar tudo de uma vez.
# ---------------------------------------------------------------------------
problems=()
inc_seen=0

for i in "${!targets[@]}"; do
	file="${targets[$i]}"
	rel="${rels[$i]}"
	label="${labels[$i]}"

	# 1) sintaxe — `php -l` escreve o parse error no stderr e sai != 0 (255), nunca 1.
	lint_err="$(php -l "$file" 2>&1 >/dev/null)"
	lint_status=$?
	if [ "$lint_status" -ne 0 ]; then
		line="$(printf '%s\n' "$lint_err" | sed -n 's/.* on line \([0-9][0-9]*\).*/\1/p' | head -n 1)"
		detail="$(printf '%s\n' "$lint_err" | grep -v '^[[:space:]]*$' | head -n 1)"
		detail="${detail//"$file"/}"
		detail="$(printf '%s' "$detail" | sed -e 's/ on line [0-9][0-9]*//' -e 's/ *in *$//' -e 's/  */ /g' -e 's/^ *//' -e 's/ *$//')"
		[ -n "$detail" ] || detail="php -l falhou (código $lint_status)"
		if [ -n "$line" ]; then
			problems+=("sintaxe: $label:$line — $detail")
		else
			problems+=("sintaxe: $label — $detail")
		fi
	fi

	# 2) e 3) estruturais — só ficheiros .php sob inc/, medidos a partir da raiz do repositório.
	# Linhas que começam por um marcador de comentário são ignoradas: documentar a regra não é violá-la.
	if is_under_inc "$rel"; then
		inc_seen=$((inc_seen + 1))
		while IFS= read -r hit; do
			[ -n "$hit" ] && problems+=("block markup: $label:${hit%%:*} — <!-- wp: em PHP sob inc/")
		done < <(grep -a -n -F -e '<!-- wp:' -e '<!--wp:' "$file" 2>/dev/null \
			| grep -vE '^[0-9]+:[[:space:]]*(//|#|\*|/\*)')

		while IFS= read -r hit; do
			[ -n "$hit" ] && problems+=("<style>: $label:${hit%%:*} — <style> emitido de PHP sob inc/")
		done < <(grep -a -n -F -- '<style' "$file" 2>/dev/null \
			| grep -vE '^[0-9]+:[[:space:]]*(//|#|\*|/\*)')
	fi
done

printf '\n'
if [ "$inc_seen" -eq 0 ]; then
	printf 'check-php: verificações estruturais não correram — nenhum ficheiro sob inc/ (ainda).\n'
fi

if [ "${#problems[@]}" -gt 0 ]; then
	printf 'check-php: problemas encontrados (%d)\n' "${#problems[@]}"
	for p in "${problems[@]}"; do
		printf '  - %s\n' "$p"
	done
	printf 'check-php: FALHOU — %d problema(s) em %d ficheiro(s) verificado(s).\n' "${#problems[@]}" "${#targets[@]}"
	exit 1
fi

# Caminhos ausentes: o que se verificou passou, mas a verificação está incompleta — não se
# pode dizer "OK" sem ressalva, senão o resumo contradiz o código de saída.
if [ "$missing" -eq 1 ]; then
	printf 'check-php: %d ficheiro(s) verificado(s) sem problemas, mas houve caminhos ausentes (acima).\n' "${#targets[@]}"
	exit 2
fi

printf 'check-php: OK — %d ficheiro(s) verificado(s), sem problemas.\n' "${#targets[@]}"
exit 0
