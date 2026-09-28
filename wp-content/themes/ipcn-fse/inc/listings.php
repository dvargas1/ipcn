<?php
/**
 * IPCN FSE — listagens: shortcodes ipcn_query_posts e ipcn_archive_hero.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Grid de posts por categoria (cards no padrao da home).
 * Uso: [ipcn_query_posts category="noticias" per_page="6"]
 */
add_shortcode(
	'ipcn_query_posts',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'category' => '',
				'per_page' => 6,
			),
			$atts,
			'ipcn_query_posts'
		);

		$paged = max( 1, (int) ( isset( $_GET['paged'] ) ? $_GET['paged'] : get_query_var( 'paged' ) ) );
		if ( $paged < 1 ) {
			$paged = max( 1, (int) get_query_var( 'page' ) );
		}

		$q = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => (int) $atts['per_page'],
				'category_name'       => $atts['category'],
				'ignore_sticky_posts' => true,
				'no_found_rows'       => false,
				'paged'               => $paged,
			)
		);

		if ( ! $q->have_posts() ) {
			return '<p>Nenhum conteudo publicado nesta secao ainda.</p>';
		}

		$out = '<div class="ipcn-grid">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$img = get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'class' => 'ipcn-card-img' ) );
			$out .= '<a class="ipcn-card" href="' . esc_url( get_permalink() ) . '">';
			if ( $img ) {
				$out .= '<span class="ipcn-card-media">' . $img . '</span>';
			} else {
				$out .= '<span class="ipcn-card-media ipcn-card-noimg" aria-hidden="true">IPCN</span>';
			}
			$out .= '<span class="ipcn-card-title">' . esc_html( get_the_title() ) . '</span>';
			$out .= '<span class="ipcn-card-date">' . esc_html( get_the_date() ) . '</span>';
			$out .= '</a>';
		}

		$total = (int) $q->max_num_pages;
		wp_reset_postdata();
		$out .= '</div>';

		if ( $total > 1 ) {
			$base   = rtrim( home_url( strtok( $_SERVER['REQUEST_URI'], '?' ) ), '/' );
			$out   .= '<nav class="ipcn-pagination">';
			for ( $i = 1; $i <= $total; $i++ ) {
				if ( $i === $paged ) {
					$out .= '<span class="ipcn-page current">' . $i . '</span>';
				} else {
					$out .= '<a class="ipcn-page" href="' . esc_url( $base . '/page/' . $i . '/' ) . '">' . $i . '</a>';
				}
			}
			$out .= '</nav>';
		}

		return $out;
	}
);

/**
 * IPCN FSE — hero de arquivo para categorias (Destaques, Diaspora, Colunistas, Notas).
 * Shortcode le a queried category e imprime eyebrow + h1 + descricao no padrao navy das outras paginas.
 * Uso no archive.html: [ipcn_archive_hero]
 */

add_shortcode(
	'ipcn_archive_hero',
	function () {
		$q = get_queried_object();
		if ( ! $q || ! property_exists( $q, 'slug' ) ) {
			return '';
		}

		$titles = array(
			'destaques'  => 'Destaques do IPCN',
			'diaspora'   => 'Diaspora Afroatlantica',
			'colunistas' => 'Colunistas do IPCN',
			'notas'      => 'Notas IPCN',
			'noticias'   => 'Noticias do IPCN',
			'editorial'  => 'Editorial IPCN',
		);
		$descs  = array(
			'destaques'  => 'Acoes, eventos e conquistas do instituto em destaque.',
			'diaspora'   => 'Vozes da diaspora negra: historias, pesquisas e reflexoes.',
			'colunistas' => 'Opiniao e analise de colaboradores do IPCN.',
			'notas'      => 'Comentarios breves sobre atualidade e cultura negra.',
			'noticias'   => 'Noticias e atualidades do Instituto de Pesquisas das Culturas Negras.',
			'editorial'  => 'Conteudo editorial produzido pelo instituto.',
		);

		$slug = $q->slug;
		$name = ( ! empty( $q->name ) ) ? $q->name : ( isset( $titles[ $slug ] ) ? $titles[ $slug ] : ucwords( str_replace( '-', ' ', $slug ) ) );
		$t    = isset( $titles[ $slug ] ) ? $titles[ $slug ] : ( $name ?: 'Conteudo IPCN' );
		$d    = isset( $descs[ $slug ] ) ? $descs[ $slug ] : 'Selecao de conteudo publicado pelo IPCN.';

		ob_start();
		?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"56px","bottom":"48px","left":"20px","right":"20px"}}},"backgroundColor":"navy","textColor":"base","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-base-color has-navy-background-color has-text-color has-background" style="padding-top:56px;padding-bottom:48px;padding-left:20px;padding-right:20px"><!-- wp:group {"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","fontWeight":"600","letterSpacing":"2px","textTransform":"uppercase"}},"textColor":"base"} -->
<p class="has-base-color has-text-color" style="font-size:13px;font-weight:600;letter-spacing:2px;text-transform:uppercase">IPCN &middot; <?php echo esc_html( $name ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"clamp(22px, 4vw, 36px)","fontWeight":"700","lineHeight":"1.15"}},"textColor":"base"} -->
<h1 class="wp-block-heading has-base-color has-text-color" style="font-family:var(--wp--preset--font-family--oswald);font-size:clamp(22px,4vw,36px);font-weight:700;line-height:1.15;color:var(--wp--preset--color--base)"><?php echo esc_html( $t ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}},"textColor":"base"} -->
<p class="has-base-color has-text-color" style="font-size:16px"><?php echo esc_html( $d ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
		<?php
		return ob_get_clean();
	}
);
