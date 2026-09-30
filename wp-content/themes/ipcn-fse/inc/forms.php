<?php
/**
 * IPCN FSE — formularios: handlers admin_post e shortcodes dos forms e mensagens.
 *
 * Um so dono para as duas superficies de contacto (AD-11). O handler valida, verifica a
 * origem sem guardar estado e — ao rejeitar — guarda os valores submetidos num transient
 * de vida curta indexado por um token que viaja no URL do redirect. O shortcode do
 * formulario le o token, re-preenche os campos, escreve o resumo de erro no topo (com
 * foco) e liga cada erro ao campo por `aria-invalid`/`aria-describedby`; o token e
 * apagado no primeiro uso. O `[ipcn_assoc_message]`/`[ipcn_contact_message]` mantem a
 * confirmacao de sucesso e deixam de escrever o erro, para a mesma falha nao aparecer
 * duas vezes.
 *
 * Sem nonce de proposito: o LiteSpeed serve HTML em cache e um nonce em pagina cacheada
 * quebra o formulario (NFR7). A proteccao real e honeypot + verificacao de origem, e um
 * cabecalho ausente nao bloqueia pessoas reais. Nenhum envio cria utilizador.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Diz se a submissao vem do proprio site, sem guardar estado (NFR7).
 *
 * Aceita quando `Origin` e `Referer` vem ambos ausentes (uma politica de privacidade do
 * browser pode retira-los) e rejeita quando algum vem presente e nao corresponde ao host
 * de `home_url()`. `Origin: null` e um cabecalho presente que nao corresponde — rejeita.
 * A comparacao e so pelo host, de forma insensivel a maiusculas.
 *
 * @return bool Verdadeiro quando a submissao pode seguir.
 */
function ipcn_fse_origin_allowed() {
	$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( ! is_string( $site_host ) || '' === $site_host ) {
		// Sem host de referencia nao ha como verificar; nao se bloqueia uma pessoa real.
		return true;
	}
	$site_host = strtolower( $site_host );

	foreach ( array( 'HTTP_ORIGIN', 'HTTP_REFERER' ) as $header ) {
		if ( ! isset( $_SERVER[ $header ] ) ) {
			continue;
		}
		$value = trim( (string) $_SERVER[ $header ] );
		if ( '' === $value ) {
			continue;
		}
		$host = wp_parse_url( $value, PHP_URL_HOST );
		if ( ! is_string( $host ) || strtolower( $host ) !== $site_host ) {
			return false;
		}
	}

	return true;
}

/**
 * Chave do transient de retencao para um token.
 *
 * O nome leva prefixo para ninguem ler o transient de outro formulario.
 *
 * @param string $token Token opaco.
 * @return string
 */
function ipcn_fse_retention_key( $token ) {
	return 'ipcn_frm_' . $token;
}

/**
 * Guarda os valores submetidos no transient de vida curta e devolve o token.
 *
 * O token e opaco (hex de 16 bytes) e viaja no URL do redirect; a fronteira de entrada e
 * o `sanitize_key()` na leitura, reforcado por um `preg_match` sobre o formato. Guarda os
 * valores submetidos *e* a lista de campos invalidos, porque o resumo precisa de dizer o
 * que falta. Um token de uso unico impede o URL de continuar a transportar dados pessoais.
 *
 * @param string $tipo    'assoc' ou 'contact'.
 * @param array  $values  Valores submetidos, ja sanitizados.
 * @param array  $invalid Nomes dos campos em falta/invalidos.
 * @return string Token gerado.
 */
function ipcn_fse_store_retention( $tipo, $values, $invalid ) {
	$token   = bin2hex( random_bytes( 16 ) );
	$payload = array(
		'tipo'    => (string) $tipo,
		'values'  => (array) $values,
		'invalid' => array_values( array_unique( array_map( 'strval', (array) $invalid ) ) ),
	);

	set_transient( ipcn_fse_retention_key( $token ), $payload, 15 * MINUTE_IN_SECONDS );

	return $token;
}

