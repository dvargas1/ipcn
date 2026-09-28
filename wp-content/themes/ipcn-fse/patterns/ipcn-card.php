<?php
/**
 * Title: Cartão IPCN
 * Slug: ipcn/card
 * Description: Cartão compacto do IPCN — imagem 16:9 (ou marcador IPCN), etiqueta por superfície, título e data.
 * Inserter: false
 *
 * As casas são só as da tabela do AD-3: imagem 16:9, marcador de ausência, etiqueta
 * (`category` em Notícias/Secção/Home; `tema_acervo` no Acervo e no Tema — sai a que
 * existir), título e data. Sem excerto. Sem PHP dependente do loop, do post type
 * ou do pedido: o contexto do post vem do `core/post-template`, ou do wrapper
 * `[ipcn_query_posts]`, quando o cartão é usado.
 *
 * @package IPCN FSE
 */
?>
<!-- wp:group {"className":"ipcn-card","borderColor":"muted","backgroundColor":"base","style":{"border":{"radius":"14px","width":"1px"}}} -->
<div class="wp-block-group ipcn-card has-border-color has-muted-border-color has-base-background-color has-background" style="border-radius:14px;border-width:1px">
	<!-- wp:group {"className":"ipcn-card-media"} -->
	<div class="wp-block-group ipcn-card-media">
		<!-- wp:html -->
		<span class="ipcn-card-noimg" aria-hidden="true">IPCN</span>
		<!-- /wp:html -->
		<!-- wp:post-featured-image {"isLink":false,"aspectRatio":"16/9","sizeSlug":"medium_large"} /-->
	</div>
	<!-- /wp:group -->
	<!-- wp:post-terms {"term":"category","separator":" · "} /-->
	<!-- wp:post-terms {"term":"tema_acervo","separator":" · "} /-->
	<!-- wp:html -->
	<span class="ipcn-card-notema">Sem Tema</span>
	<!-- /wp:html -->
	<!-- wp:post-title {"isLink":true,"className":"ipcn-card-title"} /-->
	<!-- wp:post-date {"className":"ipcn-card-date","format":"j \\d\\e M \\d\\e Y"} /-->
</div>
<!-- /wp:group -->
