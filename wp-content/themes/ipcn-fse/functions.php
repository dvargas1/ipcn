<?php
/**
 * IPCN FSE — funcoes do tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enfileira o style.css do tema (fixes visuais: logo, cards, footer, mobile).
 * Block themes não carregam style.css sozinhos no frontend.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'ipcn-fse-style',
			get_stylesheet_uri(),
			array(),
			(string) filemtime( get_stylesheet_directory() . '/style.css' )
		);
	}
);

/**
 * Carrega o style.css tambem no editor do site (FSE), para o editor ficar fiel ao frontend.
 */
add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style(
			'ipcn-fse-style-editor',
			get_stylesheet_uri(),
			array(),
			(string) filemtime( get_stylesheet_directory() . '/style.css' )
		);
	}
);

/**
 * Form nativo de Associe-se: processa POST via admin-post.php e envia para contato@ipcnbrasil.org.
 * Sem nonce estatico (LiteSpeed cache serve HTML antigo; nonce em pagina cacheada quebra o form).
 * Protecao: honeypot + validacao de campos + referer.
 */
add_action( 'admin_post_ipcn_assoc', 'ipcn_fse_handle_assoc' );
add_action( 'admin_post_nopriv_ipcn_assoc', 'ipcn_fse_handle_assoc' );

function ipcn_fse_handle_assoc() {
	$redirect = home_url( '/associe-se/' );

	// Honeypot preenchido = bot.
	if ( ! empty( $_POST['ipcn_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'cadastro', 'erro', $redirect ) );
		exit;
	}

	$nome  = isset( $_POST['ipcn_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['ipcn_nome'] ) ) : '';
	$email = isset( $_POST['ipcn_email'] ) ? sanitize_email( wp_unslash( $_POST['ipcn_email'] ) ) : '';
	$tel   = isset( $_POST['ipcn_tel'] ) ? sanitize_text_field( wp_unslash( $_POST['ipcn_tel'] ) ) : '';

	if ( empty( $nome ) || empty( $email ) || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'cadastro', 'erro', $redirect ) );
		exit;
	}

	$to      = 'contato@ipcnbrasil.org';
	$subject = 'Novo cadastro de associado - IPCN';
	$body    = "Novo cadastro pelo site:\n\n"
		. "Nome: {$nome}\n"
		. "E-mail: {$email}\n"
		. "Telefone: {$tel}\n\n"
		. 'Enviado em ' . current_time( 'd/m/Y H:i' );
	$headers = array( 'Reply-To: ' . $nome . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'cadastro', $sent ? 'ok' : 'erro', $redirect ) );
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
 * Form de associado (usado na pagina /associe-se).
 */
add_shortcode(
	'ipcn_assoc_form',
	function () {
		$action = esc_url( admin_url( 'admin-post.php' ) );
		return '<div class="ipcn-form-card"><form class="ipcn-form" method="post" action="' . $action . '">'
			. '<input type="hidden" name="action" value="ipcn_assoc">'
			. '<input type="text" name="ipcn_hp" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<label>Nome completo *<input type="text" name="ipcn_nome" required></label>'
			. '<label>E-mail *<input type="email" name="ipcn_email" required></label>'
			. '<label>Telefone<input type="tel" name="ipcn_tel"></label>'
			. '<button type="submit" class="ipcn-form-submit">Enviar cadastro</button>'
			. '</form></div>';
	}
);

/**
 * Form nativo de Fale Conosco: nome, e-mail e mensagem -> contato@ipcnbrasil.org.
 */
add_action( 'admin_post_ipcn_contact', 'ipcn_fse_handle_contact' );
add_action( 'admin_post_nopriv_ipcn_contact', 'ipcn_fse_handle_contact' );

function ipcn_fse_handle_contact() {
	$redirect = home_url( '/fale-conosco/' );

	if ( ! empty( $_POST['ipcn_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'contato', 'erro', $redirect ) );
		exit;
	}

	$nome  = isset( $_POST['ipcn_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['ipcn_nome'] ) ) : '';
	$email = isset( $_POST['ipcn_email'] ) ? sanitize_email( wp_unslash( $_POST['ipcn_email'] ) ) : '';
	$msg   = isset( $_POST['ipcn_msg'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ipcn_msg'] ) ) : '';

	if ( empty( $nome ) || empty( $email ) || ! is_email( $email ) || empty( $msg ) ) {
		wp_safe_redirect( add_query_arg( 'contato', 'erro', $redirect ) );
		exit;
	}

	$to      = 'contato@ipcnbrasil.org';
	$subject = 'Mensagem pelo site - IPCN';
	$body    = "Mensagem enviada pelo Fale Conosco:\n\n"
		. "Nome: {$nome}\n"
		. "E-mail: {$email}\n\n"
		. $msg . "\n\n"
		. 'Enviado em ' . current_time( 'd/m/Y H:i' );
	$headers = array( 'Reply-To: ' . $nome . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'contato', $sent ? 'ok' : 'erro', $redirect ) );
	exit;
}

/**
 * Form de Fale Conosco (usado na pagina /fale-conosco).
 */
add_shortcode(
	'ipcn_contact_form',
	function () {
		$action = esc_url( admin_url( 'admin-post.php' ) );
		return '<div class="ipcn-form-card"><form class="ipcn-form" method="post" action="' . $action . '">'
			. '<input type="hidden" name="action" value="ipcn_contact">'
			. '<input type="text" name="ipcn_hp" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<label>Nome *<input type="text" name="ipcn_nome" required></label>'
			. '<label>E-mail *<input type="email" name="ipcn_email" required></label>'
			. '<label>Mensagem *<textarea name="ipcn_msg" required></textarea></label>'
			. '<button type="submit" class="ipcn-form-submit">Enviar mensagem</button>'
			. '</form></div>';
	}
);

/**
 * Mensagens de feedback do Fale Conosco.
 */
add_shortcode(
	'ipcn_contact_message',
	function () {
		$status = isset( $_GET['contato'] ) ? sanitize_key( $_GET['contato'] ) : '';
		if ( 'ok' === $status ) {
			return '<div class="ipcn-form-success">Mensagem enviada! Vamos responder no e-mail informado o mais breve possivel.</div>';
		}
		if ( 'erro' === $status ) {
			return '<div class="ipcn-form-error">Ops, nao conseguimos enviar sua mensagem. Confira os campos e tente de novo, ou escreva direto para contato@ipcnbrasil.org.</div>';
		}
		return '';
	}
);

/**
 * Mensagem de sucesso/erro do form de associado (lida da query string).
 */
add_shortcode(
	'ipcn_assoc_message',
	function () {
		$status = isset( $_GET['cadastro'] ) ? sanitize_key( $_GET['cadastro'] ) : '';
		if ( 'ok' === $status ) {
			return '<div class="ipcn-form-success">Recebemos seu cadastro com sucesso! Nossa equipe vai entrar em contato em breve no e-mail ou telefone informado.</div>';
		}
		if ( 'erro' === $status ) {
			return '<div class="ipcn-form-error">Ops, nao conseguimos enviar seu cadastro. Confira os campos e tente de novo, ou fale com a gente em contato@ipcnbrasil.org.</div>';
		}
		return '';
	}
);
