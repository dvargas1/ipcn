---
title: 'Story 1.6 — Notícias puras e o hub das Secções'
type: 'feature'
created: '2026-09-28'
status: 'done'
route: 'dispatch'
review_loop_iteration: 1
baseline_commit: '82dc6566dd8db5c0bc6e07487f77e52a93bdc7e3'
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** A listagem de Notícias — a secção da Home (`front-page.html:41`) e a página `/noticias/` (página WP 2617, conteúdo na BD) — mostra conteúdo que não é notícia. A causa é de código: o `core/query` **ignora `categoryName`** (`build_query_vars_from_query_block()` lê `postType`, `sticky`, `exclude`, `perPage`, `offset`, `categoryIds`, `tagIds`, `taxQuery`, `order`, `orderBy`, `author`, `search`, `parents`), pelo que a consulta corre sem filtro de categoria desde `57825f5`, quando `taxQuery.category:[1]` deu lugar a `categoryName`. Coexiste um defeito de dados (peças de Agenda categorizadas em `noticias`) e o hero das Secções usa um mapa fixo em PHP em vez do termo (AD-8), apesar de todos os termos terem descrição na BD.

**Approach:** Um filtro `query_loop_block_query_vars` em `inc/` lê um atributo de slug no `core/query` e define `category_name` no `WP_Query`, mantendo o block markup a filtrar por slug (AD-7), e aplica-se à Home e ao hub. O tema passa a ser dono do hub num `templates/page-noticias.html` novo, com um pattern curado das Secções; o hero de arquivo passa a ler o nome e a descrição do termo.

## Decisions

- **O filtro por slug vive em `inc/`**: `query_loop_block_query_vars` mapeia o atributo de slug para `category_name`; nunca `term_id` nem `categoryIds` (AD-7).
- **A correcção abrange a Home e o hub**, incluindo o bloco congelado pela 1.5 em `front-page.html:41`.
- **A correcção de dados mantém-se** como passo manual na BD: as peças de Agenda estão mesmo em `noticias`.
- **Hub no tema por página, sem tocar na BD.** `page-{post_name}.html` serve `/noticias/`; a página 2617 fica como âncora do endereço.
- **Secção é conceito editorial; não reestruturamos a BD.** Só `destaques` é filha de `noticias` e mover as restantes mudaria endereços (NFR8).
- **A lista de Secções é um pattern curado no tema**, com os endereços canónicos dos termos.
- **O vazio de Secção fica como está**; a copy é da 1.10 e a acentuação da 1.11.

## Boundaries & Constraints

**Always:** filtro por slug, nunca `term_id`/`categoryIds` (AD-7); o filtro novo só actua quando o atributo de slug está presente; markup block em `templates/` e `patterns/`, comportamento em `inc/` (AD-1, AD-4); listar é `core/query` (AD-2); classes `ipcn-*`; endereços existentes mantêm-se (NFR8).

**Never:** reestruturar categorias na BD; `inc/agenda-block.php` (Épico 2), mu-plugin (AD-6), `inc/forms.php` e `inc/cookie-bar.php` (Épico 3), `theme.json`, `parts/header.html` e `parts/footer.html` (1.8); `WP_Query` novo; markup de cartão à mão em PHP; a copy dos vazios (1.10) e a acentuação (1.11).

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Hub | página `noticias` aberta | hero + sete ligações de Secção + grelha de Notícias | — |
| Listagem filtrada | `core/query` com o atributo de slug | só as publicações do slug, nunca todas as categorias | — |
| Bloco sem o atributo | `core/query` sem atributo de slug | consulta intacta | o filtro não actua |
| Peça de Agenda na listagem | post com `noticias` e `agenda-ipcn` | corrigido na BD (passo manual) | — |
| Nenhuma notícia | 0 posts | "Em breve, novidades por aqui." | `query-no-results` no hub |
| Secção sem conteúdo | 0 posts no termo | frase de vazio do archive, inalterada | sem grelha partida |
| Termo sem descrição | `description` vazia | fallback do hero | nunca imprimir vazio |

