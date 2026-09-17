# RELATÓRIO DE AUDITORIA — IPCN Redesign FSE

> Auditoria técnica + visão de PO. Base: commit `4bd4326` (17/09/2026), STATUS.md, ROADMAP.md, sketches aprovados, `plano-tema-custom-e-portal-associados.md`, código do tema `ipcn-fse` v0.2.0 e mu-plugins.

---

## 1. Diagnóstico Executivo

**O que está bom:**
- `theme.json` com tokens de design limpos e consistentes (paleta navy/ocre/ink/muted/base, tipografia Oswald+Inter+Playfair, spacing, radius, shadows)
- Forms nativos funcionais (Associe-se e Fale Conosco via `admin-post.php` + honeypot, testados com `wp_mail` real)
- CPT `acervo_ipcn` + taxonomia `tema_acervo` bem registrados, com `capability_type` provisionado para Fase 4
- Template `single.html` com sistema de leitura de longo formato profissional (`.ipcn-prose`, 720px, Playfair para headings, blockquotes com borda terracota)
- Cookie bar própria com painel de gerenciamento real (toggles por categoria, localStorage, fallback defensivo do CookieYes)
- Acessibilidade WCAG AA resolvida (ocre como fundo de botão com texto chumbo 7.7:1, `focus-visible` terracota)

**O que está travando a qualidade e precisa de refatoração imediata:**

| # | Problema | Severidade | Impacto |
|---|----------|------------|---------|
| 1 | **Header não é "centralizado + menu embaixo"** — usa `space-between` (marca esquerda, nav direita). Contratante pediu explicitamente layout centralizado. | Crítico | Desvio de escopo validado pela contratante |
| 2 | **URL de imagem de background hardcoded no CSS** — `style.css:157` aponta para `https://stagingredesign.ipcnbrasil.org/wp-content/uploads/...` | Crítico | Quebra em produção ou qualquer outro ambiente |
| 3 | **Category ID `[1]` hardcoded** no Query Loop de notícias da home | Alto | Quebra se term_id mudar entre ambientes |
| 4 | **Mu-plugin `ipcn-optimizations.php` carrega código morto** — ETmodules fix (Divi), CSS de forms Divi (107 linhas), cache de features Divi. Tudo legado do tema anterior. | Médio | Infla HTML, confusão para manutenção |
| 5 | **Footer usa cor `#8e9bd0` (navy-soft)** não presente nos sketches aprovados | Médio | Desvio visual do design system |
| 6 | **Menu mobile usa overlay full-screen** do WP em vez de dropdown abaixo do header (conforme sketches) | Médio | UX inconsistente com o aprovado |
| 7 | **`taxonomy-tema_acervo.html` duplica `archive-acervo_ipcn.html`** quase 100% | Baixo | Débito técnico, manutenção desnecessária |
| 8 | **`page.html` sem identidade visual** — 7 linhas, sem hero, sem estrutura | Médio | Páginas internas ficam sem identidade |
| 9 | **`theme.json` mistura Playfair como fallback de Oswald** na mesma `fontFamily` | Baixo | Confusão semântica, fontes muito diferentes |
| 10 | **Shortcodes de form sem `id` nos inputs** — labels associados implicitamente | Baixo | Acessibilidade abaixo do ideal |
| 11 | **Front-page tem 5 sections** vs "home enxuta (3 sections)" documentado no STATUS | Médio | Desvio do escopo documentado |
| 12 | **CSS do tema espalhado** — style.css (843 linhas) + 3 blocos inline via `wp_head` no mu-plugin | Médio | Dificulta manutenção e debug |

---

## 2. Plano de Execução (O Backlog)

### Tarefa 1: Corrigir URL de background hardcoded do hero

