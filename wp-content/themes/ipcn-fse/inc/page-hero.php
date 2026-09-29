<?php
/**
 * IPCN FSE — cabecalho de pagina: resolve o texto de cada superficie de entrada
 * e serve-o pelos tres shortcodes do template part `page-hero`.
 *
 * O ficheiro `parts/page-hero.html` e a forma (markup unico); aqui vive o dado. Um
 * template part nao recebe atributos que cheguem aos seus blocos, pelo que o texto
 * de cada superficie (titulo da pagina, nome e descricao do termo, o hub, o Acervo,
 * a 404) e resolvido a partir do objecto consultado. Nenhum markup de bloco nem
 * `<style>` sai daqui (AD-1, AD-5) e todo o texto sai escapado pelos shortcodes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Diz se um texto nao tem caracteres visiveis.
 *
 * `trim()` nao tira o espaco inquebravel (U+00A0) que os editores colam, e um titulo ou
 * descricao so com ele imprimiria um `h1` invisivel em vez do fallback.
 *
 * @param string $text Texto a testar.
 * @return bool Verdadeiro quando so ha espacos (ou nada).
 */
function ipcn_page_hero_is_blank( $text ) {
	return '' === preg_replace( '/[\s\x{00A0}]+/u', '', (string) $text );
}

/**
 * Texto do cabecalho de pagina: eyebrow, titulo e linha de apoio da superficie actual.
 * Resolvido por esta ordem: 404 -> Acervo (arquivo do CPT) -> hub (`/noticias/`) ->
 * termo -> autor -> pagina -> restantes arquivos (data e post type), com o titulo do
 * nucleo sem o prefixo.
 *
 * O eyebrow e sempre `IPCN · <superficie>`. A linha de apoio vem da descricao do termo
 * quando existe; sem ela e `''` e a faixa encolhe por CSS (`.ipcn-page-hero-apoio:empty`).
 * A guarda do nome (slug ou `Conteudo IPCN`) impede um `h1` vazio.
 *
 * @return array{eyebrow:string,title:string,apoio:string}
 */
function ipcn_page_hero_data() {
	static $data = null;

	if ( null !== $data ) {
		return $data;
	}

	$eyebrow = '';
	$title   = '';
	$apoio   = '';

	if ( is_404() ) {
		$eyebrow = 'IPCN · Erro 404';
		$title   = 'Página não encontrada';
	} elseif ( is_post_type_archive( 'acervo_ipcn' ) ) {
		$eyebrow = 'IPCN · Acervo';
		$title   = 'Memória e história do IPCN';
		$apoio   = 'Depoimentos, fotos e documentos preservados pelo instituto.';
	} elseif ( is_page( 'noticias' ) ) {
		// Copy do hub preservada verbatim (`templates/page-noticias.html`).
		$eyebrow = 'IPCN · Notícias';
		$title   = 'Notícias do IPCN';
		$apoio   = 'Notícias e atualidades do Instituto de Pesquisas das Culturas Negras. As seções editoriais do instituto abrem a partir daqui.';
	} else {
		$queried = get_queried_object();

		if ( $queried instanceof WP_Term ) {
			$slug = (string) $queried->slug;

			// Fallbacks que nunca imprimem vazio (matriz de I/O): sem nome, deriva-se do
			// slug; sem slug, o nome institucional — a guarda que impede um `h1` vazio.
			$name = ipcn_page_hero_is_blank( $queried->name ) ? ucwords( str_replace( '-', ' ', $slug ) ) : (string) $queried->name;
			$name = ipcn_page_hero_is_blank( $name ) ? 'Conteúdo IPCN' : $name;

			$eyebrow = 'IPCN · ' . $name;
			$title   = $name;
			// A descricao alimenta o apoio quando existe; sem ela nao ha linha de apoio.
			$description = trim( wp_strip_all_tags( (string) $queried->description ) );
			$apoio       = ipcn_page_hero_is_blank( $description ) ? '' : $description;
		} elseif ( $queried instanceof WP_User ) {
			$name    = trim( (string) $queried->display_name );
			$name    = ipcn_page_hero_is_blank( $name ) ? (string) $queried->user_login : $name;
			$eyebrow = 'IPCN · ' . $name;
			$title   = $name;
		} elseif ( is_page() ) {
			// Sem titulo visivel, o `core/post-title` nao imprimia `h1` nenhum; aqui a
			// guarda institucional evita trocar isso por um `h1` sem texto.
			$name    = ipcn_page_hero_is_blank( get_the_title() ) ? 'Conteúdo IPCN' : (string) get_the_title();
			$eyebrow = 'IPCN · ' . $name;
			$title   = $name;
		} else {
			// Arquivos de data e de post type: titulo do nucleo sem o prefixo ("Mês:", "Ano:", ...).
			$archive = (string) get_the_archive_title();
			$prefix  = get_the_archive_title_prefix();
			if ( is_string( $prefix ) && '' !== $prefix && 0 === strpos( $archive, $prefix ) ) {
				$archive = trim( substr( $archive, strlen( $prefix ) ) );
			}
			$archive = wp_strip_all_tags( $archive );

			// Ultimo recurso: um titulo de arquivo vazio deixaria um `h1` sem texto.
			if ( ipcn_page_hero_is_blank( $archive ) ) {
				$archive = wp_strip_all_tags( (string) get_bloginfo( 'name' ) );
			}

			$eyebrow = 'IPCN · ' . $archive;
			$title   = $archive;
		}
	}

	$data = array(
		'eyebrow' => (string) $eyebrow,
		'title'   => (string) $title,
		'apoio'   => (string) $apoio,
	);

	/**
	 * Filtra o texto do cabecalho de pagina.
	 *
	 * @param array $data Array com `eyebrow`, `title` e `apoio`.
	 */
	$data = (array) apply_filters( 'ipcn_page_hero_data', $data );

	return $data;
}

/**
 * Devolve um campo do texto do cabecalho, escapado.
 *
 * @param string $field Campo de `ipcn_page_hero_data()`.
 * @return string Texto pronto a imprimir.
 */
function ipcn_page_hero_field( $field ) {
	$data = ipcn_page_hero_data();

	return esc_html( isset( $data[ $field ] ) ? (string) $data[ $field ] : '' );
}

/*
 * Os tres shortcodes do template part `parts/page-hero.html`. Vivem aqui, e nao no
 * markup, para o mesmo ficheiro servir todas as superficies de entrada.
 */
add_shortcode(
	'ipcn_page_hero_eyebrow',
	function () {
		return ipcn_page_hero_field( 'eyebrow' );
	}
);

add_shortcode(
	'ipcn_page_hero_title',
	function () {
		return ipcn_page_hero_field( 'title' );
	}
);

add_shortcode(
	'ipcn_page_hero_apoio',
	function () {
		return ipcn_page_hero_field( 'apoio' );
	}
);
