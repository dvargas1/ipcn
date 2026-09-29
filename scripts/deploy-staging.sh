#!/usr/bin/env bash
#
# deploy-staging.sh — entrega o tema ipcn-fse ao stagingredesign (ambiente de teste).
#
# Invocação:
#   bash scripts/deploy-staging.sh                    # usa os valores por omissão
#   REMOTE_WP_ROOT='$HOME/outro/site' bash scripts/deploy-staging.sh
#   bash scripts/deploy-staging.sh --help
#
# Passos, na ordem do README:
#   1. tar -czf do tema, com caminhos `wp-content/themes/ipcn-fse/...`;
#   2. scp para /tmp do servidor;
#   3. tar -xzf --strip-components=3 dentro do tema remoto;
#   4. limpa a cache de patterns do tema — senão as listagens ficam sem cartões até
#      o transient expirar (~30 min), porque a Version do tema não muda no deploy;
#   5. purge da página (`wp litespeed-purge all`; o HCDN é teimoso).
#
# Configuração por ambiente. Os valores por omissão foram verificados no servidor:
#   REMOTE_WP_ROOT  raiz do WordPress remoto. Por omissão
#                   $HOME/domains/ipcnbrasil.org/public_html/stagingredesign.
#                   O tema é <raiz>/wp-content/themes/ipcn-fse, como no repositório.
#   SSH_TARGET      alvo de ssh/scp. Por omissão `ipcn` (alias de ~/.ssh/config); sem
#                   alias: SSH_TARGET=u654777386@147.93.38.215 SSH_PORT=65002.
#   SSH_PORT        porta, quando o alvo não é um alias que já a defina.
#   SSH_KEY         IdentityFile a usar (ex.: ~/.ssh/ipcn_staging_ed25519).
#
# A purga é `wp litespeed-purge all` (WP-CLI + plugin litespeed-cache). Não existe
# nenhum binário `litespeed-purge` no servidor — o README dizia-o e estava errado.
#
# Código de saída:
#   0 — deploy feito;
#   1 — um passo falhou (o deploy pode ter ficado a meio);
#   2 — erro de ambiente ou de configuração.
#
# Não mexe em mu-plugins, não commita e nunca toca na produção.
#

set -euo pipefail

usage() {
	sed -n '3,33p' "$0" | sed 's/^# \{0,1\}//'
}

die() { printf 'deploy-staging: %s\n' "$1" >&2; exit "${2:-1}"; }

case "${1:-}" in
	--help|-h)
		usage
		exit 0
		;;
	-*)
		die "opção desconhecida: $1 (ver --help)." 2
		;;
esac

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LOCAL_THEME_REL="wp-content/themes/ipcn-fse"

for tool in ssh scp tar mktemp; do
	command -v "$tool" >/dev/null 2>&1 || die "não encontrei o \`$tool\` no PATH." 2
done

[ -d "$REPO_ROOT/$LOCAL_THEME_REL" ] || die "não encontro o tema local: $LOCAL_THEME_REL" 2

SSH_TARGET="${SSH_TARGET:-ipcn}"

# O caminho remoto vai entre aspas duplas no comando remoto, onde `~` não expande mas
# `$HOME` expande. Normalizamos o `~` e restringimos o resto para não quebrar o quoting.
REMOTE_WP_ROOT="${REMOTE_WP_ROOT:-\$HOME/domains/ipcnbrasil.org/public_html/stagingredesign}"
case "$REMOTE_WP_ROOT" in
	'~') REMOTE_WP_ROOT='$HOME' ;;
	'~/'*) REMOTE_WP_ROOT='$HOME/'"${REMOTE_WP_ROOT#'~/'}" ;;
esac
root_rest=''
case "$REMOTE_WP_ROOT" in
	'$HOME') root_rest='' ;;
	'$HOME/'*) root_rest="${REMOTE_WP_ROOT#'$HOME/'}" ;;
	/*) root_rest="${REMOTE_WP_ROOT#/}" ;;
	*) die "REMOTE_WP_ROOT tem de ser absoluto ou começar por \$HOME/ (ex.: \$HOME/domains/...)." 2 ;;
esac
# Testar só o resto, sem `$` no padrão: num case o bash expande `$-` dentro do conjunto
# e o hífen de `wp-content` deixaria de ser aceite.
case "$root_rest" in
	*[!A-Za-z0-9._/-]*)
		die "REMOTE_WP_ROOT só aceita [A-Za-z0-9._/-] no caminho." 2
		;;
esac

REMOTE_THEME="$REMOTE_WP_ROOT/$LOCAL_THEME_REL"

ssh_flags=()
scp_flags=()
key_flags=()
if [ -n "${SSH_PORT:-}" ]; then
	ssh_flags+=(-p "$SSH_PORT")
	scp_flags+=(-P "$SSH_PORT")
fi
if [ -n "${SSH_KEY:-}" ]; then
	key_flags+=(-i "$SSH_KEY")
fi

tmp="$(mktemp)"
trap 'rm -f "$tmp"' EXIT
remote_tmp="/tmp/ipcn-fse-$$.tgz"

printf 'deploy-staging: a empacotar %s\n' "$LOCAL_THEME_REL"
tar -czf "$tmp" -C "$REPO_ROOT" "$LOCAL_THEME_REL" || die "falhou o tar do tema." 1

printf 'deploy-staging: a enviar para %s\n' "$SSH_TARGET"
scp ${scp_flags[@]+"${scp_flags[@]}"} ${key_flags[@]+"${key_flags[@]}"} "$tmp" "$SSH_TARGET:$remote_tmp" \
	|| die "falhou o scp para $SSH_TARGET." 1

printf 'deploy-staging: a extrair em %s\n' "$REMOTE_THEME"
ssh ${ssh_flags[@]+"${ssh_flags[@]}"} ${key_flags[@]+"${key_flags[@]}"} "$SSH_TARGET" \
	"mkdir -p \"$REMOTE_THEME\" && tar -xzf \"$remote_tmp\" --strip-components=3 -C \"$REMOTE_THEME\" && rm -f \"$remote_tmp\"" \
	|| die "falhou a extracção em $REMOTE_THEME." 1

printf 'deploy-staging: a limpar a cache de patterns\n'
ssh ${ssh_flags[@]+"${ssh_flags[@]}"} ${key_flags[@]+"${key_flags[@]}"} "$SSH_TARGET" \
	"wp --path=\"$REMOTE_WP_ROOT\" eval 'wp_get_theme()->delete_pattern_cache();'" \
	|| die "não consegui limpar a cache de patterns — as listagens podem ficar sem cartões até 30 min." 1

printf 'deploy-staging: a purgar a cache\n'
ssh ${ssh_flags[@]+"${ssh_flags[@]}"} ${key_flags[@]+"${key_flags[@]}"} "$SSH_TARGET" \
	"wp --path=\"$REMOTE_WP_ROOT\" litespeed-purge all" \
	|| die "deploy feito, mas a purga falhou — o HCDN pode servir HTML velho." 1

printf 'deploy-staging: OK — tema entregue em %s\n' "$REMOTE_THEME"
