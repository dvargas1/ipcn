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
			ob_start();
			?>
<!-- wp:group {"style":{"border":{"radius":"12px","width":"1px"},"spacing":{"padding":{"top":"44px","right":"28px","bottom":"44px","left":"28px"}}},"borderColor":"muted","backgroundColor":"base","layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group has-border-color has-muted-border-color has-base-background-color has-background" style="border-radius:12px;border-width:1px;padding-top:44px;padding-right:28px;padding-bottom:44px;padding-left:28px">
  <!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"20px","fontWeight":"600"}}} -->
  <h3 class="wp-block-heading has-text-align-center" style="font-family:var(--wp--preset--font-family--oswald);font-size:20px;font-weight:600">A agenda esta sendo montada</h3>
  <!-- /wp:heading -->
  <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"15px"},"color":{"text":"#64748b"}}} -->
  <p class="has-text-align-center" style="color:#64748b;font-size:15px">Acompanhe nossas redes sociais para os proximos encontros e atividades.</p>
  <!-- /wp:paragraph -->
  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"18px"}}}} -->
  <div class="wp-block-buttons" style="margin-top:18px">
    <!-- wp:button {"className":"is-style-outline"} -->
    <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $network ); ?>" target="_blank" rel="noopener noreferrer">Ver no Instagram</a></div>
    <!-- /wp:button -->
  </div>
  <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
			<?php
			return ob_get_clean();
		}

		// Agenda com data futura: renderiza cards.
		ob_start();
		?>
<!-- wp:query -->
<div class="wp-block-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
		<?php
		while ( $q->have_posts() ) :
			$q->the_post();
			$thumb = get_the_post_thumbnail( get_the_ID(), 'medium_large' );
			?>
<!-- wp:group {"className":"ipcn-card-v2"} -->
<div class="wp-block-group ipcn-card-v2">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":{"topLeft":"12px","topRight":"12px","bottomLeft":"0px","bottomRight":"0px"}}}} /-->
<!-- wp:post-date {"style":{"typography":{"fontSize":"13px"},"color":{"text":"#a85a32"}},"format":"j \\d\\e M \\d\\e Y"} /-->
<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"19px","fontWeight":"600","lineHeight":"1.35"},"spacing":{"margin":{"top":"8px"}}}} /-->
<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"style":{"typography":{"fontSize":"14px"}}} /-->
</div>
<!-- /wp:group -->
			<?php
		endwhile;
		wp_reset_postdata();
		?>
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
		<?php
		return ob_get_clean();
	}
);
