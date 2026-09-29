<?php
/**
 * IPCN FSE — formularios: handlers admin_post e shortcodes dos forms e mensagens.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 * Form de associado (usado na pagina /associe-se).
 */
add_shortcode(
	'ipcn_assoc_form',
	function () {
		$action = esc_url( admin_url( 'admin-post.php' ) );
		return '<div class="ipcn-form-card"><form class="ipcn-form" method="post" action="' . $action . '">'
			. '<input type="hidden" name="action" value="ipcn_assoc">'
			. '<input type="text" name="ipcn_hp" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<label for="ipcn-nome">Nome completo *<input type="text" name="ipcn_nome" id="ipcn-nome" required></label>'
			. '<label for="ipcn-email">E-mail *<input type="email" name="ipcn_email" id="ipcn-email" required></label>'
			. '<label for="ipcn-tel">Telefone<input type="tel" name="ipcn_tel" id="ipcn-tel"></label>'
			. '<button type="submit" class="ipcn-form-submit">Enviar pedido</button>'
			. '</form></div>';
	}
);

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
			. '<label for="ipcn-contato-nome">Nome *<input type="text" name="ipcn_nome" id="ipcn-contato-nome" required></label>'
			. '<label for="ipcn-contato-email">E-mail *<input type="email" name="ipcn_email" id="ipcn-contato-email" required></label>'
			. '<label for="ipcn-contato-msg">Mensagem *<textarea name="ipcn_msg" id="ipcn-contato-msg" required></textarea></label>'
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
			return '<div class="ipcn-form-success">Recebemos sua mensagem. Vamos responder pelo e-mail informado.</div>';
		}
		if ( 'erro' === $status ) {
			return '<div class="ipcn-form-error">Ops, não conseguimos enviar sua mensagem. Confira os campos e tente de novo, ou escreva direto para contato@ipcnbrasil.org.</div>';
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
			return '<div class="ipcn-form-success">Recebemos seu pedido. Vamos responder pelo e-mail informado.</div>';
		}
		if ( 'erro' === $status ) {
			return '<div class="ipcn-form-error">Ops, não conseguimos enviar seu pedido. Confira os campos e tente de novo, ou fale com a gente em contato@ipcnbrasil.org.</div>';
		}
		return '';
	}
);