**Contexto:** `style.css:157` contém `background: url("https://stagingredesign.ipcnbrasil.org/wp-content/uploads/2025/03/1001617239-1080x675.jpg")`. Isso quebra em produção e é um vazamento de ambiente.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/style.css` (linha 157)

**Instruções de Implementação:**
1. Abrir `style.css` e localizar o seletor `.ipcn-hero::before` (linha ~152-158).
2. Substituir a URL absoluta por um caminho relativo ao tema usando `get_template_directory_uri()` — mas como é CSS puro (não PHP), a solução é **remover o `background` do CSS** e mover a imagem de fundo para o markup do template `front-page.html` via inline style no `<div class="ipcn-hero">`, usando PHP ou via `theme.json` styles.
3. **Solução recomendada:** Adicionar a imagem como `background-image` inline no bloco `<!-- wp:group -->` do hero em `front-page.html` (linha 4-5), usando o atributo `style` do bloco:
   ```
   "style":{"background":{"backgroundImage":{"url":"...","size":"cover","position":"center 32%","repeat":"no-repeat"}}}
   ```
   Ou, se a imagem for um attachment do WP, usar `wp_get_attachment_url()`.
4. Remover a regra `.ipcn-hero::before` e `.ipcn-hero::after` do `style.css` (linhas 152-165) e substituir por um gradiente CSS puro (sem URL de imagem) no `::after` apenas.
5. Se a abordagem for manter CSS puro, usar uma **variável CSS custom** definida no `theme.json` ou no `functions.php` via `wp_add_inline_style`:
   ```php
   // No functions.php, dentro do wp_enqueue_scripts existente:
   wp_add_inline_style('ipcn-fse-style', '--ipcn-hero-bg: url("' . esc_url(get_template_directory_uri()) . '/assets/hero-bg.jpg");');
   ```
   E no CSS: `background: var(--ipcn-hero-bg) center 32% / cover no-repeat;`
6. Mover a imagem `1001617239-1080x675.jpg` para `wp-content/themes/ipcn-fse/assets/hero-bg.jpg` (criar pasta `assets/`).

**Critérios de Aceite:**
- [ ] Nenhuma URL absoluta de `stagingredesign.ipcnbrasil.org` em nenhum arquivo do tema
- [ ] Hero da home renderiza a imagem de fundo corretamente em staging E produção
- [ ] `grep -r "stagingredesign" wp-content/themes/ipcn-fse/` retorna 0 resultados

---

### Tarefa 2: Reestruturar header para "centralizado + menu embaixo"

**Contexto:** A contratante pediu explicitamente "header centralizado com menu embaixo" (STATUS.md:15, plano-tema §6.3.5). O header atual usa `flex` com `justifyContent: space-between` — marca à esquerda, nav+CTAs à direita na mesma linha. Isso é um desvio de escopo validado.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/parts/header.html`
- `wp-content/themes/ipcn-fse/style.css` (seções de header, linhas 23-111)

**Instruções de Implementação:**
1. Reestruturar `parts/header.html` para usar `flex-direction: column` no container principal:
   - **Linha 1 (topo):** logo + wordmark "IPCN" **centralizados** (flex + justify-content: center)
   - **Linha 2 (abaixo):** `wp-block-navigation` centralizada + CTAs (Associe-se / Apoia-se) ao lado direito da nav ou em linha separada
2. O markup atual (linha 3) tem `justifyContent: "space-between"`. Mudar para:
   ```html
   <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
   ```
   Para a marca (logo + wordmark).
3. Abaixo da marca, adicionar um segundo `wp:group` com a nav centralizada:
   ```html
   <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center","blockGap":"10px"}} -->
   ```
   Contendo a `wp:navigation` e os botões CTA.
4. No `style.css`, atualizar os seletores de header:
   - `header .wp-block-navigation` → `justify-content: center` (era `flex-end`)
   - Garantir que o container principal do header tenha `flex-direction: column; align-items: center`
5. **Menu mobile:** manter o overlay nativo do WP por enquanto (funciona), mas estilizar para abrir **abaixo do header** em vez de full-screen:
   - `.wp-block-navigation__responsive-container.is-menu-open` → `position: absolute; top: 100%; left: 0; right: 0; max-height: calc(100vh - 100px); overflow-y: auto;`
   - Remover `position: fixed` implícito (que cobre a tela toda)

