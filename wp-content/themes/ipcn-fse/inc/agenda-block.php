<?php
/**
 * IPCN FSE — shortcode ipcn_home_agenda (agenda da home).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IPCN FSE — Agenda da home com filtro de data futura + estado vazio elegante.
 * Uso no front-page.html: [ipcn_home_agenda]
 * Regra: so publicados da categoria agenda-ipcn com post_date >= hoje.
 * (Meta "data_evento" sera respeitada quando a cliente comecar a preencher.)
 */

add_shortcode(
	'ipcn_home_agenda',
	function () {
		$cat = get_category_by_slug( 'agenda-ipcn' );
		if ( ! $cat ) {
			return '';
		}

		$today   = current_time( 'Y-m-d' );
		$network = 'https://www.instagram.com/ipcnbrasil/';

		$query_args = array(
			'posts_per_page'      => 3,
			'category_name'       => 'agenda-ipcn',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'ASC',
		);

		// Estrategia: se existir meta data_evento usa ela; se nao, checa post_date.
		$meta_check = new WP_Query(
			array_merge(
				$query_args,
				array(
					'meta_query' => array(
						array(
							'key'     => 'data_evento',
							'value'   => $today,
							'compare' => '>=',
							'type'    => 'DATE',
						),
					),
				)
			)
		);

		if ( $meta_check->have_posts() ) {
			$q = $meta_check;
		} else {
			$q = new WP_Query(
				array_merge(
					$query_args,
					array(
						'date_query' => array(
							array(
								'after' => $today . ' 00:00:00',
							),
						),
					)
				)
			);
		}

		// Estado vazio elegante.
		if ( ! $q->have_posts() ) {
			return '<div class="wp-block-group" style="border:1px solid var(--wp--preset--color--muted);border-radius:12px;background:#fff;padding:44px 28px">'
				. '<h3 class="wp-block-heading" style="margin:0;text-align:center;font-family:var(--wp--preset--font-family--oswald);font-size:20px;font-weight:600">A agenda está sendo montada</h3>'
				. '<p style="margin:14px 0 0;text-align:center;color:#64748b;font-size:15px">Acompanhe nossas redes sociais para os próximos encontros e atividades.</p>'
				. '<p style="margin:18px 0 0;text-align:center"><a class="wp-element-button" style="display:inline-block;border:1px solid currentColor;border-radius:6px;padding:10px 18px;text-decoration:none" href="' . esc_url( $network ) . '" target="_blank" rel="noopener noreferrer">Ver no Instagram</a></p>'
				. '</div>';
		}

		// Eventos futuros: um cartao por evento da categoria, dentro de `.ipcn-grid` (a mesma
		// grelha que o `ipcn_query_posts` usa). O markup anterior abria um `core/query` sem
		// atributos, que nao herdava este `$q` e servia a query global — o cartao mostrava a
		// pagina actual. O cartao legado `.ipcn-card-v2` mantem-se ate a historia 2.2 o migrar
		// para o pattern `ipcn/card`.
		$items = '';
		while ( $q->have_posts() ) {
			$q->the_post();

			$thumb = get_the_post_thumbnail(
				get_the_ID(),
				'medium_large',
				array( 'style' => 'aspect-ratio:16/9;object-fit:cover;width:100%;border-radius:0' )
			);
			if ( '' === $thumb ) {
				$thumb = '<span aria-hidden="true" style="display:flex;align-items:center;justify-content:center;aspect-ratio:16/9;background:var(--wp--preset--color--navy);color:#fff;font-family:var(--wp--preset--font-family--oswald);font-size:20px;letter-spacing:2px">IPCN</span>';
			}

			$items .= '<div class="wp-block-group ipcn-card-v2">'
				. '<div class="wp-block-post-featured-image">' . $thumb . '</div>'
				. '<div style="padding:14px 18px 20px">'
				. '<div class="wp-block-post-date" style="color:#a85a32;font-size:13px"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date( 'j \d\e M \d\e Y' ) ) . '</time></div>'
				. '<h2 class="wp-block-post-title" style="margin-top:8px;margin-bottom:0;font-family:var(--wp--preset--font-family--oswald);font-size:19px;font-weight:600;line-height:1.35"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>'
				. '<div class="wp-block-post-excerpt" style="font-size:14px"><p class="wp-block-post-excerpt__excerpt">' . esc_html( get_the_excerpt() ) . '</p></div>'
				. '</div>'
				. '</div>';
		}
		wp_reset_postdata();

		return '<div class="ipcn-grid">' . $items . '</div>';
	}
);
