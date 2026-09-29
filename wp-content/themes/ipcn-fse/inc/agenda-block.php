<?php
/**
 * IPCN FSE — a Agenda: o bloco dinamico `ipcn/agenda` e o redirect do archive.
 *
 * A Agenda e a unica superficie do tema que corre a sua propria consulta (AD-9): a janela
 * "a partir de hoje, por ordem crescente de `data_evento`" nao se exprime num `core/query`,
 * que nao tem `meta_query`. O bloco tem um so atributo, `limite` (inteiro, por omissao `3`),
 * e renderiza cada encontro pelo pattern registado `ipcn/card` — que, fora de um
 * `core/post-template`, e o unico caminho de render do cartao (AD-14). A Home chama-o com
 * `limite: 3`; a pagina da Agenda, com `limite: -1`.
 *
 * Este ficheiro substitui o remedio anterior da Agenda: a heuristica pela data de publicacao, o
 * cartao legado escrito a mao e o vazio em atributos `style` saem com ele. Nenhum comentario de
 * bloco e nenhum elemento de estilo saem daqui (AD-1, AD-5): o desenho vive no `style.css`.
 *
 * O archive da categoria `agenda-ipcn` deixa de ser superficie publica (AD-8): responde 301
 * para a pagina da Agenda em vez de listar 54 publicacoes, quase todas passadas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A consulta do bloco: publicacoes da categoria `agenda-ipcn` por slug, com `data_evento`
 * preenchida e igual ou posterior a hoje, por ordem crescente de `data_evento`.
 *
 * Sem alternativa pela data de publicacao: um encontro sem `data_evento` nao conta como proximo
 * (cai no estado vazio) em vez de aparecer com uma data que nao e a dele. A categoria vai
 * por `category_name` — nunca por `term_id`, que muda entre ambientes (AD-7). A clausula do
 * `meta_query` e nomeada para o `orderby` a poder usar pelo nome.
 *
 * @param int $limite Quantos encontros devolver; `-1` devolve todos os que estao por vir.
 * @return WP_Query
 */
function ipcn_agenda_query( $limite ) {
	return new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'category_name'       => 'agenda-ipcn',
			'posts_per_page'      => $limite,
			'ignore_sticky_posts' => true,
			'meta_query'          => array(
				'data_evento' => array(
					'key'     => 'data_evento',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '>=',
					'type'    => 'DATE',
				),
			),
			'orderby'             => array( 'data_evento' => 'ASC' ),
		)
	);
}

/**
 * O estado vazio digno (FR-7): uma caixa com a copy e a ligacao ao canal publico do IPCN.
 *
 * O `h2` desce do `h1` da `page-hero` na pagina da Agenda e fica ao lado do `h2` da seccao
 * na Home; um `h3` saltaria o nivel 2. As classes sao proprias do vazio e o desenho vive no
 * `style.css` (AD-5, AD-1 excepcao 2): nenhum atributo `style` sai daqui.
 *
 * @return string
 */
function ipcn_agenda_vazio() {
	return '<div class="ipcn-agenda-vazio">'
		. '<h2 class="ipcn-agenda-vazio-titulo">A agenda está sendo montada</h2>'
		. '<p class="ipcn-agenda-vazio-texto">Acompanhe nossas redes sociais para os próximos encontros e atividades.</p>'
		. '<p class="ipcn-agenda-vazio-acao"><a class="ipcn-agenda-vazio-link" href="' . esc_url( 'https://www.instagram.com/ipcnbrasil/' ) . '" target="_blank" rel="noopener noreferrer">Ver no Instagram</a></p>'
		. '</div>';
}

/**
 * Render do bloco `ipcn/agenda`.
 *
 * @param array $attributes Atributos do bloco; so `limite`.
 * @return string
 */