**Critérios de Aceite:**
- [ ] Logo + wordmark "IPCN" centralizados no topo do header
- [ ] Menu de navegação abaixo da marca, centralizado
- [ ] CTAs (Associe-se / Apoia-se) visíveis ao lado da nav ou em linha própria
- [ ] Mobile 375px: hamburger abre painel abaixo do header, não overlay full-screen
- [ ] Header não quebra com 9 itens de menu em 1440px
- [ ] Header não quebra com título longo em mobile

---

### Tarefa 3: Substituir category ID hardcoded por slug na front-page

**Contexto:** `templates/front-page.html:41` usa `taxQuery: {category: [1]}` para filtrar notícias. IDs de termos são frágeis — podem mudar entre ambientes (staging vs produção) ou após importações.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/templates/front-page.html` (linha 41)

**Instruções de Implementação:**
1. Localizar o bloco `<!-- wp:query -->` da seção "Últimas notícias" (linha 41).
2. Substituir `taxQuery: {category: [1]}` por `categoryName: "noticias"` (ou o slug correto da categoria no DB do redesign).
3. Verificar qual é o slug correto da categoria "notícias" no banco do stagingredesign: `wp term list category --fields=slug,name --url=https://stagingredesign.ipcnbrasil.org`
4. Se o slug for `noticias`, usar: `"categoryName": "noticias"`. Se for `category` ou outro, usar o correto.
5. **Alternativa melhor:** converter a query de notícias para usar o shortcode `[ipcn_query_posts category="noticias" per_page="3"]` já existente em `functions.php`, mantendo consistência com as páginas internas. Isso eliminaria a query block hardcoded.

**Critérios de Aceite:**
- [ ] Nenhum `taxQuery` com IDs numéricos em `front-page.html`
- [ ] Seção de notícias continua mostrando 3 posts mais recentes da categoria correta
- [ ] Funciona tanto no stagingredesign quanto em produção (sem depender de term_id específico)

---

### Tarefa 4: Limpar mu-plugin `ipcn-optimizations.php` — remover código legado Divi

**Contexto:** O mu-plugin contém 3 blocos de código morto do tema Divi que não são usados pelo tema FSE: (1) cache de features Divi (linhas 20-21), (2) fix ETmodules (linhas 54-57), (3) CSS de forms Divi (linhas 63-169). O CSS de forms é o mais pesado — 107 linhas de regras para `.et_pb_contact_form_container` que o tema FSE nunca usa.

**Arquivos Afetados:**
- `wp-content/mu-plugins/ipcn-optimizations.php`

**Instruções de Implementação:**
1. **Remover** o filtro `et_builder_post_feature_cache_enabled` e `et_builder_global_feature_cache_enabled` (linhas 20-21). Justificativa: tema FSE não usa Divi, filtros são no-ops.
2. **Remover** o bloco `ipcn-etmodules-fix` (linhas 54-57). Justificativa: tema FSE não usa ETmodules.
3. **Remover** o bloco `ipcn-form-style` inteiro (linhas 63-169). Justificativa: 107 linhas de CSS para classes Divi que o tema FSE não renderiza.
4. **Manter** o bloco `ipcn-footer-logo-fix` (linhas 176-205) **apenas se** ainda houver páginas Divi ativas no staging que usem o footer Divi. Se todas as páginas foram migradas para FSE, remover também.
5. **Manter** os filtros de segurança (linhas 29, 37, 40-43): `the_generator`, `xmlrpc_enabled`, `X-Pingback`. Esses são válidos para qualquer tema.
6. O arquivo resultante deve ter apenas: segurança (generator, xmlrpc, headers) + footer fix (se necessário).

**Critérios de Aceite:**
- [ ] `grep -c "et_pb\|ETmodules\|et_builder" wp-content/mu-plugins/ipcn-optimizations.php` retorna 0
- [ ] Filtros de segurança (generator, xmlrpc, pingback) permanecem ativos
- [ ] `php -l wp-content/mu-plugins/ipcn-optimizations.php` não retorna erros
- [ ] Site continua funcionando normalmente (forms, footer, header)

