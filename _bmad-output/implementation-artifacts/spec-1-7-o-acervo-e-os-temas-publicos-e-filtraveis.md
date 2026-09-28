---
title: 'Story 1.7 — O Acervo e os Temas, publicos e filtraveis'
type: 'feature'
created: '2026-09-28'
status: 'done'
review_loop_iteration: 0
followup_review_recommended: false
baseline_commit: '4cba582b2992aace06a6277f23ac234f6706c55d'
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
warnings:
  - oversized
deferred:
  - summary: >-
      A copy do vazio das Secções passou a ser servida por `inc/listings.php` (e a do Acervo vazio continua em `templates/archive-acervo_ipcn.html`), pelo que a história 1.10 — dona dos estados vazios — passa a ter dois sítios onde editar.
    evidence: >-
      Verdadeiro e a manter: o `core/query-no-results` é estático e o mesmo `templates/archive.html` serve Secções e Temas, pelo que a frase teve de virar ramo de PHP (a alternativa era esconder markup com CSS). A frase saiu verbatim, sem mudança de comportamento. Achados 6 e 7 da revisão.
    location: >-
      wp-content/themes/ipcn-fse/inc/listings.php (ramo não-Tema de `ipcn_archive_vazio`) e templates/archive-acervo_ipcn.html:27
    severity: low
  - summary: >-
      O contrato de classes do cartão em `AGENTS.md`/AD-3 não nomeia os marcadores de ausência novos (`ipcn-card-notema`, `ipcn-single-notema`), e o marcador da imagem continua a ser o único documentado.
    evidence: >-
      Achado 12 da revisão. O remédio edita contexto de agente (o bloco gerido do `AGENTS.md`, que um refresh do `bmad-project-context` substitui), como já ia o contrato incompleto da 1.3. Fecha-se numa passagem ao bloco gerido e à espinha.
    location: >-
      wp-content/themes/ipcn-fse/patterns/ipcn-card.php, patterns/ipcn-card-feature.php, style.css
    severity: low
  - summary: >-
      A lógica nova do tema continua sem verificação durável: a guarda de superfície de `[ipcn_tema_filter]` e os ramos de `[ipcn_archive_vazio]` só são observados por um harness fora do repositório.
    evidence: >-
      Achados 16 e 17 da revisão, pré-verificados pela camada de lacunas: apagar a guarda (`is_tax('tema_acervo') || is_post_type_archive('acervo_ipcn')`) mantém o `check-php.sh` nos 17 problemas aceites e os testes do script a passar, e as pílulas do Acervo apareceriam nos archives de Secção. É a mesma infraestrutura de verificação já diferida pela 1.3, 1.5 e 1.6 (o `check-php.sh` não lê `.html` nem corre PHP do tema; o NFR9 declara «sem build step, testes ou CI»). Fecha-se estendendo o `scripts/check-php.sh` e os seus casos, com um runtime WordPress para as linhas de render.
    location: >-
      scripts/check-php.sh, inc/listings.php
    severity: low
  - summary: >-
      Um endereço paginado de um Tema (`/temas/<slug>/page/N/` além da última página) faz 404 em vez de mostrar o estado vazio nomeado.
    evidence: >-
      Achado 15 da revisão, confirmado na fonte: o `WP::handle_404()` desmarca o 404 do archive sem posts apenas quando `! is_paged()`, pelo que a página 1 explica-se e a página 2 não. É o comportamento do núcleo para páginas fora do intervalo, igual nas Secções, e não é produzido por esta história; um `pre_handle_404` seria o mecanismo se o projecto o quiser, e a página 404 é da 1.10.
    location: >-
      wp-content/themes/ipcn-fse/templates/archive.html
    severity: low
  - summary: >-
      A passagem no browser que fecha os ACs (navegação por Tema, pílula marcada, Tema sem peças, marcador de ausência, paginação) continua pendente de deploy e purga em `stagingredesign`, e o hero do Tema imprime a frase de recurso porque as três descrições estão vazias na base de dados.
    evidence: >-
      Achados 20 e 21 da revisão. O ambiente não tem WordPress local e o deploy é acção do dono (paragem declarada nas preferências do `AGENTS.md`), pelo que a verificação fica na lista manual desta spec, como ficou nas 1.5 e 1.6. As descrições dos Temas são conteúdo da base de dados — escrevê-las é alteração na base de dados, também paragem.
    location: >-
      stagingredesign.ipcnbrasil.org/acervo/, /temas/<slug>/, base de dados (tema_acervo.description)
    severity: low
---

<intent-contract>

## Intent

