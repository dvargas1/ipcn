<?php
/**
 * IPCN FSE — funcoes do tema.
 *
 * Carregador: cada preocupacao vive num ficheiro de `inc/`. Os `require_once` resolvem-se
 * com `__DIR__`, incluem cada ficheiro uma so vez e falham alto se um faltar. A ordem e
 * explicita e vinculativa:
 * setup (fontes, preconnect, style.css) -> content-model (CPT + taxonomia) -> listings ->
 * page-hero (cabecalho de pagina) -> pix (superficie Apoia-se e QR de PIX) -> forms ->
 * cookie-bar -> agenda-block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/content-model.php';
require_once __DIR__ . '/inc/listings.php';
require_once __DIR__ . '/inc/page-hero.php';
require_once __DIR__ . '/inc/pix.php';
require_once __DIR__ . '/inc/forms.php';
require_once __DIR__ . '/inc/cookie-bar.php';
require_once __DIR__ . '/inc/agenda-block.php';