</frozen-after-approval>

## Code Map

- `wp-content/themes/ipcn-fse/inc/listings.php` — **o filtro novo**: `query_loop_block_query_vars` mapeia o slug do `core/query` para `category_name` (AD-7); e `ipcn_archive_hero` (l.112-…) passa a ler o termo em vez do mapa fixo (l.120-140). O ficheiro regista `add_shortcode` (l.21, l.112) e um `add_filter` local (l.80); nenhum ficheiro do repo regista `query_loop_block_query_vars`.
- `wp-content/themes/ipcn-fse/templates/page-noticias.html` — **novo**: o hub; `page-{post_name}.html` precede `page.html` para a página `noticias` (WP ≥ 6.4).
- `wp-content/themes/ipcn-fse/patterns/ipcn-seccoes.php` — **novo**: pattern curado com as sete ligações; `Inserter: false`, como os cartões.
- `wp-content/themes/ipcn-fse/style.css` — regras do `.ipcn-seccoes*` (pílulas com alvo ≥ 44px); o idioma existente é o `.ipcn-page` (l.524-541).
- `wp-content/themes/ipcn-fse/templates/front-page.html:41-48` — a secção Notícias da Home fica **sem alteração**: o atributo `categoryName` já lá está e passa a funcionar com o filtro novo.
- `wp-content/themes/ipcn-fse/templates/archive.html:1-12` — arquivo das Secções: hero (l.3), `inherit:true` (l.6), `query-no-results` (l.9-11).
- `wp-content/themes/ipcn-fse/patterns/ipcn-card.php` — cartão `ipcn/card` (16:9), `Inserter: false`.
- Facto do núcleo (WP 6.4): `build_query_vars_from_query_block()` não lê `categoryName`; o filtro `query_loop_block_query_vars` é o ponto de extensão.
- Endereços canónicos dos termos: `/category/noticias/destaques/`, `/category/diaspora/`, `/category/colunistas/`, `/category/notas/`, `/category/editorial/`, `/category/drops-antirracista/`, `/category/memorias/`.
- `scripts/check-php.sh` — única verificação executável; não lê `.html`, não resolve slugs e não corre PHP do tema.

## Tasks & Acceptance

**Execution:**
- [x] `wp-content/themes/ipcn-fse/inc/listings.php` — acrescentar `add_filter( 'query_loop_block_query_vars', … )` que define `category_name` a partir do slug do bloco, só quando presente — é o que faz a filtragem por slug existir, e corrige a Home sem tocar no template.
- [x] `wp-content/themes/ipcn-fse/inc/listings.php` — `ipcn_archive_hero` lê `$q->name` e `$q->description`, com fallback quando vazios, sem mudar as linhas de block markup — cumpre AD-8 e o AC das Secções.
- [x] `wp-content/themes/ipcn-fse/templates/page-noticias.html` — criar o hub: hero navy, o pattern das Secções e um `core/query` com `categoryName:"noticias"`, `ipcn/card`, cabeçalho da grelha, `query-pagination` e `query-no-results`.
- [x] `wp-content/themes/ipcn-fse/patterns/ipcn-seccoes.php` — criar o pattern curado (sete ligações por slug, `Inserter: false`), com o grupo marcado como `nav` e os itens em lista.
- [x] `wp-content/themes/ipcn-fse/style.css` — regras do `.ipcn-seccoes*` (pílulas com alvo ≥ 44px), a partir dos tokens.
- [ ] Passo manual, fora do repo: retirar `noticias`/`destaques` dos posts de Agenda.

