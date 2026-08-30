<?php
/**
 * Plugin Name: IPCN Otimizações
 * Description: Correções e otimizações específicas do site IPCN Brasil.
 * Author: IPCN
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Desativa o cache de features do Divi.
 *
 * O cache de features grava grandes arrays serializados em postmeta no hook
 * 'shutdown', o que causava o erro de banco "Commands out of sync" no servidor
 * da Hostinger. Desativar elimina essas escritas sem impacto funcional.
 */
add_filter( 'et_builder_post_feature_cache_enabled', '__return_false' );
add_filter( 'et_builder_global_feature_cache_enabled', '__return_false' );

/**
 * Segurança — esconde a versão do WordPress.
 *
 * O Wordfence (hideWPVersion) não remove o <meta name="generator"> injetado
 * pelo tema/plugins. Removemos explicitamente para não expor a versão do core.
 */
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Segurança — bloqueia XML-RPC.
 *
 * O endpoint xmlrpc.php é vetor comum de ataques de força bruta e brute-force
 * de credenciais. Como o site não usa pingbacks/apps externas, desligamos.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

// Remove o header X-Pingback existente.
add_filter( 'wp_headers', function ( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
} );

/**
 * Corrige a fonte de ícones do Divi (ETmodules) que está ausente no tema.
 *
 * O pacote do tema (Divi 4.20.2) veio sem os arquivos ETmodules.* e sem o
 * @font-face correspondente. Sem eles, o hambúrguer do menu mobile vira a
 * letra "A" e as setas do slider viram "4"/"5" (caracteres crus da tabela
 * de ícones). Os arquivos ETmodules.{woff,ttf,eot,svg} foram restaurados na
 * pasta core/admin/fonts/ e injetamos o @font-face aqui (reversível, versionado).
 */
add_action( 'wp_head', function () {
	$font_url = get_template_directory_uri() . '/core/admin/fonts/ETmodules';
	echo "<style id=\"ipcn-etmodules-fix\">@font-face{font-family:'ETmodules';src:url('" . esc_url( $font_url . '.eot' ) . "');src:url('" . esc_url( $font_url . '.eot' ) . "#iefix') format('embedded-opentype'),url('" . esc_url( $font_url . '.woff' ) . "') format('woff'),url('" . esc_url( $font_url . '.ttf' ) . "') format('truetype'),url('" . esc_url( $font_url . '.svg#ETmodules' ) . "') format('svg');font-weight:normal;font-style:normal}</style>\n";
}, 1 );

/**
 * Estilização premium dos formulários Divi (Associe-se, Fale Conosco).
 * Card nítido, inputs com foco navy, botão com hover e mensagens de feedback.
 */
add_action( 'wp_head', function () {
	$css = '
	/* Card já vem com borda/sombra do builder — só reforço */
	.et_pb_contact_form_container p.et_pb_contact_field {
		margin-bottom: 18px !important;
		padding: 0 !important;
	}
	.et_pb_contact_form_container label.et_pb_contact_form_label {
		font-weight: 700 !important;
		color: #0d176b !important;
		font-size: 13px !important;
		letter-spacing: 0.02em !important;
		margin-bottom: 8px !important;
		display: block;
		font-family: "Open Sans", sans-serif !important;
	}
	.et_pb_contact_form_container .input,
	.et_pb_contact_form_container input[type="text"],
	.et_pb_contact_form_container input[type="email"],
	.et_pb_contact_form_container textarea {
		border: 1.5px solid #e2e8f0 !important;
		border-radius: 10px !important;
		padding: 14px 16px !important;
		font-size: 16px !important;
		line-height: 1.45 !important;
		background: #ffffff !important;
		color: #1e293b !important;
		transition: border-color .18s ease, box-shadow .18s ease, background .18s ease !important;
		width: 100% !important;
		box-sizing: border-box;
		font-family: "Open Sans", sans-serif !important;
	}
	.et_pb_contact_form_container .input::placeholder,
	.et_pb_contact_form_container input::placeholder,
	.et_pb_contact_form_container textarea::placeholder {
		color: #94a3b8 !important;
		opacity: 1;
	}
	.et_pb_contact_form_container .input:focus,
	.et_pb_contact_form_container input:focus,
	.et_pb_contact_form_container textarea:focus {
		border-color: #0d176b !important;
		box-shadow: 0 0 0 4px rgba(13,23,107,.10) !important;
		background: #fff !important;
		outline: none !important;
	}
	.et_pb_contact_form_container .input:invalid:focus {
		border-color: #dc2626 !important;
		box-shadow: 0 0 0 4px rgba(220,38,38,.08) !important;
	}
	.et_pb_contact_form_container .et_contact_bottom_container {
		margin-top: 10px !important;
		display: flex;
		justify-content: flex-start;
	}
	.et_pb_contact_form_container .et_pb_contact_submit {
		background: #0d176b !important;
		color: #fff !important;
		border: 0 !important;
		border-radius: 10px !important;
		padding: 15px 34px !important;
		font-weight: 700 !important;
		font-size: 15px !important;
		letter-spacing: 0.06em !important;
		text-transform: uppercase !important;
		transition: background .18s ease, transform .12s ease, box-shadow .18s ease !important;
		box-shadow: 0 8px 20px rgba(13,23,107,.18) !important;
		font-family: "Open Sans", sans-serif !important;
	}
	.et_pb_contact_form_container .et_pb_contact_submit:hover {
		background: #1a2a9e !important;
		transform: translateY(-1px);
		box-shadow: 0 12px 28px rgba(13,23,107,.24) !important;
	}
	.et_pb_contact_form_container .et_pb_contact_submit:active {
		transform: translateY(0);
		box-shadow: 0 4px 12px rgba(13,23,107,.18) !important;
	}
	/* Mensagens de feedback do Divi */
	.et_pb_contact_form_container .et-pb-contact-message {
		border-radius: 10px !important;
		padding: 12px 16px !important;
		font-size: 14px !important;
		line-height: 1.55 !important;
		margin-bottom: 16px !important;
	}
	.et_pb_contact_form_container .et-pb-contact-message p {
		margin: 0 !important;
		padding: 0 !important;
	}
	.et-pb-contact-message.et_pb_contact_form_error {
		background: #fef2f2 !important;
		border: 1px solid #fecaca !important;
		color: #991b1b !important;
	}
	.et-pb-contact-message:not(.et_pb_contact_form_error):not(:empty) {
		background: #f0fdf4 !important;
		border: 1px solid #bbf7d0 !important;
		color: #166534 !important;
	}
	@media (max-width: 767px) {
		.et_pb_contact_form_container .et_pb_contact_submit { width: 100% !important; text-align: center; justify-content: center; }
		.et_pb_contact_form_container .et_contact_bottom_container { justify-content: stretch; }
	}
	';
	echo "<style id=\"ipcn-form-style\">" . $css . "</style>\n";
}, 2 );