---

### Tarefa 5: Corrigir cor do footer — remover `navy-soft` (#8e9bd0)

**Contexto:** O footer usa `#8e9bd0` (navy-soft) para títulos das colunas ("Instituto", "Contato", "Redes sociais"), links, separator e copyright. Essa cor não está nos sketches aprovados nem no design system. Os sketches usam branco com opacidade ou tons claros de navy.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/parts/footer.html` (linhas 7-8, 17-18, 27-28, 37-38, 41-42, 44-45)
- `wp-content/themes/ipcn-fse/theme.json` (se quiser remover `navy-soft` da paleta)

**Instruções de Implementação:**
1. Substituir todas as ocorrências de `color:#8e9bd0` no `footer.html` por `color:rgba(255,255,255,0.5)` para textos secundários (títulos de coluna, copyright).
2. Substituir `color:#c9d2f5` por `color:rgba(255,255,255,0.85)` para textos primários e links.
3. No separator (linha 37-38), trocar `backgroundColor: "navy-soft"` por uma cor com opacidade: `"style":{"color":{"text":"rgba(255,255,255,0.15)"}}` ou usar `className":"is-style-dots"` se preferir.
4. **Opcional:** remover `navy-soft` da paleta em `theme.json` se não for usada em mais nenhum lugar. Verificar antes: `grep -r "navy-soft" wp-content/themes/ipcn-fse/`

**Critérios de Aceite:**
- [ ] Nenhuma ocorrência de `#8e9bd0` ou `navy-soft` no footer
- [ ] Footer usa apenas navy como fundo e variações de branco/opacidade para texto
- [ ] Contraste do texto do footer com fundo navy `#0d176b` ≥ 4.5:1 (WCAG AA)
- [ ] Visual consistente com o padrão das outras páginas do tema

---

### Tarefa 6: Eliminar duplicação de templates acervo

**Contexto:** `archive-acervo_ipcn.html` e `taxonomy-tema_acervo.html` são quase idênticos — ambos têm hero navy + grid 3 colunas + paginação. A diferença mínima é que taxonomy herda a query do contexto da taxonomia.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/templates/taxonomy-tema_acervo.html` (remover)
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` (verificar)

**Instruções de Implementação:**
1. Verificar se `archive-acervo_ipcn.html` já usa `inherit: true` na query (sim, linha 23 do archive). Isso significa que quando WordPress carrega o archive do CPT, herda a query padrão. Quando carrega uma taxonomy, a query também herda o contexto.
2. **Deletar** `taxonomy-tema_acervo.html`. WordPress vai cair no `archive-acervo_ipcn.html` como fallback (hierarchy de templates FSE).
3. Verificar se o `term-list` (linha 21 do archive) funciona corretamente quando acessado via taxonomy — o bloco `wp:term-list` mostra os termos da taxonomia, o que é correto tanto no archive geral quanto no filtrado por termo.
4. Testar: acessar `/acervo/` (deve listar todos) e `/temas/fotografia` (deve filtrar por tema). Ambos devem usar o mesmo template.

**Critérios de Aceite:**
- [ ] Arquivo `taxonomy-tema_acervo.html` não existe mais
- [ ] `/acervo/` funciona (lista todos os itens)
- [ ] `/temas/fotografia` funciona (filtra por tema)
- [ ] Ambas as URLs usam o mesmo layout (hero navy + grid)

---

### Tarefa 7: Enriquecer template `page.html` com hero de página