**Acceptance Criteria:**
- Given um `core/query` com o atributo de slug, when renderiza, then só aparecem publicações dessa categoria.
- Given a Home, when carrego, then a secção de Notícias mostra só notícias, sem a pauta de Agenda.
- Given `/noticias/` aberto, when carrego, then vejo hero, as sete ligações de Secção, a grelha paginada de Notícias, e o endereço continua a responder.
- Given uma Secção aberta, when leio o topo, then o h1 é o nome do termo e o parágrafo é a descrição do termo.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa e os problemas aceites não crescem.

## Implementation Notes

**Ficheiros.** `templates/page-noticias.html` (novo) — hub: hero navy (`IPCN · Notícias`, h1 `Notícias do IPCN`), `wp:pattern {"slug":"ipcn/seccoes"}`, e `core/query` com `"inherit":false,"categoryName":"noticias"`, `perPage:9`, `ipcn/card` num `core/post-template` de 3 colunas e `query-no-results` com `Em breve, novidades por aqui.`; o `post-content` da página não é renderizado. `patterns/ipcn-seccoes.php` (novo) — `Slug: ipcn/seccoes`, `Inserter: false`; sete ligações por slug (`/category/noticias/destaques/`, `/category/diaspora/`, `/category/colunistas/`, `/category/notas/`, `/category/editorial/`, `/category/drops-antirracista/`, `/category/memorias/`), nenhum `term_id`. `inc/listings.php` — `ipcn_archive_hero` perdeu o mapa fixo e lê `$q->name`/`$q->description`, com fallbacks que nunca imprimem vazio; as linhas de block markup ficaram idênticas ao baseline.

**Eyebrow do hero.** O implementador tinha trocado o eyebrow dinâmico por um rótulo estático (`IPCN · Seção`); revertido para `IPCN · <nome do termo>`, para o texto continuar a vir do termo (AD-8) em vez de uma string fixa em PHP. O h1 passa a repetir o nome do termo — consequência directa do AC, que fixa o h1 no nome do termo.

**Verificação.** `bash scripts/check-php.sh` → 17 problemas, todos `block markup` sob `inc/` (12 em `agenda-block.php`, 5 em `listings.php`), saída 1 — idêntico ao baseline (conferido com `git show 82dc656:… | grep -c '<!-- wp:'`), com 0 entradas `sintaxe:`. `php -l` limpo nos dois ficheiros PHP tocados. `patterns/ipcn-seccoes.php` entra na lista de ficheiros verificados sem acrescentar problema (a regra de block markup só olha a `inc/`).

**Auditoria da matriz.** Sem runner nem CI (NFR9) e sem WordPress local, nenhuma linha da matriz tem teste executável: as de markup são cobertas por inspecção do diff e as de render (hub, vazio, hero do termo) só fecham no browser em `stagingredesign`, após deploy e purga — lacuna preexistente, já diferida na 1.3 e na 1.5.

**Em aberto.** O passo manual da BD (retirar `noticias`/`destaques` dos posts de Agenda) não foi feito: é fora do repo. Até lá, a listagem continua a mostrar Agenda. O template só é escolhido se o slug da página for `noticias` — confirmado por REST (`id 2617`, slug `noticias`).

**Ciclo 1 revertido.** Tudo acima descreve o primeiro ciclo, implementado e depois **revertido** na revisão: o `Review Triage Log` (Grupo I) mostrou que a premissa congelada estava errada — `core/query` ignora `categoryName` — pelo que o filtro por slug não existia. O diff revertido está em `$COMMANDCODE_SCRATCHPAD/1-6.diff`; a re-derivação acrescenta o filtro em `inc/` e a paginação e o cabeçalho no hub.