**Problem:** O Acervo não tem filtro de Tema. O único controlo em `templates/archive-acervo_ipcn.html:21` é um `core/term-list`, e esse bloco **não existe no núcleo** (`wp-includes/blocks/term-list/block.json` dá 404 em WP 6.4.3; há `post-terms`, `tag-cloud` e `query`), pelo que `render_block()` devolve vazio: nenhuma pílula de Tema aparece em `/acervo/` e a única rota para um Tema são as etiquetas dentro dos cartões. Em `/temas/<slug>` — servido por `templates/archive.html`, não por um template de taxonomia (AD-8 e o mapa de capacidades) — um Tema sem peças mostra a frase de vazio do Notícias, sem explicação nem caminho de volta ao Acervo; o `core/query` do archive herda a consulta do termo (`inherit:true`, verificado: a listagem do Tema mostra itens `acervo_ipcn` apesar de o bloco declarar `postType:post`), e o `perPage:12` que os dois templates declaram é inerte nesse caminho — o `build_query_vars_from_query_block()` só corre quando não há `inherit`, e nenhum dos consumidores lhe aplica o `perPage`. Um Item de Acervo sem Tema não mostra etiqueta nenhuma: a ausência não aparece em lado nenhum (a etiqueta sai dos dois `core/post-terms` do cartão, e um sem termos devolve string vazia).

**Approach:** Duas pílulas de comportamento em `inc/listings.php` e um marcador de ausência no markup. `[ipcn_tema_filter]` imprime as pílulas do componente `theme-filter` do `DESIGN.md` — ligações reais, por slug, para `/temas/<slug>/`, com `aria-current="page"` no termo aberto — e entra em `/acervo/` (no lugar do bloco morto) e em `templates/archive.html` (a superfície do Tema). `[ipcn_archive_vazio]` escolhe a frase do vazio pelo objecto consultado: no Tema explica e oferece o Acervo inteiro; nos restantes archives mantém a frase actual, verbatim. Nos dois cartões (e no hero do Item), um `span.ipcn-card-notema` diz "Sem Tema" quando não há etiqueta, como o `ipcn-card-noimg` já faz para a imagem. Sem template de taxonomia, sem gate, sem contador.

## Boundaries & Constraints

**Always:** hrefs de termo por slug, de `get_term_link()` — nunca `term_id` (AD-7); a listagem do Tema é o `core/query` herdado do archive (AD-2), não um bloco nem um `WP_Query` novo; o filtro e o vazio imprimem HTML simples com as classes do tema e nunca comentários de bloco (AD-1, AD-5); `/temas/<slug>` continua servido por `templates/archive.html` com o hero a ler o nome e a descrição do termo (AD-8); 44px de alvo em cada pílula (`DESIGN.md`, FR-13); `/acervo/` e `/temas/<slug>/` mantêm os endereços (NFR8).

**Never:** template de taxonomia novo (`taxonomy-*.html`); gate de login, `403` ou "últimos N"; busca; filtro por `term_id`/`categoryIds`/`taxQuery`; `WP_Query` novo; markup de cartão à mão em PHP; tocar em `theme.json`, `parts/header.html`, `parts/footer.html` (1.8), `inc/agenda-block.php` (2.2), mu-plugin (1.12), `inc/forms.php` e `inc/cookie-bar.php` (Épico 3), na copy dos vazios da Home/Secção (1.10) ou na acentuação (1.11); `AGENTS.md` (bloco gerido por refresh do `bmad-project-context`, e já tem alterações não commitadas do dono).

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Filtro no Acervo | `/acervo/`, três Temas | uma pílula por Tema, nenhuma marcada; a grelha lista o Acervo inteiro | — |
| Filtro no Tema | `/temas/<slug>/` | as mesmas pílulas, a do termo com `aria-current="page"` | — |
| Sem Temas | `tema_acervo` sem termos | o filtro não imprime nada | devolve `''`, sem `nav` vazio |
| Tema sem peças | termo existente, zero itens | frase do Tema + ligação ao Acervo inteiro; sem grelha | o núcleo não faz 404 de um archive de termo com 0 posts (`is_tax() && get_queried_object()`), logo o template corre |
| Secção sem peças | archive de categoria, zero posts | frase actual, verbatim ("Ainda nao ha posts nesta secao.") | igual ao de hoje |
| Item sem Tema no cartão | `acervo_ipcn` sem `tema_acervo`, numa listagem | marcador "Sem Tema" visível; com um Tema, o marcador não aparece (o cartão mostra a etiqueta) | — |
| Item sem Tema na página | `single-acervo_ipcn`, sem Tema | marcador no hero | — |
| Uma página | archive com ≤ `posts_per_page` | sem paginação | `render_block_core_query_pagination()` devolve `''` quando o conteúdo interior está vazio |
| Várias páginas | archive com > `posts_per_page` | numeração `core/query-pagination` | núcleo |

