<?php
/**
 * IPCN FSE — funcoes do tema.
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
			. '<button type="submit" class="ipcn-form-submit">Enviar cadastro</button>'
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
/**
 * IPCN FSE — hero de arquivo para categorias (Destaques, Diaspora, Colunistas, Notas).
 * Shortcode le a queried category e imprime eyebrow + h1 + descricao no padrao navy das outras paginas.
 * Uso no archive.html: [ipcn_archive_hero]
 */

add_shortcode(
	'ipcn_archive_hero',
	function () {
		$q = get_queried_object();
		if ( ! $q || ! property_exists( $q, 'slug' ) ) {
			return '';
		}

		$titles = array(
			'destaques'  => 'Destaques do IPCN',
			'diaspora'   => 'Diaspora Afroatlantica',
			'colunistas' => 'Colunistas do IPCN',
			'notas'      => 'Notas IPCN',
			'noticias'   => 'Noticias do IPCN',
			'editorial'  => 'Editorial IPCN',
		);
		$descs  = array(
			'destaques'  => 'Acoes, eventos e conquistas do instituto em destaque.',
			'diaspora'   => 'Vozes da diaspora negra: historias, pesquisas e reflexoes.',
			'colunistas' => 'Opiniao e analise de colaboradores do IPCN.',
			'notas'      => 'Comentarios breves sobre atualidade e cultura negra.',
			'noticias'   => 'Noticias e atualidades do Instituto de Pesquisas das Culturas Negras.',
			'editorial'  => 'Conteudo editorial produzido pelo instituto.',
		);

		$slug = $q->slug;
		$name = ( ! empty( $q->name ) ) ? $q->name : ( isset( $titles[ $slug ] ) ? $titles[ $slug ] : ucwords( str_replace( '-', ' ', $slug ) ) );
		$t    = isset( $titles[ $slug ] ) ? $titles[ $slug ] : ( $name ?: 'Conteudo IPCN' );
		$d    = isset( $descs[ $slug ] ) ? $descs[ $slug ] : 'Selecao de conteudo publicado pelo IPCN.';

		ob_start();
		?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"56px","bottom":"48px","left":"20px","right":"20px"}}},"backgroundColor":"navy","textColor":"base","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-base-color has-navy-background-color has-text-color has-background" style="padding-top:56px;padding-bottom:48px;padding-left:20px;padding-right:20px"><!-- wp:group {"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontSize":"13px","fontWeight":"600","letterSpacing":"2px","textTransform":"uppercase"}},"textColor":"base"} -->
<p class="has-base-color has-text-color" style="font-size:13px;font-weight:600;letter-spacing:2px;text-transform:uppercase">IPCN &middot; <?php echo esc_html( $name ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"clamp(22px, 4vw, 36px)","fontWeight":"700","lineHeight":"1.15"}},"textColor":"base"} -->
<h1 class="wp-block-heading has-base-color has-text-color" style="font-family:var(--wp--preset--font-family--oswald);font-size:clamp(22px,4vw,36px);font-weight:700;line-height:1.15;color:var(--wp--preset--color--base)"><?php echo esc_html( $t ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}},"textColor":"base"} -->
<p class="has-base-color has-text-color" style="font-size:16px"><?php echo esc_html( $d ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
		<?php
		return ob_get_clean();
	}
);
/**
 * IPCN FSE — Agenda da home com filtro de data futura + estado vazio elegante.
 * Uso no front-page.html: [ipcn_home_agenda]
 * Regra: so publicados da categoria agenda-ipcn com post_date >= hoje.
 * (Meta "data_evento" sera respeitada quando a cliente comecar a preencher.)
 */

