<?php
/**
 * IPCN FSE — barra de cookies, painel de preferencias e JavaScript do wp_footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
