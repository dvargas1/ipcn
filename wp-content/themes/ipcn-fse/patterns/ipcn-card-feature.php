<?php
/**
 * Title: Cartão em destaque IPCN
 * Slug: ipcn/card-feature
 * Description: Destaque do IPCN — a mesma composição do cartão compacto, com imagem 4:3 e título em escala de headline.
 * Inserter: false
 *
 * Mesmas casas do `ipcn/card` (imagem, marcador de ausência, etiqueta por superfície,
 * título e data), com dois desvios fixados pela tabela do AD-3: imagem 4:3 e título em
 * escala de headline. Sem consumidor nesta história — a vitrine da Home é a 1.5 (AD-15).
 * A escala de headline não tem classe própria no AD-3: o hook é o modifier
 * `.ipcn-card-feature` no `wp:group`, definido em `style.css`.
 *
 * @package IPCN FSE
 */
?>
<!-- wp:group {"className":"ipcn-card ipcn-card-feature","borderColor":"muted","backgroundColor":"base","style":{"border":{"radius":"14px","width":"1px"}}} -->
<div class="wp-block-group ipcn-card ipcn-card-feature has-border-color has-muted-border-color has-base-background-color has-background" style="border-radius:14px;border-width:1px">
	<!-- wp:group {"className":"ipcn-card-media"} -->
	<div class="wp-block-group ipcn-card-media">
		<!-- wp:html -->
		<span class="ipcn-card-noimg" aria-hidden="true">IPCN</span>
		<!-- /wp:html -->
		<!-- wp:post-featured-image {"isLink":false,"aspectRatio":"4/3","sizeSlug":"medium_large"} /-->
	</div>
	<!-- /wp:group -->
	<!-- wp:post-terms {"term":"category","separator":" · "} /-->
	<!-- wp:post-terms {"term":"tema_acervo","separator":" · "} /-->
	<!-- wp:post-title {"isLink":true,"className":"ipcn-card-title"} /-->
	<!-- wp:post-date {"className":"ipcn-card-date","format":"j \\d\\e M \\d\\e Y"} /-->
</div>
<!-- /wp:group -->