add_shortcode(
	'ipcn_home_agenda',
	function () {
		$cat = get_category_by_slug( 'agenda-ipcn' );
		if ( ! $cat ) {
			return '';
		}

		$today   = current_time( 'Y-m-d' );
		$network = 'https://www.instagram.com/ipcnbrasil/';

		$query_args = array(
			'posts_per_page'      => 3,
			'category_name'       => 'agenda-ipcn',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'ASC',
		);

		// Estrategia: se existir meta data_evento usa ela; se nao, checa post_date.
		$meta_check = new WP_Query(
			array_merge(
				$query_args,
				array(
					'meta_query' => array(
						array(
							'key'     => 'data_evento',
							'value'   => $today,
							'compare' => '>=',
							'type'    => 'DATE',
						),
					),
				)
			)
		);

		if ( $meta_check->have_posts() ) {
			$q = $meta_check;
		} else {
			$q = new WP_Query(
				array_merge(
					$query_args,
					array(
						'date_query' => array(
							array(
								'after' => $today . ' 00:00:00',
							),
						),
					)
				)
			);
		}

		// Estado vazio elegante.
		if ( ! $q->have_posts() ) {
			ob_start();
			?>
<!-- wp:group {"style":{"border":{"radius":"12px","width":"1px"},"spacing":{"padding":{"top":"44px","right":"28px","bottom":"44px","left":"28px"}}},"borderColor":"muted","backgroundColor":"base","layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group has-border-color has-muted-border-color has-base-background-color has-background" style="border-radius:12px;border-width:1px;padding-top:44px;padding-right:28px;padding-bottom:44px;padding-left:28px">
  <!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"20px","fontWeight":"600"}}} -->
  <h3 class="wp-block-heading has-text-align-center" style="font-family:var(--wp--preset--font-family--oswald);font-size:20px;font-weight:600">A agenda esta sendo montada</h3>
  <!-- /wp:heading -->
  <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"15px"},"color":{"text":"#64748b"}}} -->
  <p class="has-text-align-center" style="color:#64748b;font-size:15px">Acompanhe nossas redes sociais para os proximos encontros e atividades.</p>
  <!-- /wp:paragraph -->
  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"18px"}}}} -->
  <div class="wp-block-buttons" style="margin-top:18px">
    <!-- wp:button {"className":"is-style-outline"} -->
    <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $network ); ?>" target="_blank" rel="noopener noreferrer">Ver no Instagram</a></div>
    <!-- /wp:button -->
  </div>
  <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
			<?php
			return ob_get_clean();
		}

		// Agenda com data futura: renderiza cards.
		ob_start();
		?>
<!-- wp:query -->
<div class="wp-block-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
		<?php
		while ( $q->have_posts() ) :
			$q->the_post();
			$thumb = get_the_post_thumbnail( get_the_ID(), 'medium_large' );
			?>
<!-- wp:group {"className":"ipcn-card-v2"} -->
<div class="wp-block-group ipcn-card-v2">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":{"topLeft":"12px","topRight":"12px","bottomLeft":"0px","bottomRight":"0px"}}}} /-->
<!-- wp:post-date {"style":{"typography":{"fontSize":"13px"},"color":{"text":"#a85a32"}},"format":"j \\d\\e M \\d\\e Y"} /-->
<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"19px","fontWeight":"600","lineHeight":"1.35"},"spacing":{"margin":{"top":"8px"}}}} /-->
<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"style":{"typography":{"fontSize":"14px"}}} /-->
</div>
<!-- /wp:group -->
			<?php
		endwhile;
		wp_reset_postdata();
		?>
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
		<?php
		return ob_get_clean();
	}
);
/**
 * Banner de cookies minimalista (bottom bar fixo).
 * Substitui a intrusao do CookieYes full-banner + tabela técnica na tela inicial.
 * Regra: banner some ao clicar "Aceitar Todos" (localStorage 180d);
 * "Gerenciar Preferencias" abre painel com a tabela detalhada (a mesma do plugin).
 */

