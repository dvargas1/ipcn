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