/**
 * Le a retencao do envio falhado do proprio formulario e apaga-a no primeiro uso.
 *
 * `sanitize_key()` na entrada e o `preg_match` sobre o formato fecham a fronteira: um
 * token alheio, expirado ou inventado devolve `null` e nada de alheio entra no HTML. O
 * token de outro formulario nao se consome — fica para o seu dono — e o do proprio e
 * apagado no primeiro uso, para o URL deixar de transportar dados pessoais.
 *
 * @param string $tipo 'assoc' ou 'contact'.
 * @return array|null Payload `{tipo, values, invalid}` ou `null`.
 */
function ipcn_fse_retention( $tipo ) {
	$token = isset( $_GET['ipcn_token'] ) ? sanitize_key( wp_unslash( $_GET['ipcn_token'] ) ) : '';
	if ( ! preg_match( '/^[a-f0-9]{32}$/', $token ) ) {
		return null;
	}

	$payload = get_transient( ipcn_fse_retention_key( $token ) );

	if ( ! is_array( $payload ) || ! isset( $payload['tipo'] ) || (string) $payload['tipo'] !== (string) $tipo ) {
		return null;
	}

	delete_transient( ipcn_fse_retention_key( $token ) );

	return $payload;
}

/**
 * Valores e campos invalidos retidos para um formulario, se o token for dele.
 *
 * @param string $tipo 'assoc' ou 'contact'.
 * @return array{values:array,invalid:array}
 */
function ipcn_fse_retained_values( $tipo ) {
	$vazio   = array(
		'values'  => array(),
		'invalid' => array(),
	);
	$payload = ipcn_fse_retention( $tipo );

	if ( ! is_array( $payload ) ) {
		return $vazio;
	}

	return array(
		'values'  => ( isset( $payload['values'] ) && is_array( $payload['values'] ) ) ? $payload['values'] : array(),
		'invalid' => ( isset( $payload['invalid'] ) && is_array( $payload['invalid'] ) ) ? $payload['invalid'] : array(),
	);
}

/**
 * Cabecalho `Reply-To` com o nome e o e-mail submetidos, sem cabecalho injectado.
 *
 * O nome ja vem de `sanitize_text_field()` e o e-mail de `sanitize_email()`; ainda assim
 * removem-se os caracteres que permitem partir o cabecalho (`\r`, `\n`, `<`, `>`).
 *
 * @param string $nome  Nome submetido.
 * @param string $email E-mail submetido.
 * @return string
 */
function ipcn_fse_reply_to( $nome, $email ) {
	$nome = str_replace( array( "\r", "\n", '%0a', '%0d', '<', '>' ), '', (string) $nome );
	$nome = trim( $nome );

	if ( '' === $nome ) {
		return 'Reply-To: ' . $email;
	}

	return 'Reply-To: ' . $nome . ' <' . $email . '>';
}

/**
 * Resumo de erro do topo: `role="alert"` (anunciado) e `tabindex="-1"` (recebe o foco).
 *
 * Aparece sempre que o argumento de consulta traz `erro` — tambem no honeypot, numa
 * rejeicao de origem e num ida-e-volta sem retencao — para nunca haver silencio. A frase
 * que promete que os dados continuam guardados so sai quando ha mesmo valores retidos:
 * sem retencao valida diz-se apenas que o envio falhou. A cor acompanha sempre uma palavra.
 *
 * @param array $invalid     Nomes dos campos em falta/invalidos.
 * @param bool  $tem_valores Se ha valores retidos para re-preencher.
 * @return string
 */
function ipcn_fse_error_summary( $invalid = array(), $tem_valores = false ) {
	$titulo = $tem_valores
		? 'Não foi possível enviar agora. Seus dados continuam aqui — tente de novo.'
		: 'Não foi possível enviar agora. Tente de novo.';

	$summary = '<div class="ipcn-form-error" role="alert" tabindex="-1">'
		. '<p class="ipcn-form-error-title">' . esc_html( $titulo ) . '</p>';

	if ( ! empty( $invalid ) ) {
		$labels = array(
			'ipcn_nome'  => 'Nome',
			'ipcn_email' => 'E-mail',
			'ipcn_tel'   => 'Telefone',
			'ipcn_msg'   => 'Mensagem',
		);
		$names  = array();
		foreach ( $invalid as $field ) {
			if ( isset( $labels[ $field ] ) ) {
				$names[] = $labels[ $field ];
			}
		}
		if ( ! empty( $names ) ) {
			$summary .= '<p class="ipcn-form-error-fields">Revise: ' . esc_html( implode( ', ', $names ) ) . '.</p>';
		}
	}

	return $summary . '</div>';
}