add_action(
	'wp_footer',
	function () {
		// So no frontend, sem painel admin.
		if ( is_admin() ) {
			return;
		}
		$policy = esc_url( home_url( '/politica-de-privacidade/' ) );
		?>
<div id="ipcn-cookie-bar" role="region" aria-label="Aviso de cookies" style="display:none">
  <p class="ipcn-cookie-text">Usamos cookies para melhorar sua experiência no Portal do IPCN. Ao continuar, você concorda com nossa <a href="<?php echo $policy; ?>">Política de Privacidade</a>.</p>
  <button type="button" class="ipcn-cookie-btn ipcn-cookie-accept" id="ipcn-cookie-accept">Aceitar Todos</button>
  <button type="button" class="ipcn-cookie-btn ipcn-cookie-manage" id="ipcn-cookie-manage">Gerenciar Preferências</button>
  <button type="button" class="ipcn-cookie-btn ipcn-cookie-manage" id="ipcn-cookie-necessary" style="color:#c9a86a;border-color:transparent;background:transparent;font-weight:600">Só os necessários</button>
</div>

<div id="ipcn-cookie-panel" role="dialog" aria-label="Preferências de cookies">
  <h3>Preferências de cookies</h3>
  <p style="font-size:13px;color:#b6b9c2;margin:0 0 14px">Escolha quais categorias de cookies aceitar. Cookies necessários não podem ser desativados.</p>
  <table>
    <thead><tr><th>Categoria</th><th>Para que serve</th><th>Status</th></tr></thead>
    <tbody>
      <tr><td>Necessários</td><td>Login, segurança, preferências de navegação. Sem eles o site não funciona.</td><td><strong>Sempre ativos</strong></td></tr>
      <tr><td>Analytics</td><td>Google Analytics: medição de tráfego anônima para melhorar o conteúdo.</td><td><label><input type="checkbox" id="ipcn-cookie-analytics" checked> Permitir</label></td></tr>
      <tr><td>Marketing / Terceiros</td><td>Integrações de vídeo e redes sociais. Nenhum dado é vendido.</td><td><label><input type="checkbox" id="ipcn-cookie-marketing"> Permitir</label></td></tr>
    </tbody>
  </table>
  <div class="ipcn-cookie-actions">
    <button type="button" class="ipcn-cookie-btn ipcn-cookie-manage" id="ipcn-cookie-save">Salvar preferências</button>
    <button type="button" class="ipcn-cookie-btn ipcn-cookie-accept" id="ipcn-cookie-accept-all-panel">Aceitar todos</button>
  </div>
</div>

<script>
(function () {
  var KEY = 'ipcn_cookie_consent_v1';
  var bar = document.getElementById('ipcn-cookie-bar');
  var panel = document.getElementById('ipcn-cookie-panel');
  if (!bar) return;

  function consented() {
    try { return !!localStorage.getItem(KEY); } catch (e) { return false; }
  }
  function hideBar() { bar.style.display = 'none'; }
  function showBar() { bar.style.display = 'flex'; }
  function closePanel() { panel.classList.remove('open'); }

  function save(cons) {
    try { localStorage.setItem(KEY, JSON.stringify(Object.assign({ ts: Date.now() }, cons))); } catch (e) {}
    hideBar();
    closePanel();
    // Sincroniza CookieYes (se ativo) tocando o botao de aceite interno, para não exibir o banner legado.
    var cy = document.querySelector('#cookie-law-info-bar .wt-cli-accept-all-btn');
    if (cy && cons.all) { cy.click(); }
  }

  document.getElementById('ipcn-cookie-accept').addEventListener('click', function () {
    save({ necessary: true, analytics: true, marketing: true, all: true });
  });
  document.getElementById('ipcn-cookie-necessary').addEventListener('click', function () {
    save({ necessary: true, analytics: false, marketing: false, all: false });
  });
  document.getElementById('ipcn-cookie-manage').addEventListener('click', function () {
    panel.classList.toggle('open');
  });
  document.getElementById('ipcn-cookie-save').addEventListener('click', function () {
    save({
      necessary: true,
      analytics: document.getElementById('ipcn-cookie-analytics').checked,
      marketing: document.getElementById('ipcn-cookie-marketing').checked,
      all: false
    });
  });
  document.getElementById('ipcn-cookie-accept-all-panel').addEventListener('click', function () {
    save({ necessary: true, analytics: true, marketing: true, all: true });
  });

  if (!consented()) {
    setTimeout(showBar, 600);
  }
})();
</script>
		<?php
	}
);
