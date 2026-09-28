<?php
/**
 * IPCN FSE — modelo de conteudo: CPT acervo_ipcn e taxonomia tema_acervo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CPT do Acervo + taxonomia tema_acervo (Fase 3).
 * capability_type provisionado pra Fase 4 (roles de associados).
 */
add_action(
	'init',
	function () {
		register_post_type(
			'acervo_ipcn',
			array(
				'labels'          => array(
					'name'          => 'Acervo',
					'singular_name' => 'Item do Acervo',
					'add_new_item'  => 'Adicionar item ao Acervo',
					'edit_item'     => 'Editar item do Acervo',
					'search_items'  => 'Buscar no Acervo',
				),
				'public'          => true,
				'has_archive'     => 'acervo',
				'rewrite'         => array(
					'slug'       => 'acervo',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
				'show_in_rest'    => true,
				'capability_type' => 'acervo',
				'map_meta_cap'    => true,
				'menu_icon'       => 'dashicons-archive',
				'menu_position'   => 5,
			)
		);

		register_taxonomy(
			'tema_acervo',
			'acervo_ipcn',
			array(
				'labels'            => array(
					'name'          => 'Temas',
					'singular_name' => 'Tema',
					'menu_name'     => 'Temas do Acervo',
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'temas',
					'with_front' => false,
				),
			)
		);
	}
);
