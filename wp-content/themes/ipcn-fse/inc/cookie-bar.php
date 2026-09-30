<?php
/**
 * IPCN FSE — barra de cookies, painel de preferencias e JavaScript do wp_footer.
 *
 * A barra aparece so sem escolha registada, tem fundo navy opaco (sem `backdrop-filter`),
 * as tres accoes usam o mesmo tratamento `ipcn-cookie-action` e nenhuma categoria vem
 * pre-seleccionada (FR-11). A escolha persiste em `localStorage['ipcn_cookie_consent_v1']`
 * com o objecto `{ts, necessary, analytics, marketing, all}`, vale 180 dias e e revista por
 * um controlo do rodape. Ha afastamento de scroll reservado (a altura real da barra, medida
 * em tempo de execucao) e a barra cede o passo ao foco do teclado (achado F-16). Todo o
 * desenho vive no `style.css` (AD-5): nenhum `style` inline sai daqui.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_footer',
	function () {
		// So no frontend, sem painel admin.
		if ( is_admin() ) {
			return;
		}
		$policy = esc_url( home_url( '/politica-de-privacidade/' ) );
		?>
<div id="ipcn-cookie-bar" role="region" aria-label="Aviso de cookies">
  <p class="ipcn-cookie-text">Usamos cookies para melhorar sua experiência no Portal do IPCN. Ao continuar, você concorda com nossa <a href="<?php echo $policy; ?>">Política de Privacidade</a>.</p>
  <div class="ipcn-cookie-bar-actions">
    <button type="button" class="ipcn-cookie-btn ipcn-cookie-action" id="ipcn-cookie-accept">Aceitar todos</button>
    <button type="button" class="ipcn-cookie-btn ipcn-cookie-action" id="ipcn-cookie-manage">Gerenciar preferências</button>
    <button type="button" class="ipcn-cookie-btn ipcn-cookie-action" id="ipcn-cookie-necessary">Só os necessários</button>
  </div>
</div>

<div id="ipcn-cookie-panel" role="dialog" aria-label="Preferências de cookies" tabindex="-1">
  <div class="ipcn-cookie-panel-inner">
    <h3 class="ipcn-cookie-panel-title">Preferências de cookies</h3>
    <p class="ipcn-cookie-panel-intro">Escolha quais categorias de cookies aceitar. Cookies necessários não podem ser desativados.</p>
    <table>
      <thead><tr><th>Categoria</th><th>Para que serve</th><th>Status</th></tr></thead>
      <tbody>
        <tr><td>Necessários</td><td>Login, segurança, preferências de navegação. Sem eles o site não funciona.</td><td><strong>Sempre ativos</strong></td></tr>
        <tr><td>Analytics</td><td>Google Analytics: medição de tráfego anônima para melhorar o conteúdo.</td><td><label class="ipcn-cookie-check"><input type="checkbox" id="ipcn-cookie-analytics"> Permitir</label></td></tr>
        <tr><td>Marketing / Terceiros</td><td>Integrações de vídeo e redes sociais. Nenhum dado é vendido.</td><td><label class="ipcn-cookie-check"><input type="checkbox" id="ipcn-cookie-marketing"> Permitir</label></td></tr>
      </tbody>
    </table>
    <div class="ipcn-cookie-actions">
      <button type="button" class="ipcn-cookie-btn ipcn-cookie-action" id="ipcn-cookie-save">Salvar preferências</button>
      <button type="button" class="ipcn-cookie-btn ipcn-cookie-action" id="ipcn-cookie-accept-all-panel">Aceitar todos</button>
    </div>
  </div>
</div>

<script>
(function () {
  var KEY = 'ipcn_cookie_consent_v1';
  var TTL = 180 * 24 * 60 * 60 * 1000;
  var bar = document.getElementById('ipcn-cookie-bar');
  var panel = document.getElementById('ipcn-cookie-panel');
  if (!bar || !panel) return;

  var analytics = document.getElementById('ipcn-cookie-analytics');
  var marketing = document.getElementById('ipcn-cookie-marketing');
  var revisit = document.getElementById('ipcn-cookie-revisit');

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function boxOn(box) { return !!(box && box.checked); }
  function setBox(box, on) { if (box) { box.checked = !!on; } }

  function readConsent() {
    var raw;
    try { raw = localStorage.getItem(KEY); } catch (e) { return null; }
    if (!raw) return null;
    var obj;
    try { obj = JSON.parse(raw); } catch (e) { return null; }
    if (!obj || typeof obj !== 'object' || typeof obj.ts !== 'number') return null;
    if ((Date.now() - obj.ts) > TTL) {
      try { localStorage.removeItem(KEY); } catch (e) {}
      return null;
    }
    return obj;
  }

  function reserve() {
    document.documentElement.style.setProperty('--ipcn-cookie-bar-height', bar.offsetHeight + 'px');
  }
  function release() {
    document.documentElement.style.setProperty('--ipcn-cookie-bar-height', '0px');
  }

  function showBar() { bar.classList.add('is-visible'); reserve(); }
  function hideBar() {
    bar.classList.remove('is-visible');
    bar.classList.remove('is-yielding');
    release();
  }

  var opener = null;

  function openPanel(from) {
    // Sincroniza as caixas com a escolha guardada; sem escolha, ficam ambas vazias.
    var choice = readConsent();
    setBox(analytics, choice ? choice.analytics : false);
    setBox(marketing, choice ? choice.marketing : false);
    opener = from || null;
    panel.classList.add('open');
    // Levar o foco para dentro do dialogo: quem usa teclado ouve que ele abriu.
    panel.focus();
  }
  function closePanel() {
    panel.classList.remove('open');
    // Devolver o foco a quem abriu, para nao se perder o lugar no teclado.
    if (opener && opener.focus) { opener.focus(); opener = null; }
  }

  function save(cons) {
    var payload = { ts: Date.now(), necessary: true, analytics: !!cons.analytics, marketing: !!cons.marketing, all: !!cons.all };
    try { localStorage.setItem(KEY, JSON.stringify(payload)); } catch (e) {}
    hideBar();
    closePanel();
    // Sincroniza o CookieYes legado (se activo) tocando o botao de aceite interno.
    var cy = document.querySelector('#cookie-law-info-bar .wt-cli-accept-all-btn');
    if (cy && payload.all) { cy.click(); }
  }

  var accept = document.getElementById('ipcn-cookie-accept');
  var necessary = document.getElementById('ipcn-cookie-necessary');
  var manage = document.getElementById('ipcn-cookie-manage');
  var saveBtn = document.getElementById('ipcn-cookie-save');
  var acceptAllPanel = document.getElementById('ipcn-cookie-accept-all-panel');

  if (accept) accept.addEventListener('click', function () { save({ analytics: true, marketing: true, all: true }); });
  if (necessary) necessary.addEventListener('click', function () { save({ analytics: false, marketing: false, all: false }); });
  if (manage) manage.addEventListener('click', function (e) { panel.classList.contains('open') ? closePanel() : openPanel(e.currentTarget); });
  if (saveBtn) saveBtn.addEventListener('click', function () { save({ analytics: boxOn(analytics), marketing: boxOn(marketing), all: false }); });
  if (acceptAllPanel) acceptAllPanel.addEventListener('click', function () { save({ analytics: true, marketing: true, all: true }); });
  if (revisit) revisit.addEventListener('click', function (e) { openPanel(e.currentTarget); });

  // O painel fecha com Escape e devolve o foco — sem obrigar a gravar uma escolha.
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && panel.classList.contains('open')) { closePanel(); }
  });

  // A barra cede o passo ao foco: desloca-se enquanto um controlo por tras dela (fora da
  // propria barra e do painel) tem o foco, e volta quando o foco sai.
  document.addEventListener('focusin', function (e) {
    if (!bar.classList.contains('is-visible')) return;
    if (bar.contains(e.target) || panel.contains(e.target)) { bar.classList.remove('is-yielding'); return; }
    var r = e.target.getBoundingClientRect();
    if (r.bottom > (window.innerHeight - bar.offsetHeight)) {
      bar.classList.add('is-yielding');
    } else {
      bar.classList.remove('is-yielding');
    }
  });
  document.addEventListener('focusout', function () { bar.classList.remove('is-yielding'); });
  window.addEventListener('resize', function () {
    if (bar.classList.contains('is-visible')) reserve();
  });

  if (!readConsent()) {
    if (reduce) { showBar(); } else { setTimeout(showBar, 600); }
  }
})();
</script>
		<?php
	}
);
