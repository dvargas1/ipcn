<?php
/**
 * IPCN FSE — modelo de conteudo: CPT acervo_ipcn, taxonomia tema_acervo e a meta
 * `data_evento` com a sua entrada propria no editor.
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

		// A data de um encontro (AD-10): meta de `post`, guardada em `Y-m-d` e validada ao
		// guardar. Registar a meta NAO cria campo nenhum no editor — e a caixa abaixo que o
		// cria. Fora do REST de proposito: a unica portagem de escrita e a caixa com a sua
		// validacao, e um `show_in_rest` sem `sanitize_callback` deixaria escrever um valor
		// fora do `Y-m-d` por outra via, que a consulta da agenda descartaria em silencio.
		register_post_meta(
			'post',
			'data_evento',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
			)
		);
	}
);

/**
 * A entrada propria da meta `data_evento` (AD-10): uma caixa de meta com o campo
 * `input type="date"`, que traz o selector de data do browser. O painel de campos
 * personalizados crus nao serve — a cliente nao escreve uma meta key a mao.
 */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'ipcn_data_evento',
			'Data do encontro',
			function ( $post ) {
				$value = get_post_meta( $post->ID, 'data_evento', true );
				$value = is_string( $value ) ? $value : '';

				wp_nonce_field( 'ipcn_data_evento_save', 'ipcn_data_evento_nonce' );

				echo '<label for="ipcn-data-evento">Data do encontro</label>';
				echo '<input type="date" id="ipcn-data-evento" name="ipcn_data_evento" value="' . esc_attr( $value ) . '" />';
				echo '<p class="description">Deixe o campo vazio se o encontro ainda não tem data.</p>';
			},
			'post',
			'side'
		);
	}
);

/**
 * A unica portagem de escrita da meta (AD-10): nonce, capacidade, guardas de autosave e
 * revisao, e a validacao de formato. Vazio e permitido e limpa o valor; so um `Y-m-d` real
 * entra; qualquer outro formato nao escreve nada, pelo que o valor valido anterior sobrevive
 * em vez de ser apagado por um erro de digitacao.
 */
add_action(
	'save_post_post',
	function ( $post_id, $post ) {
		if ( ! isset( $_POST['ipcn_data_evento_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['ipcn_data_evento_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'ipcn_data_evento_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['ipcn_data_evento'] ) || ! is_string( $_POST['ipcn_data_evento'] ) ) {
			return;
		}

		$data = trim( wp_unslash( $_POST['ipcn_data_evento'] ) );

		if ( '' === $data ) {
			delete_post_meta( $post_id, 'data_evento' );
			return;
		}

		if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $data, $matches ) && checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1] ) ) {
			update_post_meta( $post_id, 'data_evento', $data );
		}
	},
	10,
	2
);