function ipcn_agenda_render( $attributes = array() ) {
	$limite = isset( $attributes['limite'] ) ? (int) $attributes['limite'] : 3;

	$query = ipcn_agenda_query( $limite );

	if ( ! $query->have_posts() ) {
		return ipcn_agenda_vazio();
	}

	// O markup do cartao vive so em `patterns/` (AD-1 excepcao 1, AD-3). Sem pattern nao ha
	// cartao: sem esta guarda sairia uma grelha vazia e nada o diria (o modo de falha
	// silenciosa do AD-14).
	$registry = WP_Block_Patterns_Registry::get_instance();
	$pattern  = $registry->is_registered( 'ipcn/card' ) ? $registry->get_registered( 'ipcn/card' ) : null;
	$markup   = ( is_array( $pattern ) && isset( $pattern['content'] ) ) ? $pattern['content'] : '';

	if ( '' === $markup ) {
		_doing_it_wrong( 'ipcn_agenda_render', 'O pattern ipcn/card não está registrado; o cartão não pode ser renderizado.', '0.2.0' );
		wp_reset_postdata();
		return ipcn_agenda_vazio();
	}

	$items = '';
	while ( $query->have_posts() ) {
		$query->the_post();

		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );

		// Fora de um `core/post-template`, o contexto do post e injectado a mao — o mesmo
		// idioma de `inc/listings.php`. Sem ele, o `core/post-title` do cartao mostra o
		// titulo da propria pagina e falha em silencio com aspecto plausivel (AD-14).
		$context = static function ( $block_context ) use ( $post_id, $post_type ) {
			$block_context['postType'] = $post_type;
			$block_context['postId']   = $post_id;
			return $block_context;
		};

		// A data que o cartao mostra e a do encontro, no texto e no `datetime`: o
		// `core/post-date` do nucleo tira os dois de `get_the_date( $format, $post_ID )` e de
		// `get_the_date( 'c', $post_ID )`, pelo que um filtro a volta do render troca as duas
		// coisas sem tocar no markup do pattern. A data de publicacao nao sobra em lado nenhum.
		// O filtro responde pelo post que lhe e dado — nunca pelo `$post_id` do ciclo: se
		// alguem pedir a data de outro post dentro desta janela, recebe a data dele.
		$data_do_encontro = static function ( $the_date, $format, $post ) {
			if ( ! ( $post instanceof WP_Post ) ) {
				return $the_date;
			}

			$data = get_post_meta( $post->ID, 'data_evento', true );
			if ( ! is_string( $data ) || '' === $data ) {
				return $the_date;
			}

			$timezone = wp_timezone();
			$evento   = date_create_immutable( $data, $timezone );
			if ( ! $evento instanceof DateTimeImmutable ) {
				return $the_date;
			}

			if ( '' === $format ) {
				$format = (string) get_option( 'date_format' );
			}

			return wp_date( $format, $evento->getTimestamp(), $timezone );
		};

		add_filter( 'render_block_context', $context, 1 );
		add_filter( 'get_the_date', $data_do_encontro, 10, 3 );

		foreach ( parse_blocks( $markup ) as $card_block ) {
			if ( empty( $card_block['blockName'] ) ) {
				continue;
			}
			$items .= render_block( $card_block );
		}

		remove_filter( 'get_the_date', $data_do_encontro, 10 );
		remove_filter( 'render_block_context', $context, 1 );
	}

	wp_reset_postdata();

	return '<div class="ipcn-grid">' . $items . '</div>';
}

add_action(
	'init',
	function () {
		register_block_type(
			'ipcn/agenda',
			array(
				'attributes'      => array(
					'limite' => array(
						'type'    => 'integer',
						'default' => 3,
					),
				),
				'render_callback' => 'ipcn_agenda_render',
			)
		);
	}
);

/**
 * O archive de `agenda-ipcn` redirecciona (301) para a pagina da Agenda (AD-8, historia 2.4).
 *
 * O endereco deixa de servir HTML de listagem: nao e superficie publica nem indexavel, e
 * nunca lista encontros passados como se fossem a agenda. A pagina resolve-se por slug
 * (`agenda-ipcn`), nunca por id. Sem a pagina na base de dados nao se redirecciona — melhor
 * o archive do que um destino inexistente.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_category( 'agenda-ipcn' ) ) {
			return;
		}

		$pagina = get_page_by_path( 'agenda-ipcn' );
		if ( ! $pagina instanceof WP_Post ) {
			return;
		}

		$destino = get_permalink( $pagina );
		if ( ! is_string( $destino ) || '' === $destino ) {
			return;
		}

		wp_safe_redirect( $destino, 301 );
		exit;
	}
);
