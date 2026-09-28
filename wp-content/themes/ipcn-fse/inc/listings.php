<?php
/**
 * IPCN FSE — listagens: filtro de categoria do `core/query` e shortcodes
 * ipcn_query_posts e ipcn_archive_hero.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * IPCN FSE — filtro de categoria por slug para o `core/query` (AD-7).
 *
 * O nucleo nao le o slug de categoria de um bloco `core/query`: a funcao que constroi os
 * argumentos do `WP_Query` copia `postType`, `sticky`, `exclude`, `perPage`, `offset`,
 * `categoryIds`, `tagIds`, `taxQuery`, `order`, `orderBy`, `author`, `search` e `parents`
 * — nunca o `categoryName`. Sem este filtro, um bloco com `categoryName` corre sem filtro
 * de categoria (foi o que deixou a pauta de Agenda passar para a listagem de Noticias).
 * Aqui o slug declarado no bloco mapeia-se para `category_name`, para o markup continuar
 * declarativo por slug e os enderecos nunca dependerem de um id de termo (AD-7). O filtro
 * so actua quando o atributo esta presente: um bloco sem ele fica intacto.
 *
 * O `$block` e a instancia de `core/post-template` (ou do `core/query-pagination`), que
 * recebe o `query` do `core/query` pelo contexto — e onde o `categoryName` sobrevive.
 */
add_filter(
	'query_loop_block_query_vars',
	function ( $query, $block ) {
		if ( ! ( $block instanceof WP_Block ) || ! isset( $block->context['query']['categoryName'] ) ) {
			return $query;
		}

		$category_name = $block->context['query']['categoryName'];
		if ( ! is_string( $category_name ) ) {
			return $query;
		}

		$category_name = trim( $category_name );
		if ( '' !== $category_name ) {
			$query['category_name'] = $category_name;
		}

		return $query;
	},
	10,
	2
);

/**
 * Grid de posts por categoria, com o cartao do pattern registado.
 *
 * Wrapper fino: o markup do cartao vive so em `patterns/` (pattern `ipcn/card`). Este
 * shortcode le o `content` desse pattern no `WP_Block_Patterns_Registry` e entrega-o ao
 * API de blocos com `do_blocks()` — o que a excepcao 1 do AD-1 autoriza. Nenhum markup de
 * cartao e escrito a mao aqui. O `WP_Query` legado e a paginacao manual ficam como excepcao
 * tolerada (AD-2); nenhuma superficie nova nasce assim.
 *
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

		$registry     = WP_Block_Patterns_Registry::get_instance();
		$card_pattern = $registry->is_registered( 'ipcn/card' ) ? $registry->get_registered( 'ipcn/card' ) : null;
		$card_markup  = ( is_array( $card_pattern ) && isset( $card_pattern['content'] ) ) ? $card_pattern['content'] : '';

		// Sem pattern nao ha cartao: sem esta guarda sairia uma grelha vazia, com paginacao,
		// e nada o diria (o modo de falha silenciosa do AD-14).
		if ( '' === $card_markup ) {
			_doing_it_wrong( 'ipcn_query_posts', 'O pattern ipcn/card nao esta registado; o cartao nao pode ser renderizado.', '1.3.0' );
			return '<p>Nenhum conteudo publicado nesta secao ainda.</p>';
		}

		$out = '<div class="ipcn-grid">';
		while ( $q->have_posts() ) {
			$q->the_post();

			// Sem `postId` no contexto, o `core/post-title`, o `post-date`, o `post-terms` e o
			// `post-featured-image` devolvem string vazia (o modo de falha silenciosa do AD-14).
			// Quem o injecta e o `core/post-template`, pelo filtro `render_block_context`; aqui,
			// fora dele, faz-se o mesmo a volta do `do_blocks()`. O `the_post()` sozinho nao chega.
			$post_id   = get_the_ID();
			$post_type = get_post_type( $post_id );
			$context   = static function ( $block_context ) use ( $post_id, $post_type ) {
				$block_context['postType'] = $post_type;
				$block_context['postId']   = $post_id;
				return $block_context;
			};

			add_filter( 'render_block_context', $context, 1 );
			$out .= do_blocks( $card_markup );
			remove_filter( 'render_block_context', $context, 1 );
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
 * IPCN FSE — hero de arquivo por termo (Seccoes: Destaques, Diaspora, Colunistas, Notas...).
 * Shortcode le o termo consultado e imprime eyebrow + h1 + descricao no padrao navy das outras paginas.
 * AD-8: o nome e a descricao vem do termo (a descricao vive na base de dados), nao de um mapa fixo em PHP.
 * Uso no archive.html: [ipcn_archive_hero]
 */

add_shortcode(
	'ipcn_archive_hero',
	function () {
		$q = get_queried_object();

		// AD-8: o hero le o nome e a descricao do termo, nao de um mapa fixo em PHP. A guarda
		// e `WP_Term` para um arquivo de post type (WP_Post_Type tem `name`) nao imprimir o
		// nome de maquina como h1.
		if ( ! ( $q instanceof WP_Term ) ) {
			return '';
		}

		$slug = (string) $q->slug;

		// Fallbacks que nunca imprimem vazio (matriz de I/O): sem nome, deriva-se do slug;
		// sem descricao, fica a frase institucional. `trim` para um nome/descricao so com
		// espacos contar como ausente, e `wp_strip_all_tags` para uma descricao com markup
		// nao sair com etiquetas literais.
		$name        = ( '' !== trim( (string) $q->name ) ) ? (string) $q->name : ucwords( str_replace( '-', ' ', $slug ) );
		$name        = ( '' !== trim( $name ) ) ? $name : 'Conteudo IPCN';
		$description = wp_strip_all_tags( (string) $q->description );
		$description = ( '' !== trim( $description ) ) ? $description : 'Selecao de conteudo publicado pelo IPCN.';

		ob_start();
		?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"56px","bottom":"48px","left":"20px","right":"20px"}}},"backgroundColor":"navy","textColor":"base","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-base-color has-navy-background-color has-text-color has-background" style="padding-top:56px;padding-bottom:48px;padding-left:20px;padding-right:20px"><!-- wp:group {"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","fontWeight":"600","letterSpacing":"2px","textTransform":"uppercase"}},"textColor":"base"} -->
<p class="has-base-color has-text-color" style="font-size:13px;font-weight:600;letter-spacing:2px;text-transform:uppercase">IPCN &middot; <?php echo esc_html( $name ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"clamp(22px, 4vw, 36px)","fontWeight":"700","lineHeight":"1.15"}},"textColor":"base"} -->
<h1 class="wp-block-heading has-base-color has-text-color" style="font-family:var(--wp--preset--font-family--oswald);font-size:clamp(22px,4vw,36px);font-weight:700;line-height:1.15;color:var(--wp--preset--color--base)"><?php echo esc_html( $name ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}},"textColor":"base"} -->
<p class="has-base-color has-text-color" style="font-size:16px"><?php echo esc_html( $description ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
		<?php
		return ob_get_clean();
	}
);
