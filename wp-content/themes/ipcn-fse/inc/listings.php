<?php
/**
 * IPCN FSE — listagens: filtro de categoria do `core/query` e shortcodes
 * ipcn_query_posts, ipcn_archive_hero, ipcn_tema_filter e ipcn_archive_vazio.
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
			return '<p>Nenhum conteúdo publicado nesta seção ainda.</p>';
		}

		$registry     = WP_Block_Patterns_Registry::get_instance();
		$card_pattern = $registry->is_registered( 'ipcn/card' ) ? $registry->get_registered( 'ipcn/card' ) : null;
		$card_markup  = ( is_array( $card_pattern ) && isset( $card_pattern['content'] ) ) ? $card_pattern['content'] : '';

		// Sem pattern nao ha cartao: sem esta guarda sairia uma grelha vazia, com paginacao,
		// e nada o diria (o modo de falha silenciosa do AD-14).
		if ( '' === $card_markup ) {
			_doing_it_wrong( 'ipcn_query_posts', 'O pattern ipcn/card não está registrado; o cartão não pode ser renderizado.', '1.3.0' );
			return '<p>Nenhum conteúdo publicado nesta seção ainda.</p>';
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
		$name        = ( '' !== trim( $name ) ) ? $name : 'Conteúdo IPCN';
		$description = wp_strip_all_tags( (string) $q->description );
		$description = ( '' !== trim( $description ) ) ? $description : 'Seleção de conteúdo publicado pelo IPCN.';

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

/**
 * IPCN FSE — filtro de Temas do Acervo (componente `theme-filter` do DESIGN).
 *
 * Imprime as pilulas dos Temas como ligacoes reais, por slug, para `/temas/<slug>/` — o
 * endereco muda na barra e o botao de voltar funciona, porque o filtro e navegacao e nao
 * estado escondido. Uma ultima liberdade do AD-1 (excepcao 2): HTML simples com as classes do
 * tema, sem comentarios de bloco, para nao crescer a contagem do `check-php.sh` (AD-5).
 *
 * So corre nas superficies do Acervo: `/acervo/` (arquivo do CPT) e `/temas/<slug>/` (arquivo
 * do termo, servido deliberadamente por `templates/archive.html`, AD-8). Fora delas devolve
 * `''`, porque o mesmo `archive.html` serve as Seccoes (categorias) e um filtro do Acervo ali
 * seria um controlo novo numa superficie de Noticias. Sem termos devolve `''` — nada de um
 * `nav` vazio (matriz de I/O).
 *
 * `hide_empty => false` de proposito: um Tema sem pecas continua alcancavel pelo filtro e
 * explica-se no vazio, em vez de desaparecer. A ordem e por nome, ascendente (nenhum documento
 * fixa ordem). O termo aberto leva `aria-current="page"` — a pilula preenchida do componente.
 *
 * Uso: [ipcn_tema_filter]
 */
add_shortcode(
	'ipcn_tema_filter',
	function () {
		if ( ! is_tax( 'tema_acervo' ) && ! is_post_type_archive( 'acervo_ipcn' ) ) {
			return '';
		}

		$terms = get_terms(
			'tema_acervo',
			array(
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		// AD-7: quem decide o endereco e `get_term_link()`, do slug — nunca um id de termo. O id
		// so serve para reconhecer, em memoria, qual das pilulas e a do termo consultado.
		$queried = get_queried_object();
		$current = ( $queried instanceof WP_Term ) ? (int) $queried->term_id : 0;

		$items = '';
		foreach ( $terms as $term ) {
			if ( ! ( $term instanceof WP_Term ) ) {
				continue;
			}

			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}

			$is_current = ( (int) $term->term_id === $current );
			$items     .= '<li><a href="' . esc_url( $link ) . '"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $term->name ) . '</a></li>';
		}

		// Sem nenhuma ligacao valida nao ha filtro: melhor nada do que um `nav` sem itens.
		if ( '' === $items ) {
			return '';
		}

		return '<nav class="ipcn-tema-filter" aria-label="Temas do Acervo"><ul class="ipcn-tema-filter-links">' . $items . '</ul></nav>';
	}
);

/**
 * IPCN FSE — frase do vazio do arquivo, escolhida pelo objecto consultado.
 *
 * O `core/query-no-results` e estatico e o `templates/archive.html` serve tanto as Seccoes
 * (categorias) como os Temas — e ainda os archives de tag, autor e data, que caem no mesmo
 * template de recurso. Em `/temas/<slug>/` sem pecas explica que o Tema ainda nao tem pecas e
 * oferece o Acervo inteiro; numa Seccao sem publicacoes explica a ausencia e oferece o hub das
 * Seccoes (a historia 1.10 decidiu as duas copies). Nos restantes archives devolve a frase de
 * sempre (acentuada na 1.11): nao sao superficies nomeadas pelo par de UX, e uma segunda mensagem ao lado
 * da do Tema seria o defeito obvio.
 *
 * O Tema sem pecas nao vira 404: `WP::handle_404()` nao marca 404 quando `is_tax()` e ha
 * objecto consultado, logo o template corre e o estado vazio e conteudo do template.
 *
 * Uso (dentro do `core/query-no-results` de `templates/archive.html`): [ipcn_archive_vazio]
 */
add_shortcode(
	'ipcn_archive_vazio',
	function () {
		if ( ! is_tax( 'tema_acervo' ) ) {
			// So as Seccoes (categorias) levam a copy nomeada e a oferta das restantes Seccoes:
			// os archives de tag, autor e data caem neste mesmo template e continuam com a
			// frase de sempre (acentuada na 1.11), sem a copy nomeada.
			if ( ! is_category() ) {
				return '<p>Ainda não há posts nesta seção.</p>';
			}

			$out = '<p>Ainda não há publicações nesta Seção.';

			// O caminho de saida e o hub das Secoes (`/noticias/`), resolvido por slug — o
			// endereco e do sistema, nunca um caminho escrito a mao (AD-7). O guard e o mesmo
			// do ramo do Tema: sem a pagina, fica so a explicacao, em vez de uma ligacao morta.
			$hub = get_page_by_path( 'noticias' );
			if ( $hub instanceof WP_Post ) {
				$link = get_permalink( $hub );
				if ( is_string( $link ) && '' !== $link ) {
					$out .= ' <a href="' . esc_url( $link ) . '">Ver as Seções</a>';
				}
			}

			return $out . '</p>';
		}

		$out = '<p>Ainda não há peças publicadas neste Tema.';

		// O caminho de volta e o arquivo do CPT, pelo mesmo motivo do filtro: o endereco e do
		// sistema, nao um caminho escrito a mao que um dia muda de sitio.
		$acervo = get_post_type_archive_link( 'acervo_ipcn' );
		if ( is_string( $acervo ) && '' !== $acervo ) {
			$out .= ' <a href="' . esc_url( $acervo ) . '">Ver o Acervo inteiro</a>';
		}

		return $out . '</p>';
	}
);