/**
 * Handler do pedido de associacao (`ipcn_assoc`).
 *
 * Contrato AD-11: campos `ipcn_nome`, `ipcn_email`, `ipcn_tel` e o honeypot `ipcn_hp`;
 * destino unico `contato@ipcnbrasil.org`; redirect para `/associe-se/` com `cadastro`.
 */
add_action( 'admin_post_ipcn_assoc', 'ipcn_fse_handle_assoc' );
add_action( 'admin_post_nopriv_ipcn_assoc', 'ipcn_fse_handle_assoc' );

function ipcn_fse_handle_assoc() {
	$redirect = home_url( '/associe-se/' );

	// Honeypot preenchido = bot. Mesmo estado de erro, nunca silencio; sem retencao.
	if ( ! empty( $_POST['ipcn_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'cadastro', 'erro', $redirect ) );
		exit;
	}

	// Origem de outro host (ou `Origin: null`) rejeita sem guardar nada.
	if ( ! ipcn_fse_origin_allowed() ) {
		wp_safe_redirect( add_query_arg( 'cadastro', 'erro', $redirect ) );
		exit;
	}

	$nome  = isset( $_POST['ipcn_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['ipcn_nome'] ) ) : '';
	$email = isset( $_POST['ipcn_email'] ) ? sanitize_email( wp_unslash( $_POST['ipcn_email'] ) ) : '';
	$tel   = isset( $_POST['ipcn_tel'] ) ? sanitize_text_field( wp_unslash( $_POST['ipcn_tel'] ) ) : '';

	$invalid = array();
	if ( '' === $nome ) {
		$invalid[] = 'ipcn_nome';
	}
	if ( '' === $email || ! is_email( $email ) ) {
		$invalid[] = 'ipcn_email';
	}

	$values = array(
		'ipcn_nome'  => $nome,
		'ipcn_email' => $email,
		'ipcn_tel'   => $tel,
	);

	if ( ! empty( $invalid ) ) {
		$token = ipcn_fse_store_retention( 'assoc', $values, $invalid );
		wp_safe_redirect(
			add_query_arg(
				array(
					'cadastro'   => 'erro',
					'ipcn_token' => $token,
				),
				$redirect
			)
		);
		exit;
	}

	$to      = 'contato@ipcnbrasil.org';
	$subject = 'Novo pedido de associação pelo site - IPCN';
	$body    = "Novo pedido de associacao pelo site:\n\n"
		. "Nome: {$nome}\n"
		. "E-mail: {$email}\n"
		. "Telefone: {$tel}\n\n"
		. 'Enviado em ' . current_time( 'd/m/Y H:i' );
	$headers = array( ipcn_fse_reply_to( $nome, $email ) );

	$sent = wp_mail( $to, $subject, $body, $headers );

	// O correio falhou: o pedido fica retido e a pessoa reenvia sem reescrever nada (FR-8).
	if ( ! $sent ) {
		$token = ipcn_fse_store_retention( 'assoc', $values, array() );
		wp_safe_redirect(
			add_query_arg(
				array(
					'cadastro'   => 'erro',
					'ipcn_token' => $token,
				),
				$redirect
			)
		);
		exit;
	}

	wp_safe_redirect( add_query_arg( 'cadastro', 'ok', $redirect ) );
	exit;
}

/**
 * Handler do Fale Conosco (`ipcn_contact`).
 *
 * Contrato AD-11: campos `ipcn_nome`, `ipcn_email`, `ipcn_msg` e o honeypot `ipcn_hp`;
 * destino unico `contato@ipcnbrasil.org`; redirect para `/fale-conosco/` com `contato`.
 * O assunto distingue-se do pedido de associacao.
 */
add_action( 'admin_post_ipcn_contact', 'ipcn_fse_handle_contact' );
add_action( 'admin_post_nopriv_ipcn_contact', 'ipcn_fse_handle_contact' );

function ipcn_fse_handle_contact() {
	$redirect = home_url( '/fale-conosco/' );

	if ( ! empty( $_POST['ipcn_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'contato', 'erro', $redirect ) );
		exit;
	}

	if ( ! ipcn_fse_origin_allowed() ) {
		wp_safe_redirect( add_query_arg( 'contato', 'erro', $redirect ) );
		exit;
	}

	$nome  = isset( $_POST['ipcn_nome'] ) ? sanitize_text_field( wp_unslash( $_POST['ipcn_nome'] ) ) : '';
	$email = isset( $_POST['ipcn_email'] ) ? sanitize_email( wp_unslash( $_POST['ipcn_email'] ) ) : '';
	$msg   = isset( $_POST['ipcn_msg'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ipcn_msg'] ) ) : '';

	$invalid = array();
	if ( '' === $nome ) {
		$invalid[] = 'ipcn_nome';
	}
	if ( '' === $email || ! is_email( $email ) ) {
		$invalid[] = 'ipcn_email';
	}
	if ( '' === $msg ) {
		$invalid[] = 'ipcn_msg';
	}

	$values = array(
		'ipcn_nome'  => $nome,
		'ipcn_email' => $email,
		'ipcn_msg'   => $msg,
	);

	if ( ! empty( $invalid ) ) {
		$token = ipcn_fse_store_retention( 'contact', $values, $invalid );
		wp_safe_redirect(
			add_query_arg(
				array(
					'contato'    => 'erro',
					'ipcn_token' => $token,
				),
				$redirect
			)
		);
		exit;
	}

	$to      = 'contato@ipcnbrasil.org';
	$subject = 'Nova mensagem pelo site (Fale conosco) - IPCN';
	$body    = "Mensagem enviada pelo Fale Conosco:\n\n"
		. "Nome: {$nome}\n"
		. "E-mail: {$email}\n\n"
		. $msg . "\n\n"
		. 'Enviado em ' . current_time( 'd/m/Y H:i' );
	$headers = array( ipcn_fse_reply_to( $nome, $email ) );

	$sent = wp_mail( $to, $subject, $body, $headers );

	// O correio falhou: a mensagem fica retida e a pessoa reenvia sem reescrever nada (FR-9).
	if ( ! $sent ) {
		$token = ipcn_fse_store_retention( 'contact', $values, array() );
		wp_safe_redirect(
			add_query_arg(
				array(
					'contato'    => 'erro',
					'ipcn_token' => $token,
				),
				$redirect
			)
		);
		exit;
	}

	wp_safe_redirect( add_query_arg( 'contato', 'ok', $redirect ) );
	exit;
}

/**
 * Script minimo do formulario, impresso com o proprio formulario.
 *
 * Sai com o markup do formulario, e nao num segundo `wp_footer` — esse hook ja e de
 * `inc/cookie-bar.php`, e nenhum ficheiro de `inc/` regista o mesmo hook que outro (AD-4).
 * Foca o resumo de erro (que tem `tabindex="-1"`) e desactiva o botao em envio, para nao
 * haver segundo envio por impaciencia. Nao emite `<style>` nenhum: o desenho vive no
 * `style.css` (AD-5). Sai uma so vez, mesmo que a pagina tivesse os dois formularios. Voltar
 * pelo historico (bfcache) devolve o botao ao estado normal, para o formulario nao ficar
 * preso a dizer que esta a enviar.
 *
 * @return string
 */
function ipcn_fse_form_script() {
	static $printed = false;

	if ( $printed ) {
		return '';
	}
	$printed = true;

	return <<<'HTML'
<script>
(function () {
  var resumos = document.querySelectorAll('.ipcn-form-error[role="alert"]');
  for (var i = 0; i < resumos.length; i++) { resumos[i].focus(); }
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || !form.classList || !form.classList.contains('ipcn-form')) { return; }
    var botao = form.querySelector('.ipcn-form-submit');
    if (!botao) { return; }
    if (!botao.dataset.rotulo) { botao.dataset.rotulo = botao.textContent; }
    botao.disabled = true;
    botao.textContent = 'Enviando…';
  });
  window.addEventListener('pageshow', function (e) {
    if (!e.persisted) { return; }
    var botoes = document.querySelectorAll('.ipcn-form-submit');
    for (var i = 0; i < botoes.length; i++) {
      botoes[i].disabled = false;
      if (botoes[i].dataset.rotulo) { botoes[i].textContent = botoes[i].dataset.rotulo; }
    }
  });
})();
</script>
HTML;
}

/**
 * Form de associado (usado na pagina `/associe-se/`).
 *
 * Le o token do redirect, re-preenche os campos, escreve o resumo com foco e liga cada
 * erro ao seu campo. Cada campo tem etiqueta visivel e associada e `autocomplete`; o
 * honeypot e inalcancavel por teclado (`tabindex="-1"`) e invisivel a leitores de ecra
 * (`aria-hidden="true"`). O markup usa as classes do tema (AD-1, excepcao 2).
 */
add_shortcode(
	'ipcn_assoc_form',
	function () {
		$retained = ipcn_fse_retained_values( 'assoc' );
		$values   = $retained['values'];
		$invalid  = $retained['invalid'];
		$status   = isset( $_GET['cadastro'] ) ? sanitize_key( wp_unslash( $_GET['cadastro'] ) ) : '';

		$nome  = isset( $values['ipcn_nome'] ) ? (string) $values['ipcn_nome'] : '';
		$email = isset( $values['ipcn_email'] ) ? (string) $values['ipcn_email'] : '';
		$tel   = isset( $values['ipcn_tel'] ) ? (string) $values['ipcn_tel'] : '';

		$nome_invalid  = in_array( 'ipcn_nome', $invalid, true );
		$email_invalid = in_array( 'ipcn_email', $invalid, true );

		$out = '<div class="ipcn-form-card">';
		if ( 'erro' === $status ) {
			$out .= ipcn_fse_error_summary( $invalid, ! empty( $values ) );
		}

		$out .= '<form class="ipcn-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
			. '<input type="hidden" name="action" value="ipcn_assoc">'
			. '<input type="text" name="ipcn_hp" class="ipcn-hp" value="" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<label for="ipcn-assoc-nome">Nome completo *</label>'
			. '<input type="text" name="ipcn_nome" id="ipcn-assoc-nome" autocomplete="name" value="' . esc_attr( $nome ) . '"' . ( $nome_invalid ? ' aria-invalid="true" aria-describedby="ipcn-assoc-nome-erro"' : '' ) . ' required>'
			. ( $nome_invalid ? '<span class="ipcn-form-field-erro" id="ipcn-assoc-nome-erro">Informe seu nome.</span>' : '' )
			. '<label for="ipcn-assoc-email">E-mail *</label>'
			. '<input type="email" name="ipcn_email" id="ipcn-assoc-email" autocomplete="email" value="' . esc_attr( $email ) . '"' . ( $email_invalid ? ' aria-invalid="true" aria-describedby="ipcn-assoc-email-erro"' : '' ) . ' required>'
			. ( $email_invalid ? '<span class="ipcn-form-field-erro" id="ipcn-assoc-email-erro">Informe um e-mail válido.</span>' : '' )
			. '<label for="ipcn-assoc-tel">Telefone</label>'
			. '<input type="tel" name="ipcn_tel" id="ipcn-assoc-tel" autocomplete="tel" value="' . esc_attr( $tel ) . '">'
			. '<button type="submit" class="ipcn-form-submit">Enviar pedido</button>'
			. '</form>' . ipcn_fse_form_script() . '</div>';

		return $out;
	}
);

/**
 * Form de Fale Conosco (usado na pagina `/fale-conosco/`).
 */
add_shortcode(
	'ipcn_contact_form',
	function () {
		$retained = ipcn_fse_retained_values( 'contact' );
		$values   = $retained['values'];
		$invalid  = $retained['invalid'];
		$status   = isset( $_GET['contato'] ) ? sanitize_key( wp_unslash( $_GET['contato'] ) ) : '';

		$nome  = isset( $values['ipcn_nome'] ) ? (string) $values['ipcn_nome'] : '';
		$email = isset( $values['ipcn_email'] ) ? (string) $values['ipcn_email'] : '';
		$msg   = isset( $values['ipcn_msg'] ) ? (string) $values['ipcn_msg'] : '';

		$nome_invalid  = in_array( 'ipcn_nome', $invalid, true );
		$email_invalid = in_array( 'ipcn_email', $invalid, true );
		$msg_invalid   = in_array( 'ipcn_msg', $invalid, true );

		$out = '<div class="ipcn-form-card">';
		if ( 'erro' === $status ) {
			$out .= ipcn_fse_error_summary( $invalid, ! empty( $values ) );
		}

		$out .= '<form class="ipcn-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
			. '<input type="hidden" name="action" value="ipcn_contact">'
			. '<input type="text" name="ipcn_hp" class="ipcn-hp" value="" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<label for="ipcn-contato-nome">Nome *</label>'
			. '<input type="text" name="ipcn_nome" id="ipcn-contato-nome" autocomplete="name" value="' . esc_attr( $nome ) . '"' . ( $nome_invalid ? ' aria-invalid="true" aria-describedby="ipcn-contato-nome-erro"' : '' ) . ' required>'
			. ( $nome_invalid ? '<span class="ipcn-form-field-erro" id="ipcn-contato-nome-erro">Informe seu nome.</span>' : '' )
			. '<label for="ipcn-contato-email">E-mail *</label>'
			. '<input type="email" name="ipcn_email" id="ipcn-contato-email" autocomplete="email" value="' . esc_attr( $email ) . '"' . ( $email_invalid ? ' aria-invalid="true" aria-describedby="ipcn-contato-email-erro"' : '' ) . ' required>'
			. ( $email_invalid ? '<span class="ipcn-form-field-erro" id="ipcn-contato-email-erro">Informe um e-mail válido.</span>' : '' )
			. '<label for="ipcn-contato-msg">Mensagem *</label>'
			. '<textarea name="ipcn_msg" id="ipcn-contato-msg"' . ( $msg_invalid ? ' aria-invalid="true" aria-describedby="ipcn-contato-msg-erro"' : '' ) . ' required>' . esc_textarea( $msg ) . '</textarea>'
			. ( $msg_invalid ? '<span class="ipcn-form-field-erro" id="ipcn-contato-msg-erro">Escreva sua mensagem.</span>' : '' )
			. '<button type="submit" class="ipcn-form-submit">Enviar mensagem</button>'
			. '</form>' . ipcn_fse_form_script() . '</div>';

		return $out;
	}
);

/**
 * Confirmacao de sucesso do Fale Conosco (o erro e do formulario).
 */
add_shortcode(
	'ipcn_contact_message',
	function () {
		$status = isset( $_GET['contato'] ) ? sanitize_key( wp_unslash( $_GET['contato'] ) ) : '';
		if ( 'ok' === $status ) {
			return '<div class="ipcn-form-success" role="status">Recebemos sua mensagem. Vamos responder pelo e-mail informado.</div>';
		}
		return '';
	}
);

/**
 * Confirmacao de sucesso do form de associado (o erro e do formulario).
 */
add_shortcode(
	'ipcn_assoc_message',
	function () {
		$status = isset( $_GET['cadastro'] ) ? sanitize_key( wp_unslash( $_GET['cadastro'] ) ) : '';
		if ( 'ok' === $status ) {
			return '<div class="ipcn-form-success" role="status">Recebemos seu pedido. Vamos responder pelo e-mail informado.</div>';
		}
		return '';
	}
);
