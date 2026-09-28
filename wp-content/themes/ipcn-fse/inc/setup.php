<?php
/**
 * IPCN FSE — setup: fontes, preconnect e style.css (frontend e editor).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fontes Google (Oswald + Inter) com display=swap, preconnect antes.
 * Era buraco: theme.json declarava as fontes mas nada as baixava —
 * o site inteiro renderizava no fallback sans-serif.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		// Oswald no cluster principal + Playfair Display como alternativa de identidade
		wp_enqueue_style(
			'ipcn-fse-fonts',
			'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap',
			array(),
			null
		);
	}
);

/**
 * Preconnect pro Google Fonts (menoslatência no carregamento das fontes).
 */
add_filter(
	'wp_resource_hints',
	function ( $urls, $relation_type ) {
		if ( 'preconnect' === $relation_type ) {
			$urls[] = array(
				'href'        => 'https://fonts.googleapis.com',
				'crossorigin' => 'anonymous',
			);
			$urls[] = 'https://fonts.gstatic.com';
		}
		return $urls;
	},
	10,
	2
);

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
		// Variável CSS para a imagem de fundo do hero (path dinâmico por ambiente).
		$hero_bg = get_theme_file_uri( 'assets/hero-bg.jpg' );
		wp_add_inline_style( 'ipcn-fse-style', ':root{--ipcn-hero-bg:url("' . esc_url( $hero_bg ) . '")}' );
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