**Ciclo 2 (derivação actual).** `inc/listings.php` — filtro `query_loop_block_query_vars` (slug → `category_name`, só quando o atributo existe; guarda `instanceof WP_Block`) e hero a ler o termo (guarda `instanceof WP_Term`, fallbacks com `trim`); a contagem de `<!-- wp:` do ficheiro ficou em 5. `templates/page-noticias.html` (novo) — hero, pattern, cabeçalho de grelha (eyebrow + h2 "Últimas notícias"), query com `categoryName:"noticias"`/`perPage:9`, `ipcn/card`, `query-pagination` e `query-no-results`; o `post-content` da página não renderiza. `patterns/ipcn-seccoes.php` (novo) — `nav` (`tagName`) + `core/list` com as sete ligações por slug. `style.css` — pílulas com `min-height:44px` e tokens. `front-page.html` ficou intocado, como previsto.

**Verificação do ciclo 2.** `check-php.sh` → 17 problemas (12 `agenda-block.php` + 5 `listings.php`), 0 `sintaxe:`, saída 1 — idêntico ao baseline; `php -l` limpo. Harness de lógica em `$COMMANDCODE_SCRATCHPAD/verify-listings.php` (fora do deliverable), re-executado por mim: 16/16, cobrindo as linhas da matriz do filtro (slug presente, ausente, só espaços, não-string, sem contexto, não-`WP_Block`) e do hero (nome/descrição do termo, fallbacks, não-termo).

**Eyebrow do hero.** Mantido como no baseline (`IPCN · <nome do termo>`), pelo que o h1 repete o nome. É comportamento pré-existente — o mapa antigo também repetia a palavra (eyebrow `IPCN · Destaques`, h1 `Destaques do IPCN`) — e a spec não fixou política de eyebrow; fica para o passo de copy (1.11).

**Como o hub escolhe o template.** Confirmado por REST que a página 2617 tem slug `noticias`, que é o que faz `page-noticias.html` vencer `page.html`.