</intent-contract>

## Code Map

- `wp-content/themes/ipcn-fse/inc/listings.php` — **onde entram os dois shortcodes.** Já tem o filtro `query_loop_block_query_vars` (l.33-…) e `ipcn_archive_hero` (l.112-…), com 5 literais `<!-- wp:` aceites; o ficheiro registra 2 `add_shortcode` e um `add_filter` local. O filtro novo não corre no caminho herdado (`inherit`), pelo que não há conflito com ele.
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — l.21 o `wp:term-list` morto dentro do grupo subtle; l.23 a `core/query` (`perPage:12`, `inherit:true`); l.26 a frase de vazio; l.28 a paginação.
- `wp-content/themes/ipcn-fse/templates/archive.html` — l.3 `[ipcn_archive_hero]`; l.6 a `core/query` (`perPage:12`, `postType:post`, `inherit:true`); l.9-10 o `core/query-no-results`; l.11 a paginação. É o template que serve `/temas/<slug>` (hierarquia FSE) e os archives de categoria.
- `wp-content/themes/ipcn-fse/patterns/ipcn-card.php` — l.27-30 a casa da etiqueta: dois `core/post-terms` (`category` e `tema_acervo`), depois `ipcn-card-title`.
- `wp-content/themes/ipcn-fse/patterns/ipcn-card-feature.php` — l.29-32 as mesmas casas (consumido pela vitrine da Home).
- `wp-content/themes/ipcn-fse/templates/single-acervo_ipcn.html` — l.6-7 o grupo do hero; l.12 os `post-terms tema_acervo`; o grupo interior não tem classe (é preciso um hook para o CSS).
- `wp-content/themes/ipcn-fse/style.css` — l.544-574 o bloco `.ipcn-seccoes-links` (a pílula que a 1.6 deixou, a espelhar); l.430-446 a regra da etiqueta do cartão (`.ipcn-card .wp-block-post-terms`); l.421 o `:has()` do `ipcn-card-noimg` (precedente do marcador de ausência); l.332-347 a paginação.
- Factos do núcleo (WP 6.4.3, lidos na fonte): `core/term-list` não existe; `build_query_vars_from_query_block()` (`wp-includes/blocks.php:1692`) não lê `categoryName` nem trata `inherit` — quem trata são `post-template.php`, `query-no-results.php` e os blocos de paginação, que usam o `$wp_query` global; `render_block_core_query_no_results()` devolve o conteúdo (com shortcodes já expandidos) só quando `post_count` é 0; `render_block_core_query_pagination()` devolve `''` com conteúdo interior vazio; `WP::handle_404()` não marca 404 quando `is_tax()` e há objecto consultado.
- API a usar: `get_terms( 'tema_acervo', array( 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) )`, `get_term_link()`, `get_post_type_archive_link( 'acervo_ipcn' )`, `get_queried_object()`.
- Verificado no ambiente de revisão (2.3 itens, 3 Temas): `/acervo/` 200 com o `term-list` a não produzir markup nenhum; `/temas/fotografia/` 200 a listar o item `acervo_ipcn`; `/temas/inexistente-xyz/` 404; item `/acervo/carta-de-fundacao-do-ipcn-1975/` 200 sem sessão. Os três Temas têm `description` vazia na BD (REST).
- `scripts/check-php.sh` — única verificação executável; conta 17 problemas aceites (12 em `agenda-block.php`, 5 em `listings.php`), todos `block markup`.

## Tasks & Acceptance

