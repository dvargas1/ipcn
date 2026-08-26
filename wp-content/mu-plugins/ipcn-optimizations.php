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
 * Estilização profissional dos formulários de contato do Divi (páginas Associe-se, etc).
 * Inputs com borda suave, foco na cor navy do IPCN, labels legíveis e botão com hover.
 */
add_action( 'wp_head', function () {
	$css = '
	.et_pb_contact_form_container .input,
	.et_pb_contact_form_container input[type="text"],
	.et_pb_contact_form_container input[type="email"],
	.et_pb_contact_form_container textarea {
		border: 1px solid #d4d9e3 !important;
		border-radius: 8px !important;
		padding: 14px 16px !important;
		font-size: 16px !important;
		background: #fff !important;
		color: #1a1a2e !important;
		transition: border-color .2s, box-shadow .2s;
		width: 100% !important;
		box-sizing: border-box;
	}
	.et_pb_contact_form_container .input:focus,
	.et_pb_contact_form_container input:focus,
	.et_pb_contact_form_container textarea:focus {
		border-color: #0d176b !important;
		box-shadow: 0 0 0 3px rgba(13,23,107,.12) !important;
		outline: none !important;
	}
	.et_pb_contact_form_container label {
		font-weight: 600 !important;
		color: #0d176b !important;
		font-size: 14px !important;
		margin-bottom: 6px !important;
		display: block;
	}
	.et_pb_contact_form_container .et_contact_bottom_container {
		margin-top: 18px !important;
	}
	.et_pb_contact_form_container .et_pb_contact_submit {
		background: #0d176b !important;
		border-radius: 8px !important;
		padding: 14px 32px !important;
		font-weight: 700 !important;
		letter-spacing: 1px !important;
		transition: background .2s, transform .1s;
	}
	.et_pb_contact_form_container .et_pb_contact_submit:hover {
		background: #1a2a9e !important;
		transform: translateY(-1px);
	}
	';
	echo "<style id=\"ipcn-form-style\">" . $css . "</style>\n";
}, 2 );