## Spec Change Log

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo; **EC** = caça a casos-limite; **VG** = lacunas de verificação.
Veredictos: `high`/`medium`/`low` (defeito real), `false` (refutado), `maybe-false`. Agrupamento e encaminhamento no fim.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 1 | BH | O AC do épico é substituído, não cumprido, e sem renegociação registada | `high` | Real: `epics.md:247-260` pede "só publicações da categoria `noticias`" e "Secção é categoria filha de `noticias`"; o AC da spec trocou-o por três verificações de render, e o passo de dados ficou por fazer. Grupo I. |
| 2 | BH | A decisão contradiz o `epic-1-context.md:45` e o AD-8, que continuam a dizer "categoria filha de `noticias`" | `low` | Real: as duas afirmações convivem no repo. O remédio edita artefactos de planeamento. Grupo D. |
| 3 | BH | `Spec Change Log` e `Review Triage Log` vazios | `low` | Real enquanto redacção deste passo; rejeitado porque o remédio edita esta spec. |
| 4 | BH | O AD-8 volta a ser violado: o hub fixa em template o título e a descrição de `noticias` que saíram do mapa PHP | `low` | Real: `page-noticias.html` escreve "Notícias do IPCN" e a introdução em duro, quando o termo tem descrição. A spec não fixou a fonte dessa copy. Grupo S. |
| 5 | BH | O hub não tem paginação (perPage 9, sem `query-pagination`) | `medium` | Real: as notícias a partir da décima ficam sem rota no hub, que a Home apresenta como canónico. Grupo P. |
| 6 | BH | O bloco de listagem do hub não tem título, ao contrário do da Home | `low` | Real: nove cartões sem cabeçalho que identifique a grelha. Grupo P. |
| 7 | BH | A frase de vazio do hub é a da Home, não a de Notícias/Secção | `low` | Real: `epic-1-context.md:56` separa as duas ("secção mantida" vs "explicação com as restantes Secções"). A copy é da 1.10, pelo que é diferido. Grupo D. |
| 8 | BH | O eyebrow e o h1 do hero imprimem a mesma string | `low` | Real e admitido nas *Implementation Notes*; consequência do AC, mas a política do eyebrow nunca foi escolhida. Grupo P. |
| 9 | BH | Seis Secções mudam de h1 sem registo antes/depois, e só uma é verificada | `low` | Real: a copy do mapa apagado (e os seus títulos) só se recupera pelo git. A verificação das outras cinco é a passagem no browser, já diferida. Grupo D. |
| 10 | BH | Os fallbacks servem os termos que o mapa nunca cobria, e nada prova que tenham descrição | `false` | Refutado: verificado por REST que os sete termos têm nome e descrição (Destaques 46, Diáspora 1, Colunistas 7, Notas 15, Editorial 6, Drops 2, Memórias 2). Os fallbacks ficam como rede, sem disparar. |
| 11 | BH | O slug `drops-antirracista` contraria o conjunto nomeado (`drops`) | `false` | Refutado: o slug na BD é mesmo `drops-antirracista` (REST); é o `ARCHITECTURE-SPINE.md` que está desactualizado. |
| 12 | BH | As classes do pattern são mortas e o pattern inventa um segundo estilo de pílula | `low` | Real: `ipcn-seccoes`/`ipcn-seccoes-links` não têm regra em `style.css`, e o chrome repete-se inline sete vezes. Grupo P. |
| 13 | BH | As sete ligações são sete parágrafos soltos, sem marco de navegação, com alvo abaixo de 44px | `low` | Real: sem `nav`/lista, a tecnologia assistiva não ganha marco, e a pílula fica abaixo do alvo de toque do FR-13 (a 1.9 é a dona do alvo). Grupo P. |
| 14 | BH | A superfície duplicada `/category/noticias/` nunca é tratada | `low` | Real, mas preexistente: o arquivo da categoria já respondia antes desta história. Grupo D. |
| 15 | BH | Ortografia misturada (pt-BR acentuado no novo contra o pt-PT do repo) | `low` | Real: "Seções"/"canônicos" contra "Secções"/"canónicos" do próprio repo, com o vazio do archive sem acento ao lado. A acentuação é da 1.11. Grupo D. |
| 16 | BH | A correcção de dados está indefinida como operação | `medium` | Real: sem consulta de identificação, contagem esperada, base nomeada nem reversão. Grupo I (a raiz é a premissa errada, ver #28). |
| 17 | BH | Nada verifica os contratos dos ficheiros novos | `low` | Real e igual a #27. Grupo P. |
| 18 | BH | Os diferimentos desta história não ficam registados no `deferred-work.md` | `low` | Real: zero entradas com `spec-1-6`. O remédio é o próprio mecanismo de diferimento. Grupo D. |
| 19 | BH | O hub fica acoplado a um invariante não declarado (o slug da página) | `low` | Real: se a página 2617 mudar de slug, cai no `page.html` e ressurge o conteúdo antigo. O remédio edita contexto de agente. Grupo D. |
| 20 | BH | Ledger e spec discordam do estado | `low` | Real e transitório: o ledger diz `in-progress` e a spec `in-review`. É escrito pelo próprio fluxo; rejeitado. |
| 21 | BH | Referências de linha do `Code Map` obsoletas e linhas em falta na matriz (a Home, que é o tema do AC) | `low` | Real enquanto redacção; rejeitado porque o remédio edita esta spec. |
| 22 | EC | A guarda nova deixa entrar um arquivo de post type e imprime o nome de máquina como h1 | `low` | Real a regressão (`name` em vez de `slug`), sem caminho demonstrado no repo: nenhum CPT arquiva pelo `archive.html` (o acervo tem o seu template). O remédio é uma linha (`instanceof WP_Term`). Grupo P. |
| 23 | EC | Nome ou descrição só com espaços passa `! empty()` | `low` | Real no código, não alcançável com os dados actuais. Remédio direto (`trim`). Grupo P. |
| 24 | EC | A descrição do termo com markup imprime etiquetas literais | `low` | Real no código; as descrições actuais são texto simples. Remédio direto. Grupo P. |
| 25 | EC | Com mais de nove notícias não há paginação | `medium` | Duplicado de #5 (mesma raiz). |
| 26 | EC | Endereços `/category/…` em duro quebram se a base mudar | `low` | Real: o pattern fixa a base `category`; a spec não fixou a fonte dos endereços. Grupo S. |
| 27 | VG | O hub e a referência `wp:pattern {"slug":"ipcn/seccoes"}` não são lidos por verificação nenhuma | `low` | Pré-verificado pela camada (demonstrado por mutação de um carácter em cada lado, com `check-php.sh` inalterado). Disposição `patch`. Grupo P. |
| 28 | VG | **O filtro de categoria do `core/query` é inerte: `categoryName` não existe no núcleo** | `high` | Pré-verificado pela camada e **confirmado na fonte do WordPress 6.4**: `build_query_vars_from_query_block()` copia `postType`, `sticky`, `exclude`, `excludeCurrent`, `perPage`, `offset`, `categoryIds`, `tagIds`, `taxQuery`, `format`, `order`, `orderBy`, `author`, `search`, `parents` — nunca `categoryName`. A consulta do hub (e a da Home, desde `57825f5`) corre sem filtro. Grupo I. |
| 29 | VG | O hero derivado do termo não tem teste que o observe | `low` | Pré-verificado (a mutação para o mapa antigo passa `php -l` e o `check-php.sh` idêntico). Disposição `defer`. Grupo D. |

**Agrupamento e encaminhamento**

- **Grupo I — a premissa congelada está errada (#1, #16, #28):** `intent_gap`. O filtro por slug em `core/query` não existe no núcleo (verificado na fonte), pelo que a secção de Notícias da Home lista os posts mais recentes de todas as categorias — é essa a origem da pauta de Agenda, não uma categorização errada na BD. A causa raiz está dentro de `<frozen-after-approval>` (a *Problem* e a decisão "é defeito de dados", mais o "filtrar por slug" sem mecanismo). **Loopback.**
- **Grupo P — o hub ficou áspero e tem contrato por verificar (#5, #25, #6, #8, #12, #13, #17, #22, #23, #24, #27):** `patch` — moot, o código vai ser re-derivado.
- **Grupo S — incoerências entre spec e planeamento (#4, #26):** `bad_spec` — moot pela mesma razão.
- **Grupo D — dívida preexistente ou de outras histórias (#2, #7, #9, #14, #15, #18, #19, #29):** `defer`.
- **Refutados:** #3, #10, #11, #20, #21.

**Cascata:** o Grupo I dispara o loopback (`review_loop_iteration` passa a 1). O código foi revertido — `inc/listings.php` restaurado ao baseline e os dois ficheiros novos removidos — e o `check-php.sh` voltou aos 17 problemas aceites. Os grupos P, S e D ficam moot até a intenção ser renegociada; o diff do trabalho revertido fica em `$COMMANDCODE_SCRATCHPAD/1-6.diff` para a re-derivação.

**Rodada 2** (depois da renegociação e da re-derivação). Os achados reencontrados são confrontados com as linhas acima; os `false` e os rejeitados do ciclo 1 mantêm-se.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 30 | BH | Spec `in-review` contra o ledger `in-progress` | `low` | **carried** de #20: mesmo local e mesma afirmação; o estado é escrito pelo próprio fluxo. Rejeitado. |
| 31 | BH | A spec mistura os ciclos 1 e 2, e as `Tasks` mostram itens "do ciclo 1" | `low` | Real enquanto densidade das notas (append-only por desenho); as `Tasks` são as do ciclo 2 e as notas separam-nos. Rejeitado: o remédio edita esta spec. |
| 32 | BH | O relato da reversão contradiz o próprio patch | `false` | As entradas do ciclo 1 descrevem o estado *daquele* momento e as notas marcam o ciclo 2 como a derivação actual. |
| 33 | BH | `Spec Change Log` vazio, apesar da renegociação | `false` | Por desenho, o `Spec Change Log` é do loopback `bad_spec`; a renegociação está nas *Decisions* e neste registo. |
| 34 | BH | O AC da Home não se sustenta sem o passo de dados | `low` | Real enquanto redacção: o AC assume a tarefa de dados, que está listada e aberta. Rejeitado: o remédio edita esta spec. |
| 35 | BH | O passo de dados não está definido como operação | `low` | Real e de baixo dano: é uma acção editorial de uma só vez, conferível pela própria listagem; o remédio é documentação, mais do que uma correcção directa. Rejeitado. |
| 36 | BH | O hub fixa a copy do hero em template (AD-8) | `false` | **Supersede #4**, refutado: o AD-8 governa o texto de apresentação de uma **Secção** (termo), não o de uma página; o hub é a página `noticias` e a sua copy reproduz o que a página 2617 já renderizava. |
| 37 | BH | O docblock diz que o filtro corre também pela paginação | `false` | Refutado: o `build_query_vars_from_query_block()` é usado pelo Query Loop, pelo `query-pagination-numbers` e pelo `query-pagination-next` — o docblock está certo. |
| 38 | BH | Nada guarda a armadilha de o editor descartar `categoryName` | `low` | Real e já documentado nas *Design Notes*; o remédio é um detector, mais do que uma correcção directa. Rejeitado. |
| 39 | BH | Hex `#a85a32` em vez de token | `false` | Refutado: a terracota **não** é token no `theme.json` e o `AGENTS.md` manda não inventar slugs para ela; o hex é o padrão do tema. |
| 40 | BH | Ortografia misturada (pt-BR no novo, pt-PT no repo) | `low` | Duplicado de #15; a acentuação é da 1.11 → diferido. |
| 41 | BH | Selectores redundantes no `style.css` e rede que vestiria listas alheias | `low` | Real; **patch** aplicado (selectores reduzidos a `.ipcn-seccoes-links`). |
| 42 | BH | Pílulas sem `:focus` e `list-style:none` | `false` | Refutado: o anel duplo global da 1.4 cobre `a:focus-visible`, e `list-style:none` em listas já é convenção do tema (`style.css:206-207`). |
| 43 | BH | `Code Map` com linhas obsoletas e sem a Home | `low` | Real a densidade; falso o "sem a Home" (`front-page.html:41-48` está no mapa). Rejeitado: o remédio edita esta spec. |
| 44 | BH | As provas vivem em caminhos do scratchpad | `low` | Real: um harness durável é a infraestrutura de verificação já diferida na 1.3/1.5 → diferido. |
| 45 | BH | `context:` escasso | `low` | Rejeitado: o remédio edita esta spec. |
| 46 | BH | Diferimentos não registados no `deferred-work.md` | `low` | Real; fechado neste passo (entradas acrescentadas). |
| 47 | BH | A matriz não tem linha para `categoryName` vazio ou só espaços | `low` | O comportamento está coberto no harness e nas notas; o remédio edita esta spec. Rejeitado. |
| 48 | EC | `categoryName` não-string (array/int) deixa a consulta sem filtro, em silêncio | `low` | Real no código, sem estado demonstrado (nenhum bloco o faz); o remédio acrescenta ramos → rejeitado. |
| 49 | EC | `categoryName` a par de `categoryIds`/`taxQuery` | `false` | Nenhum bloco do repo traz os dois; sem estado demonstrado não há defeito vivo. |
| 50 | EC | Descrição do termo com markup sai com etiquetas literais | `low` | Real; **patch** aplicado (`wp_strip_all_tags`). |
| 51 | EC | Hrefs `/category/…` escritos em duro | `false` | **Supersede #26**, refutado: hrefs root-relative são a convenção do tema (`front-page.html`, `header.html`, `footer.html`, `404.html`). |
| 52 | EC | O AC da Home não se sustenta porque as peças estão mesmo em `noticias` | `low` | Duplicado de #34. |
| 53 | EC | `category_name` deixa `include_children` a true, logo `noticias` inclui `destaques` | `false` | Não é defeito: o AC pede "publicações da categoria `noticias`" e `destaques` é filha de `noticias`, parte de Notícias no modelo. |
| 54 | VG | O filtro novo não é observado por teste nenhum do repo | `low` | Pré-verificado; diferido (sem runner nem CI, NFR9) — a mesma lacuna da 1.3/1.5. |
| 55 | VG | O hero derivado do termo não é observado por teste nenhum | `low` | Duplicado de #29/#54; diferido. |
| 56 | VG | As referências cruzadas do hub (template, slug do pattern, hooks de CSS) não são verificadas | `low` | Pré-verificado; diferido (mesma infraestrutura da 1.3/1.5). |
| 57 | VG | A pré-visualização do editor não é filtrada (o filtro só afecta o frontend) | `low` | Real e inerente ao mecanismo escolhido; o remédio seria outro mecanismo → rejeitado, registado como dívida. |

**Agrupamento e encaminhamento (rodada 2)**

- **Patch aplicado:** #41, #50 — os dois no `style.css` e no `inc/listings.php`.
- **Diferido (registado no `deferred-work.md`):** #40 (ortografia, 1.11), #44/#54/#55 (sem verificação durável da lógica nova), #56 (referências cruzadas do hub).
- **Rejeitados:** #30–#35, #38, #43, #45, #47, #48, #57.
- **Refutados:** #32, #33, #36, #37, #39, #42, #49, #51, #53.
- **Sem `intent_gap` nem `bad_spec` sobreviventes** — não há loopback (`review_loop_iteration` fica 1). `#4` e `#26`, cuja rota era `bad_spec` no ciclo 1, foram refutados com prova (`#36`, `#51`) em vez de reaplicados, porque a sua premissa não se sustenta; o desvio à regra de `carried` fica assim explícito. Depois dos patches: `check-php.sh` nos 17 aceites e o harness em 16/16.

## Design Notes

**Porquê um filtro e não IDs.** O `core/query` só sabe filtrar por ID de termo (`categoryIds`/`taxQuery`), o que muda entre ambientes — a razão de ser do AD-7. O filtro `query_loop_block_query_vars` é o ponto de extensão que o núcleo expõe: lê o slug do bloco e define `category_name`, e o markup continua declarativo por slug. **Armadilha:** `categoryName` não é um atributo declarado do bloco, pelo que salvar o template no editor pode descartá-lo em silêncio; o filtro só actua quando o atributo existe, e nada fica vermelho se ele desaparecer.

**Porquê uma página e não o arquivo.** A base das categorias é `category`, logo os arquivos das Secções vivem em `/category/...` e `/noticias/` é a página 2617. `page-{post_name}.html` põe o tema a servir esse endereço sem mexer na BD nem perder o endereço (NFR8). A alternativa — retirar a página e redireccionar para `/category/noticias/` — mudaria de facto o endereço da listagem.

**Porquê um pattern curado.** As Secções não estão sob `noticias` na BD (só `destaques`), logo não são deriváveis. Há páginas duplicadas por Secção na BD (ex.: `/noticias/destaques-3/`): dívida de dados, não desta história.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 `block markup` aceites sob `inc/` (12 em `agenda-block.php`, 5 em `listings.php`), saída 1, nenhuma entrada `sintaxe:`.
- `php -l wp-content/themes/ipcn-fse/inc/listings.php` — esperado: sem erros.

**Manual checks (if no CLI):**
- Sem WordPress local, o filtro, o hub e o hero do termo fecham no browser em `stagingredesign`, após deploy e purga (`?nocache=1`): a secção de Notícias da Home (sem a pauta de Agenda), `/noticias/` (hero, sete ligações, grelha paginada) e uma Secção (`/category/colunistas/`) com o h1 e o parágrafo do termo.
