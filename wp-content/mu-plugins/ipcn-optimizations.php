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