**Execution:**
- `wp-content/themes/ipcn-fse/inc/listings.php` — acrescentar `add_shortcode( 'ipcn_tema_filter', … )`: `get_terms('tema_acervo')` sem esconder os vazios, `nav.ipcn-tema-filter[aria-label]` + `ul.ipcn-tema-filter-links` com um `a` por termo (`get_term_link()`), `aria-current="page"` quando `is_tax('tema_acervo')` e o termo é o consultado, `''` sem termos — é o filtro que hoje não existe.
- `wp-content/themes/ipcn-fse/inc/listings.php` — acrescentar `add_shortcode( 'ipcn_archive_vazio', … )`: em `is_tax('tema_acervo')` devolve a frase do Tema e a ligação ao Acervo inteiro; nos restantes archives devolve a frase actual verbatim — cumpre o Tema sem peças sem inventar 404 nem segunda mensagem.
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — trocar o `wp:term-list` por `[ipcn_tema_filter]` e tirar o `perPage:12` inerte da `core/query` — o controlo que faltava e o "sem últimos N" legível.
- `wp-content/themes/ipcn-fse/templates/archive.html` — pôr `[ipcn_tema_filter]` no grupo da listagem (que devolve `''` fora do Tema) e `[ipcn_archive_vazio]` dentro do `core/query-no-results`; tirar o `perPage:12` inerte — o Tema ganha filtro e vazio próprio sem tocar em nenhuma superfície de Notícias.
- `wp-content/themes/ipcn-fse/patterns/ipcn-card.php` e `patterns/ipcn-card-feature.php` — acrescentar `span.ipcn-card-notema` com "Sem Tema" depois dos dois `core/post-terms` — a etiqueta em falta passa a ler-se.
- `wp-content/themes/ipcn-fse/templates/single-acervo_ipcn.html` — a mesma casa no hero, e uma classe no grupo que a contém, para o CSS poder decidir.
- `wp-content/themes/ipcn-fse/style.css` — `.ipcn-tema-filter-links` (pílula `theme-filter`: contorno muted, `rounded.full`, 44px, label em maiúsculas; `[aria-current="page"]` preenchida em navy com texto base) e as duas regras de visibilidade do marcador de ausência (escondido por omissão; visível no cartão de `acervo_ipcn` sem `.wp-block-post-terms` e no hero do Item sem Tema).

**Acceptance Criteria:**
- Given o site aberto sem sessão, when carrego `/acervo/`, then vejo uma pílula por Tema, a grelha lista o Acervo inteiro e não há limite de "últimos N" nem 403.
- Given uma pílula de Tema em `/acervo/`, when a sigo, then o endereço passa a `/temas/<slug>/`, o botão de voltar regressa, e a listagem mostra só as peças desse Tema.
- Given `/temas/<slug>/`, when leio o topo, then o hero traz o nome e a descrição do termo e a pílula do termo aberto está marcada.
- Given um Tema sem peças, when abro `/temas/<slug>/`, then leio a explicação e a ligação para o Acervo inteiro, e nenhuma grelha de outro tipo de conteúdo aparece.
- Given um Item de Acervo sem Tema atribuído, when olho para o cartão numa listagem e para o hero da sua página, then a ausência está escrita ("Sem Tema"); com Tema atribuído leio o Tema e não a ausência.
- Given `tema_acervo` sem termos, when carrego `/acervo/`, then não aparece filtro nem marco de navegação vazio.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa e os problemas aceites não crescem.

## Spec Change Log

Sem alterações ao contrato: as duas decisões abaixo são do implementador e ficam registadas para poderem ser contestadas.

- **`[ipcn_tema_filter]` guarda de superfície, não só de termos.** O `Execution` só fixa `''` sem termos; a tarefa do `templates/archive.html` diz que o shortcode "devolve `''` fora do Tema". Lido literalmente ("fora do Tema"), o shortcode não imprimiria em `/acervo/`, contra a primeira linha da matriz e o AC 1 — o `$is_acervo` é portanto `is_tax('tema_acervo') || is_post_type_archive('acervo_ipcn')`. Fica `''` nas Seções (categorias), que partilham o `archive.html`: é o que "sem tocar em nenhuma superfície de Notícias" exige.
- **Copy do Tema sem peças.** `Ainda nao ha pecas publicadas neste Tema.` com a ligação `Ver o Acervo inteiro` (`get_post_type_archive_link( 'acervo_ipcn' )`), sem acentos como o resto de `inc/listings.php` — a acentuação é da 1.11 e a copy dos vazios é da 1.10.

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo (14 achados); **EC** = caça a casos-limite (1); **VG** = lacunas de verificação (2 + 1 outro); **IA** = auditor de alinhamento com a intenção (5 divergências descritivas). Total: 23 achados. Veredictos: `high`/`medium`/`low` (defeito real), `false` (refutado).

