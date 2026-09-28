<?php
/**
 * Title: Seções do IPCN
 * Slug: ipcn/seccoes
 * Description: Lista curada das Seções do IPCN — as sete categorias editoriais, com os endereços canônicos dos termos.
 * Inserter: false
 *
 * As Seções não estão sob `noticias` na base de dados (só `destaques`), logo não são
 * deriváveis por consulta: esta lista é curada no tema, com os endereços canônicos dos
 * termos, escritos por slug — nunca por id numérico de termo (AD-7). Consumido pelo hub
 * `/noticias/` (`templates/page-noticias.html`). `Inserter: false`, como os cartões: não
 * aparece na inserção, só é referido por slug. O marco é um `nav` com os itens em lista
 * (o `core/list` não aceita `className`, por isso o hook de estilo `ipcn-seccoes-links`
 * vive no `core/group` que o envolve), com alvo de toque de 44px no `style.css`.
 *
 * @package IPCN FSE
 */
?>
<!-- wp:group {"tagName":"nav","className":"ipcn-seccoes","ariaLabel":"Seções do IPCN","style":{"spacing":{"padding":{"top":"40px","bottom":"8px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<nav class="wp-block-group ipcn-seccoes" aria-label="Seções do IPCN" style="padding-top:40px;padding-bottom:8px;padding-left:20px;padding-right:20px">
	<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px","fontWeight":"600","letterSpacing":"0.2em","textTransform":"uppercase"},"color":{"text":"#a85a32"}}} -->
	<p style="color:#a85a32;font-size:12px;font-weight:600;letter-spacing:0.2em;text-transform:uppercase">Seções</p>
	<!-- /wp:paragraph -->
	<!-- wp:heading {"level":2,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"clamp(26px, 3vw, 34px)","fontWeight":"700"},"spacing":{"margin":{"bottom":"18px"}}}} -->
	<h2 class="wp-block-heading" style="font-family:var(--wp--preset--font-family--oswald);font-size:clamp(26px,3vw,34px);font-weight:700;margin-bottom:18px">Explore as seções</h2>
	<!-- /wp:heading -->
	<!-- wp:group {"className":"ipcn-seccoes-links","layout":{"type":"constrained","contentSize":"100%"}} -->
	<div class="wp-block-group ipcn-seccoes-links">
		<!-- wp:list -->
		<ul class="wp-block-list">
			<!-- wp:list-item -->
			<li><a href="/category/noticias/destaques/">Destaques</a></li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><a href="/category/diaspora/">Diáspora</a></li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><a href="/category/colunistas/">Colunistas</a></li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><a href="/category/notas/">Notas</a></li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><a href="/category/editorial/">Editorial</a></li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><a href="/category/drops-antirracista/">Drops Antirracista</a></li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><a href="/category/memorias/">Memórias</a></li>
			<!-- /wp:list-item -->
		</ul>
		<!-- /wp:list -->
	</div>
	<!-- /wp:group -->
</nav>
<!-- /wp:group -->
