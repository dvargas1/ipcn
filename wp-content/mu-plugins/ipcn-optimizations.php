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