| # | Camada | Achado | Veredicto | Rota | Evidência |
|---|--------|--------|-----------|------|-----------|
| 1 | BH | `[ipcn_archive_vazio]` dentro de um `wp:paragraph` produz `<p><p>` | `false` | reject | Refutado na fonte: `get_the_block_template_html()` corre `shortcode_unautop()` antes de `do_shortcode()` e `do_blocks()` (6.4.3), pelo que `<p>[…]</p>` perde o invólucro; a frase sai num único `<p>`, byte-idêntica à de antes. |
| 2 | BH | A regra do marcador assenta em comportamento não verificado do núcleo (o `post-terms` vazio podia deixar o invólucro) | `false` | reject | Refutado na fonte: `render_block_core_post_terms()` devolve `''` quando `get_the_terms()` é vazio ou erro — não há `<div class="wp-block-post-terms">` sem termos. |
| 3 | BH | Sem recurso para motores sem `:has()`, e a falha é muda | `false` | reject | O `:has()` já é idioma do tema (`.ipcn-card:has(…)` da 1.3, `.ipcn-vitrine:not(:has(…))` da 1.5) e está no baseline de todos os browsers alvo; nenhum estado alcançável foi demonstrado. O CSS é versionado por `filemtime`, pelo que não há CSS velho servido com markup novo. |
| 4 | BH | O marcador lê "Sem Tema" em cartões fora do Acervo | `low` | **patch** | Verdadeiro: a regra sem prefixo apanhava um `post` sem categoria nenhuma. Corrigido com `.type-acervo_ipcn` (classe do `core/post-template`) — ver o grupo com o achado 19. |
| 5 | BH | O filtro não oferece caminho de volta ao Acervo inteiro | `low` | reject | Nenhum documento pede uma pílula "Todos"; o voltar do browser é o que o AC exige ("o botão de voltar funciona"), o menu dá `/acervo/` e o vazio do Tema oferece-o. Acrescentar um controlo novo seria mais do que uma correcção directa. |
| 6 | BH | A frase de vazio da Secção mudou de sítio sem handoff registado para a 1.10 | `low` | defer | Verdadeiro (perda de descobribilidade para quem vier a seguir). Registado no diferido, com os dois sítios que a 1.10 passa a ter de editar. |
| 7 | BH | O vazio do próprio `/acervo/` continua divergente da copy do épico | `false` | reject | A frase (`Ainda nao ha itens publicados no acervo.`) é pré-existente e a copy do Acervo vazio é da 1.10 (épico, AC "o Acervo vazio diz…"); esta história não a tocou nem fez dela a sua. Fica anotado no diferido com o handoff. |
| 8 | BH | `deferred-work.md` não ganha entrada para esta história | `low` | defer | Verdadeiro: os diferimentos viviam só na prosa. Entradas acrescentadas ao `deferred-work.md` além da lista do frontmatter. |
| 9 | BH | Retirar `perPage:12` apaga o único registo do tamanho de página | `low` | **patch** | Verdadeiro: o tamanho efectivo passa a ser o `posts_per_page` do site, sem nada no repo a dizê-lo. Corrigido com uma nota nos dois templates, junto da consulta. |
| 10 | BH | `orderby => 'name'` sem decisão de colação (nomes acentuados cairiam ao fim) | `false` | reject | A ordem por nome é já a omissão do `get_terms`, e as colações do WordPress (`utf8mb4_unicode_ci`/`unicode_520_ci`) ordenam as letras acentuadas junto da sua base; sem prova de colação binária, o mau resultado não se demonstra. |
| 11 | BH | As pílulas novas duplicam as das Seções, com divergências | `low` | reject | São dois componentes distintos: o `theme-filter` do `DESIGN.md` fixa rótulo em maiúsculas e estado escolhido, que a pílula das Seções não tem. Unificar acoplaria componentes que o desenho separa. |
| 12 | BH | O contrato de classes do cartão ganha marcadores sem registo durável (`AGENTS.md`/AD-3) | `low` | defer | Verdadeiro; o remédio edita contexto de agente (o bloco gerido do `AGENTS.md`), pelo que vai para diferido, como já ia o contrato incompleto da 1.3. |
| 13 | BH | O esqueleto do spec diverge dos anteriores (`route:`, `## Decisions`, `## Implementation Notes`), `oversized` sem justificação e `followup_review_recommended` pré-julgado | `false` | reject | As chaves e secções em falta vêm do renderizador desta execução (o `spec-template.md` deste render não as tem); `warnings: [oversized]` é o que o próprio modelo manda pôr acima de 1600 tokens; `followup_review_recommended: false` é o valor de omissão do template. |
| 14 | BH | O `sprint-status.yaml` diz `backlog` e a chave acentuada não casa com o ficheiro | `false` | reject | Estado transitório escrito por outro passo do método (a ledger não é escrita por este fluxo), e a incompatibilidade ficheiro/chave já está registada no diferido desde a 1.1. |
| 15 | EC | `/temas/<slug>/page/N/` fora do intervalo faz 404 em vez do vazio nomeado | `low` | defer | Confirmado na fonte: `WP::handle_404()` só desmarca o 404 quando `! is_paged()`. É o comportamento do núcleo para páginas fora do intervalo (igual nas Secções) e não é produzido por esta história; o remédio seria um `pre_handle_404`, registado no diferido. |
| 16 | VG | A guarda de superfície do filtro não é observada por verificação nenhuma do repo | `low` | defer | Achado pré-verificado pela camada (remover a guarda mantém `check-php.sh` nos 17 e os testes do script a passar). É a lacuna de infraestrutura de verificação já diferida na 1.3/1.5/1.6 e a que o NFR9 fecha (sem runner nem CI). |
| 17 | VG | A asserção do vazio era um `strpos` de ficheiro inteiro, que não vê a mudança de sítio | `low` | defer | Verdadeiro no harness do scratchpad. Apertei a asserção (regex que atravessa `query-no-results` + uma única chamada) e re-corri; o harness durável continua a ser a infraestrutura diferida. |
| 18 | VG | (outro) A dependência de `shortcode_unautop()` não se distingue no ficheiro | `false` | reject | Mesma refutação do achado 1, e a camada verificou-a na fonte. A limitação é da leitura do ficheiro, não do comportamento. |
| 19 | IA | (3d) O marcador é um proxy: o gatilho é "sem etiqueta", não "item do Acervo" | `low` | **patch** | Mesma raiz do achado 4. Corrigido por escopo: `.type-acervo_ipcn .ipcn-card:not(:has(.wp-block-post-terms)) .ipcn-card-notema`; a nota de desenho passou a descrever a regra real. |
| 20 | IA | (3a) O que a intenção pede vive no browser; as provas vivem no código e em harness com stubs | `low` | defer | Verdadeiro e estrutural: sem WordPress local e sem deploy (que é acção do dono), a passagem no browser fica pendente, como ficou nas 1.5 e 1.6. Registado no diferido. |
| 21 | IA | (3b) "O hero a ler a descrição do termo" não é observável: as três descrições estão vazias na BD | `low` | defer | Verdadeiro: o código lê a descrição (`ipcn_archive_hero`), a base de dados é que não a tem. Escrevê-las é alteração na base de dados — paragem —, pelo que vai para diferido. |
| 22 | IA | (3c) "Sem últimos N" é declarativo: o `perPage` já era inerte | `false` | reject | A cláusula do AC é cumprida pelo `inherit` herdado (verificado na fonte e no ambiente) e a remoção é de legibilidade; o achado descreve a natureza da correcção, não um defeito. |
| 23 | IA | (3e) O diff mexe no `AGENTS.md`, contra o `Never` do próprio contrato | `false` | reject | A alteração do `AGENTS.md` é do dono, já presente na árvore antes desta execução (verificado no arranque: `M AGENTS.md`, mtime anterior à sessão). Não foi produzida por esta história e é commitada em separado. |