**Contexto:** `page.html` tem apenas 7 linhas — header, título genérico e conteúdo. Páginas que usam este template (Quem Somos, Projetos, etc.) ficam sem identidade visual, sem hero navy, sem respiro.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/templates/page.html`

**Instruções de Implementação:**
1. Substituir o conteúdo de `page.html` por uma estrutura com hero navy (padrão das outras páginas):
   ```html
   <!-- wp:template-part {"slug":"header"} /-->

   <!-- wp:group {"align":"full","className":"ipcn-page-hero","style":{"spacing":{"padding":{"top":"56px","bottom":"48px","left":"20px","right":"20px"}}},"backgroundColor":"navy","textColor":"base","layout":{"type":"constrained","contentSize":"1100px"}} -->
   <div class="wp-block-group alignfull ipcn-page-hero has-base-color has-navy-background-color has-text-color has-background" style="padding-top:56px;padding-bottom:48px;padding-left:20px;padding-right:20px">
     <!-- wp:post-title {"level":1,"style":{"typography":{"fontFamily":"var:preset|font-family|oswald","fontSize":"clamp(24px, 4vw, 36px)","fontWeight":"700","lineHeight":"1.15"}},"textColor":"base"} /-->
   </div>
   <!-- /wp:group -->

   <!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"40px","bottom":"72px","left":"20px","right":"20px"}}},"layout":{"type":"constrained","contentSize":"720px"}} -->
   <div class="wp-block-group alignfull" style="padding-top:40px;padding-bottom:72px;padding-left:20px;padding-right:20px">
     <!-- wp:post-content {"layout":{"type":"constrained","contentSize":"720px"}} /-->
   </div>
   <!-- /wp:group -->

   <!-- wp:template-part {"slug":"footer"} /-->
   ```
2. No `style.css`, adicionar regra para o hero da página (se necessário, já existe `.ipcn-hero` mas esse é da home):
   ```css
   .ipcn-page-hero { /* pode reutilizar estilos do hero da home se aplicável */ }
   ```
3. Verificar se a classe `.ipcn-hero-alone` (style.css:319) ainda é necessária — era usada para esconder títulos em páginas sem hero. Com o novo `page.html`, pode ser removida.

**Critérios de Aceite:**
- [ ] Páginas genéricas (ex: Quem Somos, Projetos) renderizam com hero navy + título
- [ ] Conteúdo da página aparece com `contentSize: 720px` (leitura confortável)
- [ ] Footer aparece corretamente
- [ ] Mobile 375px: hero e conteúdo não quebram

---

### Tarefa 8: Separar Playfair Display de Oswald no theme.json

**Contexto:** `theme.json:20` declara `"fontFamily": "\"Oswald\", \"Playfair Display\", serif"` no slug `oswald`. Playfair Display é uma serif elegante usada para headings de leitura longa (single.html usa `var:preset|font-family|serif`). Misturá-la como fallback de Oswald (uma sans-serif condensed) é semântica errada — navegadores nunca vão cair no fallback porque Oswald carrega.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/theme.json` (linha 20)
- `wp-content/themes/ipcn-fse/functions.php` (linha 21 — Google Fonts URL)
- `wp-content/themes/ipcn-fse/style.css` (verificar se algum seletor usa `var:preset|font-family|oswald` esperando Playfair)

**Instruções de Implementação:**
1. No `theme.json`, corrigir a família `oswald` para `"Oswald", sans-serif` (remover Playfair do fallback).
2. Verificar que o slug `serif` já existe (linha 21: `"Playfair Display", Georgia, serif`). Confirmar que `single.html` usa `var:preset|font-family|serif` para headings (sim, linha 10).
3. A Google Fonts URL em `functions.php:21` já baixa Oswald, Playfair e Inter — não precisa mudar.
4. Verificar que nenhum template usa `fontFamily: "var:preset|font-family|oswald"` esperando renderizar Playfair. Buscar: `grep -r "font-family--oswald" wp-content/themes/ipcn-fse/templates/`

**Critérios de Aceite:**
- [ ] `theme.json` tem `oswald` com fallback `sans-serif` (não Playfair)
- [ ] `theme.json` tem `serif` com Playfair Display (já existe)
- [ ] Todos os headings de leitura longa usam `serif`, não `oswald`
- [ ] Nenhuma quebra visual na home ou em single posts

---

### Tarefa 9: Mover CSS inline do mu-plugin para style.css do tema

