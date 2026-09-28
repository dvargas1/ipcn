#!/usr/bin/env bash
#
# check-php.test.sh — testes do `scripts/check-php.sh`.
#
# Invocação:
#   bash scripts/check-php.test.sh
#
# Sem framework e sem CI, como o próprio script: bash puro e um trap de limpeza.
# Cada caso cria os seus ficheiros numa pasta temporária e verifica o código de saída
# e a mensagem essencial. Sai 0 se todos passarem, 1 se algum falhar.
#
# Estes testes fixam as linhas da matriz de I/O da história 1.1: sem eles, uma
# verificação que deixasse de verificar passaria despercebida — que é precisamente
# o defeito que o script existe para apanhar.

set -u

SUT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/check-php.sh"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

pass=0
fail=0

report() {
	if [ "$1" -eq 0 ]; then
		printf '  ok   %s\n' "$2"
		pass=$((pass + 1))
	else
		printf '  FALHA %s\n' "$2"
		fail=$((fail + 1))
	fi
}

# Corre o script sobre os argumentos dados e compara código de saída e conteúdo.
expect() {
	local desc="$1" want_code="$2" want_text="$3"
	shift 3
	out="$(bash "$SUT" "$@" 2>&1)"
	code=$?
	if [ "$code" -ne "$want_code" ]; then
		printf '  FALHA %s — esperava saída %s, veio %s\n' "$desc" "$want_code" "$code"
		printf '%s\n' "$out" | sed 's/^/        /'
		fail=$((fail + 1))
		return
	fi
	if [ -n "$want_text" ] && ! printf '%s' "$out" | grep -qF -- "$want_text"; then
		printf '  FALHA %s — não encontrei %s na saída\n' "$desc" "$want_text"
		printf '%s\n' "$out" | sed 's/^/        /'
		fail=$((fail + 1))
		return
	fi
	report 0 "$desc"
}

# --- Fixtures -----------------------------------------------------------------
mkdir -p "$TMP/inc" "$TMP/limpo"

printf '<?php\n$x = 1;\n' > "$TMP/inc/ok.php"
printf '<?php\n$a = 1;\n$b = 2\n' > "$TMP/inc/erro.php"
printf '<?php\n$c = "<!-- wp:group -->";\n' > "$TMP/inc/markup.php"
printf '<?php\necho "<style id=\\"x\\">";\n' > "$TMP/inc/estilo-duplo.php"
printf "<?php\necho '<style>';\n" > "$TMP/inc/estilo-simples.php"
printf '<?php\nprintf("%s", "<style>");\n' > "$TMP/inc/estilo-printf.php"
printf '<?php\n$d = 1;\n' > "$TMP/limpo/bom.php"

printf 'check-php.test.sh — a correr\n'

# --- Sintaxe -----------------------------------------------------------------
expect "ficheiro válido sai 0" 0 "sem problemas" "$TMP/limpo/bom.php"
expect "erro de sintaxe sai 1 e aponta a linha" 1 "sintaxe:" "$TMP/inc/erro.php"
expect "caminho inexistente sai 2" 2 "caminho inexistente" "$TMP/nao-existe.php"
printf 'notas\n' > "$TMP/notas.txt"
expect "argumento que não é PHP não é verificado" 0 "nenhum ficheiro PHP encontrado" "$TMP/notas.txt"

# --- Estruturais -------------------------------------------------------------
expect "block markup sob inc/ sai 1" 1 "block markup:" "$TMP/inc/markup.php"
expect "<style id= sob inc/ sai 1" 1 "<style>:" "$TMP/inc/estilo-duplo.php"
expect "echo com aspas simples sob inc/ sai 1" 1 "<style>:" "$TMP/inc/estilo-simples.php"
expect "printf com <style sob inc/ sai 1" 1 "<style>:" "$TMP/inc/estilo-printf.php"
expect "ficheiro sob inc/ limpo sai 0" 0 "sem problemas" "$TMP/inc/ok.php"

# Variante sem espaço no comentário de bloco.
printf '<?php\n$f = "<!--wp:group-->";\n' > "$TMP/inc/markup-sem-espaco.php"
expect "<!--wp: sem espaço também é apanhado" 1 "block markup:" "$TMP/inc/markup-sem-espaco.php"

# Documentar a regra num comentário não é violá-la.
printf '<?php\n// proibido escrever <!-- wp: em strings PHP aqui\n/**\n * Também não uses <style> de PHP.\n */\n$g = 1;\n' > "$TMP/inc/comentado.php"
expect "menção em comentário não é falsa positivo" 0 "sem problemas" "$TMP/inc/comentado.php"

# Caminho ausente junto de ficheiros limpos: o resumo não pode dizer OK sem ressalva.
out="$(bash "$SUT" "$TMP/limpo/bom.php" "$TMP/nao-existe.php" 2>&1)"
code=$?
if [ "$code" -eq 2 ] && printf '%s' "$out" | grep -qF "caminhos ausentes"; then
	report 0 "caminho ausente com ficheiros limpos: ressalva e saída 2"
else
	printf '  FALHA caminho ausente com ficheiros limpos — saiu %s\n' "$code"
	printf '%s\n' "$out" | sed 's/^/        /'
	fail=$((fail + 1))
fi

# ---------------------------------------------------------------------------
# O que o script NÃO deve fazer: policiar um `inc/` que não está no repositório
# não é possível de distinguir aqui sem a raiz — o que se fixa é que um ficheiro
# fora de `inc/` não é policiado.
# ---------------------------------------------------------------------------
mkdir -p "$TMP/fora"
printf '<?php\n$e = "<!-- wp:x -->";\n' > "$TMP/fora/sem-inc.php"
expect "block markup fora de inc/ não é policiado" 0 "sem problemas" "$TMP/fora/sem-inc.php"

# --- Interface --------------------------------------------------------------
out="$(bash "$SUT" --help 2>&1)"
if printf '%s' "$out" | grep -qF "Invocação"; then
	report 0 "--help mostra a ajuda e sai 0"
else
	report 1 "--help mostra a ajuda e sai 0"
fi

# ---------------------------------------------------------------------------
printf '\n%d passaram, %d falharam\n' "$pass" "$fail"
[ "$fail" -eq 0 ] || exit 1
exit 0