**Agrupamento e encaminhamento:** os achados 4 e 19 partilham a raiz (o gatilho do marcador) e a rota `patch`. Os `patch` (4/19 e 9) foram aplicados por mim, porque o subagente da 1.3 não é re-endereçável por id nesta plataforma; depois deles, `check-php.sh` nos 17 aceites, `php -l` limpo e o harness em 45/45. Os restantes `low` (6, 8, 12, 15, 16, 17, 20, 21) seguem para o diferido, com entrada no `deferred-work.md`. Rejeitados por refutação: 1, 2, 3, 7, 10, 13, 14, 18, 22, 23. Não há `intent_gap` nem `bad_spec` — sem loopback; `review_loop_iteration` fica 0.

## Design Notes

**Porquê um shortcode e não um pattern.** As Seções da 1.6 são curadas porque não são deriváveis na BD; os Temas são termos reais e crescem sem ninguém editar o tema, e o componente `theme-filter` do `DESIGN.md` tem estado *escolhido* — um pattern estático não o sabe exprimir. `get_terms()` não é uma listagem de conteúdo (AD-2 não é tocado: continua a ser `core/query` a listar), e o HTML sai com classes do tema, que é a excepção 2 do AD-1. Sem comentários de bloco, para não crescer a contagem do `check-php.sh`.

**Porquê o template do archive e não um template de taxonomia.** O mapa de capacidades da espinha atribui `/temas/` a `templates/archive.html` e o AD-8 diz que o endereço é servido *deliberadamente* pelo template do archive. Criar `taxonomy-tema_acervo.html` contradiria os dois; o que falta é conteúdo condicional dentro do archive, e é isso que `[ipcn_archive_vazio]` põe no `query-no-results`.

**Porquê o Tema vazio não é 404.** `WP::handle_404()` não marca 404 quando `is_tax()` e há objecto consultado, mesmo sem posts — o receio registado em `review-adversarial.md` (F7) não se confirma na fonte. O estado vazio é portanto conteúdo do template, não uma página de erro.