**Contexto:** O `ipcn-optimizations.php` injeta CSS via `wp_head` em 3 blocos (ETmodules fix, form style, footer fix). Após a Tarefa 4 (remover código Divi morto), o único CSS restante será o `footer-logo-fix`. Esse CSS deveria estar no `style.css` do tema.

**Arquivos Afetados:**
- `wp-content/mu-plugins/ipcn-optimizations.php` (linhas 176-205 — footer fix)
- `wp-content/themes/ipcn-fse/style.css`

**Instruções de Implementação:**
1. Após completar a Tarefa 4, verificar se o bloco `ipcn-footer-logo-fix` ainda é necessário. Se as páginas FSE já estiverem usando o footer FSE (não Divi), o fix não é aplicável (seletores `.et-l--footer` são Divi-specific).
2. Se ainda necessário (páginas Divi ativas), mover o CSS do bloco `ipcn-footer-logo-fix` para o final do `style.css` do tema.
3. Remover o bloco `wp_head` do mu-plugin após a migração.
4. O mu-plugin resultante deve ter **apenas** filtros PHP (generator, xmlrpc, headers) — zero `echo "<style>"`.

**Critérios de Aceite:**
- [ ] `ipcn-optimizations.php` não contém nenhum `echo "<style>"`
- [ ] CSS do footer fix (se necessário) está no `style.css`
- [ ] Footer renderiza corretamente em todas as páginas

---

### Tarefa 10: Melhorar acessibilidade dos shortcodes de form

**Contexto:** Os shortcodes `ipcn_assoc_form` e `ipcn_contact_form` (functions.php:286-316) geram inputs sem `id` associado ao `<label>`. A associação é implícita (input dentro do label), que é válida HTML, mas ter `id` explícito é melhor para screen readers e automação.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/functions.php` (shortcodes nas linhas 286-316)

**Instruções de Implementação:**
1. No shortcode `ipcn_assoc_form` (linha 286), adicionar `id` aos inputs:
   - `<input type="text" name="ipcn_nome" id="ipcn-nome" required>`
   - `<input type="email" name="ipcn_email" id="ipcn-email" required>`
   - `<input type="tel" name="ipcn_tel" id="ipcn-tel">`
2. Adicionar `for` nos labels:
   - `<label for="ipcn-nome">Nome completo *</label>`
   - `<label for="ipcn-email">E-mail *</label>`
   - `<label for="ipcn-tel">Telefone</label>`
3. Repetir para `ipcn_contact_form` (linha 304):
   - `<input type="text" name="ipcn_nome" id="ipcn-contato-nome" required>`
   - `<input type="email" name="ipcn_email" id="ipcn-contato-email" required>`
   - `<textarea name="ipcn_msg" id="ipcn-contato-msg" required></textarea>`
4. Adicionar `for` correspondentes nos labels.
5. Manter o honeypot como está (sem `id` visível é intencional).

**Critérios de Aceite:**
- [ ] Todo `<input>` e `<textarea>` nos forms têm `id` único
- [ ] Todo `<label>` tem `for` correspondente ao `id` do input
- [ ] Forms continuam funcionando (POST via admin-post.php, honeypot ativo)
- [ ] `php -l wp-content/themes/ipcn-fse/functions.php` sem erros

---

### Tarefa 11: Avaliar e documentar decisão sobre sections da home (5 vs 3)

**Contexto:** O STATUS.md documenta "home enxuta (3 sections)" mas a `front-page.html` atual tem 5 blocos de conteúdo (Hero, Notícias, Acervo, Agenda, CTA Contato). Isso pode ser intencional (evolução pós-STATUS) ou um desvio.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/templates/front-page.html`
- `STATUS.md` (atualizar se necessário)

**Instruções de Implementação:**
1. **Verificar com o Daniel/contratante** se a home deve ter 3 ou 5 sections.
2. Se a decisão for 3 sections (Hero + Notícias + CTA):
   - Remover a section "Acervo em destaque" (linhas 64-99 do front-page.html)
   - Remover a section "Agenda" (linhas 101-116 do front-page.html)
   - O link "Explorar Acervo" no hero já leva para `/acervo`
   - A agenda pode ter link no menu ou no footer
