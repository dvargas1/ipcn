<?php
/**
 * IPCN FSE — Apoia-se: o QR de PIX apresentado pelo tema (FR-10).
 *
 * Preocupacao propria (AD-4): o tema passa a ser dono da apresentacao do QR, a partir de um
 * asset do tema — como o hero (`inc/setup.php`, `assets/hero-bg.jpg`) — e nunca de uma
 * imagem do conteudo da base de dados. O estado e decidido pela presenca do asset e nao por
 * uma data: sem QR que o tema possa garantir, a superficie mostra o estado honesto com o
 * contacto, em vez de um codigo morto (EXPERIENCE.md, *QR de PIX desatualizado*). Quando a
 * contratante entregar o QR estatico, basta guarda-lo no asset do tema.
 *
 * So HTML com as classes do tema (AD-1, excepcao 2): nenhum comentario de bloco e nenhum
 * `<style>` (o desenho vive no `style.css`, AD-5).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Caminho relativo do asset do QR de PIX no tema.
 *
 * E o unico literal do caminho: resolvido por `get_theme_file_path()`/`get_theme_file_uri()`
 * para funcionar no tema filho e no pai.
 *
 * @return string
 */
function ipcn_pix_qr_rel() {
	return 'assets/qrcode-pix.jpg';
}

/**
 * Diz se o tema tem um QR de PIX que possa garantir.
 *
 * O estado decide-se pela presenca do asset; o filtro `ipcn_pix_qr_available` permite
 * fixa-lo (por exemplo, esconder um QR que se saiba vencido) sem tocar no ficheiro.
 *
 * @return bool
 */
function ipcn_pix_qr_available() {
	$path      = get_theme_file_path( ipcn_pix_qr_rel() );
	$available = ( is_string( $path ) && '' !== $path && file_exists( $path ) );

	/**
	 * Filtra se o QR de PIX do tema pode ser mostrado.
	 *
	 * @param bool $available Verdadeiro quando o asset do QR existe no tema.
	 */
	return (bool) apply_filters( 'ipcn_pix_qr_available', $available );
}

/**
 * Shortcode `[ipcn_pix_qr]`: imprime a figura do QR ou o estado «em atualizacao».
 *
 * Com QR: uma figura com a imagem, o texto alternativo, largura fixa de 220px e a legenda
 * «Escaneie para apoiar». Sem QR: a copy do estado honesto com a ligacao ao contacto e
 * nenhum `<img>` — nunca mostrar um codigo vencido nem um placeholder grafico.
 */
add_shortcode(
	'ipcn_pix_qr',
	function () {
		if ( ipcn_pix_qr_available() ) {
			return '<figure class="ipcn-pix-qr">'
				. '<img class="ipcn-pix-qr-img" src="' . esc_url( get_theme_file_uri( ipcn_pix_qr_rel() ) ) . '" alt="QR code para apoiar o IPCN por PIX" width="220" height="220" loading="lazy" decoding="async">'
				. '<figcaption class="ipcn-pix-qr-legenda">Escaneie para apoiar</figcaption>'
				. '</figure>';
		}

		return '<div class="ipcn-pix-atualizacao">'
			. '<p class="ipcn-pix-atualizacao-texto">Código PIX em atualização. Fale conosco para apoiar agora.</p>'
			. '<p class="ipcn-pix-atualizacao-acao"><a class="ipcn-pix-atualizacao-link" href="' . esc_url( home_url( '/fale-conosco/' ) ) . '">Fale conosco</a></p>'
			. '</div>';
	}
);