**A frase da Secção muda de sítio.** Passa a ser servida pelo mesmo shortcode (verbatim), porque o `query-no-results` é estático e uma segunda mensagem ao lado da do Tema seria o defeito óbvio. A copy continua da 1.10; o que muda é o ficheiro onde a edita (`inc/listings.php`).

**Porquê um marcador e não uma etiqueta inventada.** O PRD diz "mostra a ausência; não inventa uma tag": o marcador não é ligação nem terracota, é metadado em `text-muted`. A regra que o revela é a ausência de etiqueta **no cartão de um item do Acervo** — `.type-acervo_ipcn .ipcn-card:not(:has(.wp-block-post-terms))` — depois de a revisão ter mostrado que a versão sem o prefixo lia "Sem Tema" num cartão de Notícias sem categoria nenhuma, onde a ausência se chamaria "Sem categoria" (o `core/post-template` põe `type-acervo_ipcn` no `<li>` pelo `get_post_class()`, a mesma classe que a própria listagem usa). O gatilho continua a ser a ausência da casa: o `render_block_core_post_terms()` devolve `''` quando o `get_the_terms()` é vazio, verificado na fonte do 6.4.3 — logo um item sem Tema não deixa `.wp-block-post-terms` no DOM e o marcador sai. É o mesmo idioma do `ipcn-card-noimg` para a imagem (AD-3).

**Ordem e vazios.** `hide_empty => false` de propósito: um Tema sem peças continua alcançável e explica-se em vez de desaparecer do filtro. A ordem é por nome, ascendente — nenhum documento fixa ordem.

**Dívida de dados, fora do repo.** Os três Temas têm `description` vazia, logo o hero do Tema imprime a frase de recurso: o código lê a descrição (AD-8), a base de dados é que não a tem. Escrever as descrições é alteração na base de dados — fica registada, não feita.