3. Se a decisão for manter 5 sections:
   - Atualizar `STATUS.md` para refletir o estado real
   - Documentar que a evolução de 3→5 foi deliberada
4. **Não implementar** sem confirmação — esta tarefa é de decisão, não de código.

**Critérios de Aceite:**
- [ ] Decisão documentada (3 ou 5 sections)
- [ ] `STATUS.md` reflete o estado real da home
- [ ] Se removidas sections, links para /acervo e /agenda existem em outro lugar (menu/footer)

---

### Tarefa 12: Remover `ABSPATH` checks duplicados em functions.php

**Contexto:** `functions.php` tem `if ( ! defined( 'ABSPATH' ) ) { exit; }` em 4 lugares (linhas 6, 358, 420, 540). A checagem na linha 6 já protege todo o arquivo — as subsequentes são redundantes.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/functions.php` (linhas 358-360, 420-422, 540-542)

**Instruções de Implementação:**
1. Manter apenas o `if ( ! defined( 'ABSPATH' ) ) { exit; }` na linha 6 (topo do arquivo).
2. Remover as 3 ocorrências subsequentes (linhas 358-360, 420-422, 540-542).
3. Verificar que não há nenhuma linha de código PHP entre os blocos que precise de proteção independente (não há — são todas dentro do mesmo `<?php`).

**Critérios de Aceite:**
- [ ] Apenas 1 ocorrência de `ABSPATH` check no arquivo (linha 6)
- [ ] `php -l functions.php` sem erros
- [ ] Arquivo funciona normalmente no WordPress

---

### Tarefa 13: Validar e corrigir responsividade mobile do header

**Contexto:** O header atual com 9 itens de menu + 2 CTAs pode estourar em mobile 375px. O `style.css` tem regras para `.ipcn-header-cta` (linhas 85-93) que fazem wrap, mas não há garantia de que o menu + CTAs caibam na mesma linha.

**Arquivos Afetados:**
- `wp-content/themes/ipcn-fse/parts/header.html`
- `wp-content/themes/ipcn-fse/style.css` (linhas 85-111)

**Instruções de Implementação:**
1. Após a Tarefa 2 (reestruturação do header), testar em 375px com 9 itens de menu.
2. Se o header estiver sendo reestruturado para "centralizado + menu embaixo" (Tarefa 2), o problema se resolve naturalmente: a nav fica em linha separada da marca.
3. Garantir que o hamburger button apareça em telas ≤ 782px (comportamento padrão do WP navigation block com `icon: "menu"`).
4. Testar que o painel mobile não corte itens do menu (overflow-y: auto se necessário).

**Critérios de Aceite:**
- [ ] 375px: hamburger visível, CTAs visíveis (ou hamburger inclui CTAs)
- [ ] 768px: menu completo visível sem quebra
- [ ] 1440px: header centralizado com menu abaixo, CTAs ao lado
- [ ] Nenhum overflow horizontal em nenhum breakpoint

---

## Resumo de Prioridades

| Prioridade | Tarefas | Motivo |
|------------|---------|--------|
| **P0 — Crítico** | T1 (URL hardcoded), T2 (Header layout) | Quebra em produção / Desvio de escopo da contratante |
| **P1 — Alto** | T3 (Category ID), T4 (Limpar mu-plugin), T5 (Footer cor) | Fragilidade técnica / Código morto / Inconsistência visual |
| **P2 — Médio** | T6 (Duplicação templates), T7 (page.html), T8 (theme.json fonts), T9 (CSS inline) | Débito técnico / Qualidade |
| **P3 — Baixo** | T10 (A11y forms), T11 (Sections home), T12 (ABSPATH), T13 (Mobile header) | Polish / Boas práticas |

---

*Relatório gerado em 17/09/2026. Base: commit `4bd4326`, código do tema `ipcn-fse` v0.2.0, STATUS.md, ROADMAP.md, sketches aprovados.*
