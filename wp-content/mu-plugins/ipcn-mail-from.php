<?php
/**
 * From padrao dos emails do site: contato@ipcnbrasil.org com nome IPCN.
 * Motivo: PHPMailer usava wordpress@staging.ipcnbrasil.org (sem SPF no subdominio) e o
 * Gmail rejeitava/spamava os envios do form Associe-se. Fix 11/09/2026.
 * Validado: 4 e-mails de teste chegaram no Gmail do Daniel (13:42-13:43).
 */
add_filter(
	'wp_mail_from',
	function () {
		return 'contato@ipcnbrasil.org';
	}
);
add_filter(
	'wp_mail_from_name',
	function () {
		return 'IPCN - Instituto de Pesquisas das Culturas Negras';
	}
);