**Nomes dos hooks, para não haver deriva entre os ficheiros.** Shortcodes `ipcn_tema_filter` e `ipcn_archive_vazio` (convenção `ipcn_*`); no filtro, `nav.ipcn-tema-filter` com `aria-label="Temas do Acervo"` e `ul.ipcn-tema-filter-links`; o marcador de ausência é sempre `span.ipcn-card-notema` com o texto "Sem Tema"; no hero do Item, o grupo que o contém leva `ipcn-single-tema` e o marcador `ipcn-single-notema`, com a cor do rótulo do hero (`#c9d2f5`), como o `post-terms` ao lado.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 `block markup` aceites sob `inc/` (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1, nenhuma entrada `sintaxe:`.
- `php -l wp-content/themes/ipcn-fse/inc/listings.php` — esperado: sem erros.
- Harness de lógica em `$COMMANDCODE_SCRATCHPAD/verify-1-7.php` (fora do deliverable, como o da 1.6) — esperado: todas as asserções a passar. Cobre as linhas 1-5 da matriz (filtro com e sem termos, termo consultado, vazio do Tema e vazio da Secção) com os `get_terms`/`get_term_link`/`get_queried_object`/`is_tax` substituídos por stubs, incluindo os ramos de não-Tema e de categoria sem posts. As linhas 6-7 (visibilidade do marcador) são regras `:has()` de `style.css`, conferíveis por inspecção do CSS e no browser; as linhas 8-9 (paginação) são comportamento do núcleo, já lido na fonte.

**Manual checks (if no CLI):**
- Sem WordPress local, o filtro, o Tema marcado, o Tema sem peças e o marcador de ausência fecham no browser em `stagingredesign`, após deploy e purga (`?nocache=1`): `/acervo/` (pílulas, grelha inteira, sem paginação com uma página), `/temas/fotografia/` (hero do termo, pílula marcada) e um Item sem Tema na listagem e na sua página.

## Auto Run Result

**O que mudou.** O Acervo ganhou o filtro de Tema que não existia (o `core/term-list` do template não é um bloco do núcleo e não imprimia nada): `[ipcn_tema_filter]` em `inc/listings.php` imprime uma pílula por termo de `tema_acervo`, por slug de `get_term_link()`, com `aria-current="page"` no termo aberto, e corre nas duas superfícies do Acervo (`/acervo/` e `/temas/<slug>/`), devolvendo `''` nas Secções. `[ipcn_archive_vazio]` dá ao Tema sem peças a explicação e o caminho para o Acervo inteiro, mantendo verbatim a frase das Secções. O `perPage:12` inerte saiu das duas consultas herdadas do archive (com nota do tamanho de página real). Nos dois cartões e no hero do Item, um marcador `Sem Tema` fecha a ausência de etiqueta, revelado só no cartão de `acervo_ipcn`.

**Ficheiros alterados** (7, +218/−7):
- `wp-content/themes/ipcn-fse/inc/listings.php` — `[ipcn_tema_filter]` e `[ipcn_archive_vazio]` (HTML simples, sem comentários de bloco).
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — bloco morto trocado pelo filtro, `perPage` inerte fora, nota da consulta herdada.
- `wp-content/themes/ipcn-fse/templates/archive.html` — filtro e vazio pelos shortcodes, `perPage` inerte fora, nota do template que serve Secção e Tema.
- `wp-content/themes/ipcn-fse/patterns/ipcn-card.php` e `patterns/ipcn-card-feature.php` — marcador `span.ipcn-card-notema` depois das duas etiquetas.
- `wp-content/themes/ipcn-fse/templates/single-acervo_ipcn.html` — casa `ipcn-single-tema` no hero e marcador `ipcn-single-notema`.
- `wp-content/themes/ipcn-fse/style.css` — pílula `theme-filter` (contorno muted, `rounded.full`, 44px, `[aria-current="page"]` preenchida em navy) e a visibilidade do marcador, escopada a `.type-acervo_ipcn`.

**Triagem da revisão.** 23 achados de quatro camadas — `high` 0, `medium` 0, `low` 13, `false` 10 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **2 entradas corrigidas por patch:** o marcador de ausência passou a ser escopado ao cartão do Acervo (achados 4 e 19, `low` — a regra sem prefixo lia "Sem Tema" num cartão de Notícias sem categoria) e os dois templates ganharam a nota de que o tamanho de página vem do `posts_per_page` do site (achado 9, `low`). **8 entradas diferidas**, registadas no frontmatter `deferred:` e no `deferred-work.md`: as duas casas da copy do vazio para a 1.10 (achado 6, com a nota do 7), o contrato de classes no `AGENTS.md`/AD-3 (12), a ausência de verificação durável da lógica nova (16, 17), o 404 de uma página fora do intervalo num Tema (15) e a passagem no browser com as descrições vazias na BD (20, 21). **10 rejeitadas por refutação:** o `<p><p>` do `shortcode_unautop` e a dependência dessa ordem (1, 18 — a ordem está na fonte do 6.4.3), o invólucro vazio do `core/post-terms` (2 — `render_block_core_post_terms()` devolve `''` sem termos), o `:has()` sem recurso (3), o vazio do próprio Acervo (7, copy da 1.10), a colação do `orderby` (10), a duplicação das pílulas (11, dois componentes do `DESIGN.md`), o esqueleto do spec (13, vem do renderizador), a ledger do sprint (14, escrita por outro passo) e o `AGENTS.md` no diff (23, alteração do dono). **`followup_review_recommended: false`** — dois patches, ambos `low`, nenhum `high`.

**Verificação.** `bash scripts/check-php.sh` → 17 problemas, todos `block markup` (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1 e 0 entradas `sintaxe:` — idêntico ao baseline; `php -l` limpo em `inc/listings.php`; `style.css` com 181 `{` e 181 `}`. Harness de sessão (fora do deliverable) em 45/45, cobrindo as linhas 1-5 da matriz com stubs e as asserções estáticas de fiação; depois do patch, a asserção do vazio atravessa o `query-no-results` em vez de procurar a string no ficheiro inteiro. Verificado na fonte do 6.4.3: `shortcode_unautop()` antes de `do_shortcode()` em `get_the_block_template_html()`; `render_block_core_post_terms()` vazio sem termos; `inherit` tratado pelos consumidores, nunca pelo construtor de argumentos; `render_block_core_query_pagination()` vazio com uma página; `WP::handle_404()` sem 404 em `is_tax()` com objecto consultado. Sem WordPress local, a passagem no browser fica pendente de deploy e purga.

**Riscos residuais.** (1) O marcador depende de `type-acervo_ipcn`, a classe que o `core/post-template` põe no `<li>` por `get_post_class()`: se o núcleo a mudasse, o marcador deixaria de sair e nada ficaria vermelho — é a mesma lacuna de verificação durável já diferida. (2) A pílula marcada e a ligação de volta assentam em `get_term_link()`/`get_post_type_archive_link()`, não observados por verificação nenhuma do repo. (3) As descrições dos Temas estão vazias na base de dados, pelo que o hero do Tema mostra a frase de recurso até alguém as escrever. (4) Não houve deploy de `stagingredesign` (paragem declarada nas preferências do `AGENTS.md`), pelo que o HTML em revisão ainda é anterior às histórias 1.5/1.6 e não serve de prova deste diff.
