---
title: 'Story 1.10 — Os estados vazios e a página 404'
type: 'feature'
created: '2026-09-29'
status: 'done'
baseline_revision: '17300f26e3784a76cd84d03b44b05148e405310a'
review_loop_iteration: 0
followup_review_recommended: false
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
warnings:
  - multiple-goals
  - oversized
deferred:
  - summary: >-
      Os archives de autor e data servidos por `templates/archive.html` não têm `h1`: o `[ipcn_archive_hero]` devolve `''` quando o objecto consultado não é `WP_Term`.
    evidence: >-
      Achados BH4 (revisão cega) e EC3 (casos-limite) da 1.10, confirmados na fonte: a guarda `WP_Term` de `inc/listings.php` existe para não imprimir o nome de máquina do post type como h1, mas deixa esses archives sem cabeçalho de nível 1. É pré-existente — a 1.10 subiu o hero do Acervo a `h1` e levantou o título das páginas institucionais, e o AC fala de nove templates, cada um com uma fonte de `h1`; o comportamento por URL nestes archives fica por decidir. Fecha-se com uma decisão de desenho para o hero desses archives ou com a passagem no browser que os percorra.
    location: >-
      wp-content/themes/ipcn-fse/inc/listings.php:160-162
    severity: low
  - summary: >-
      A verificação durável do markup novo e do CSS novo continua a não existir: o `check-php.sh` não lê `.html` nem `.css`, e uma mutação no skip link, no `main#conteudo` ou no `h1` por página deixa o repositório inteiro verde.
    evidence: >-
      Achados VG1, VG2, VG3, BH5 e BH17 da 1.10. A camada de lacunas de verificação demonstrou por mutação: apagar o bloco `ipcn-skip` de `parts/header.html`, deixar `.ipcn-skip a { top:-90px }` sem o revelar em foco, ou tirar `anchor:"conteudo"` de uma template deixa o `check-php.sh` nos mesmos 17 problemas e o `check-php.test.sh` a 14/14 — nenhuma asserção do repositório observa o markup do tema nem executa o seu PHP. É a mesma infraestrutura já diferida pela 1.3, 1.5, 1.6, 1.7, 1.8 e 1.9, e o NFR9 declara «sem build step, testes ou CI». Fecha-se com um harness durável em `scripts/`, no estilo do `check-php.test.sh`, que enumere `templates/*.html` e carregue `inc/listings.php` com stubs.
    location: >-
      scripts/check-php.sh
    severity: low
  - summary: >-
      O piso de acessibilidade do par de UX não foi emendado: o `EXPERIENCE.md` continua a dizer «cabeçalho incluído» na ordem de leitura e não declara o contrato de landmark.
    evidence: >-
      Achado BH8 da 1.10. O `Fix` do F-17 (`review-accessibility.md:151`) pede também a emenda de «cabeçalho incluído» para «cabeçalho após a ligação de salto» e o contrato do `main` com `id`. O `EXPERIENCE.md` é artefacto de planeamento congelado, corrigido na fonte e não à mão — como já ficou registado para o `epic-1-context.md` e o `AGENTS.md`. Fecha-se num refresh dos docs de planeamento.
    location: >-
      _bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/EXPERIENCE.md:146
    severity: low
  - summary: >-
      Os restantes compromissos do UX-DR20 (estados de formulário com `autocomplete`/`aria-invalid`/`aria-describedby`, anúncio do envio, texto alternativo e movimento reduzido) continuam sem dono no Épico 1.
    evidence: >-
      Achado BH10 da 1.10. A história tomou a única parcela do UX-DR20 sem dono — o skip link — e o resto pertence à superfície dos formulários (histórias 3.1-3.3: contrato, `aria-invalid`/`aria-describedby`, retenção dos valores) ou ao cartão (texto alternativo). Nenhum AC do Épico 1 os nomeia. Fecha-se quando o Épico 3 os entregar e a passagem de revisão os confirmar.
    location: >-
      _bmad-output/planning-artifacts/epics.md:108
    severity: low
  - summary: >-
      A passagem no browser que fecha os AC da 1.10 fica pendente de deploy e purga em `stagingredesign`, que são acção do dono.
    evidence: >-
      Riscos residuais do `## Auto Run Result` da 1.10. Os AC pedem geometria e copy renderizadas — o chip do skip link visível ao foco, o `main#conteudo` no HTML servido, o vazio das Secções com a ligação ao hub, o `h1` das institucionais e do Acervo, e a cadência full-bleed da Home depois do novo grupo envolvente — e o repositório não as mede. A lista do que verificar está na secção `## Verification` da spec.
    severity: low
---

<intent-contract>

## Intent

**Problem:** As superfícies do Épico 1 não têm um estado vazio coerente: o Acervo diz "Ainda nao ha itens publicados no acervo." em vez da copy do épico, e uma Secção vazia não oferece as restantes Secções (o vazio da Home e a frase da 404 já cumprem). Além disso, o piso de acessibilidade do UX-DR20 — skip link como primeiro elemento focável, landmark `main` com `id` e um `h1` por página — continua sem dono: a 1.8 remeteu-o para "1.9 ou 1.10" e a 1.9 (cujo contrato diz "vão para a 1.10") para esta história, a última das superfícies públicas. Não existe hoje skip link, `main` nem `id` de conteúdo em ficheiro nenhum; `/acervo/` e as páginas institucionais não têm `h1`.

**Approach:** Fechar os vazios nomeados com a copy do épico onde ela é exacta e com uma oferta de saída nos restantes, e assentar o piso de acessibilidade: um skip link no template part do cabeçalho, um landmark `main` com `id="conteudo"` em cada template (reutilizando o grupo único onde já existe, envolvendo a região de conteúdo onde há vários) e um `h1` por página. O bloco de 404 já cumpre a frase e o caminho de volta; recebe só o landmark.

## Boundaries & Constraints

**Always:** o diff é `templates/`, `parts/header.html`, `style.css` e `inc/listings.php`; a copy dos vazios é a do AC quando ele a nomeia ("Em breve, novidades por aqui.", "Em breve, novos itens do acervo."); o texto novo é escrito em português do Brasil (a passagem global de acento é da 1.11); o alvo do skip link é `#conteudo` e o `main` leva exactamente esse `id`, em todas as nove templates; o `h1` por página é feito subindo um cabeçalho já existente, nunca inventando um; o anel de foco da 1.4 (`style.css:915-919`) é o único regime de foco; a medida de leitura, o bloco de reflow da 1.9 e os 17 problemas aceites do `check-php.sh` ficam intactos; o `main` que envolve vários blocos é `align:full` com `blockGap:0`, para as secções full-bleed continuarem a sangrar e não nascer um espaçamento entre elas.

**Never:** tocar em `theme.json`, `functions.php`, `inc/setup.php` ou no mu-plugin; introduzir JavaScript; mexer no interior do painel de cookies (3.6); o cabeçalho pegajoso (decidido não fazer na 1.9); arrumar as declarações mortas do CTA (`style.css:524-530`) ou a regra genérica `.has-background` da grelha (`style.css:129`) — dívida sem dono, registada; construir o acordeão; dar estado vazio aos archives por tag/data/autor (`index.html`) — fora do conjunto nomeado pelo UX-DR13; mudar endereços; duas grelhas iguais seguidas; qualquer superfície banida.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Home sem Notícias | home, `categoryName:noticias` sem publicações | a secção mantém-se e diz "Em breve, novidades por aqui." | — |
| Notícias vazias | `/noticias/` sem publicações | a listagem explica a ausência; as Seções da mesma página (pattern `ipcn/seccoes`) são a oferta | — |
| Secção vazia | `/category/<seccao>/` sem publicações | explica a ausência e liga ao hub das Seções | hub não resolvido → fica só a frase |
| Acervo vazio | `/acervo/` sem itens | "Em breve, novos itens do acervo." | — |
| Tema sem itens | `/temas/<slug>/` sem peças | inalterado (1.7): explica e oferece o Acervo inteiro | — |
| Endereço errado | qualquer URL inexistente | a 404 diz uma frase e dá o caminho de volta ao início | — |
| Primeira tabulação | qualquer página, teclado | o skip link é o primeiro focável, visível ao receber foco, e `#conteudo` existe | `#conteudo` ausente → foco não salta |
| Orientação de títulos | `/acervo/`, páginas institucionais | passam a ter um `h1` visível | — |

</intent-contract>

## Code Map

- `wp-content/themes/ipcn-fse/parts/header.html` — o cabeçalho de todas as páginas: um grupo exterior (linha 1) com a marca, o `wp:navigation ref:5358` e as pílulas CTA. É aqui que entra o skip link, antes do grupo da marca; o `<header>` que envolve o part vem da `area` declarada em `theme.json:63`.
- `wp-content/themes/ipcn-fse/templates/404.html` — um único grupo (`subtle`, `padding 80/80`, `contentSize:720px`) com `h1` "Página não encontrada" e "Volte para a página inicial.": a frase e o caminho já cumprem o AC; falta o landmark.
- `wp-content/themes/ipcn-fse/templates/page.html` — grupo único com `wp:post-title` **escondido** por `className:"ipcn-hero-alone"` (`style.css:339`) e sem `level` (o omissão do bloco é `h2`): é a causa de as páginas institucionais não terem `h1`. `docs/auditoria-redesign-fse-2026-09-17.md:226` já recomenda verificar se a classe ainda é necessária.
- `wp-content/themes/ipcn-fse/templates/index.html` — grupo único (`padding 40/40`): arquivo de recurso (tag/data/autor), com `wp:query-title` (nível 1 por omissão). Recebe o landmark; não recebe estado vazio (fora do conjunto nomeado).
- `wp-content/themes/ipcn-fse/templates/front-page.html` — cinco secções de topo (hero l.4-26, Notícias l.29-50 com o vazio da l.46, vitrine do Acervo l.57-85 com a copy da l.75, Agenda l.88-102, faixa de contacto l.105-119). Os vazios de Notícias e de Acervo já têm a copy do épico.
- `wp-content/themes/ipcn-fse/templates/page-noticias.html` — hero `navy` com `h1` (l.11), `wp:pattern {"slug":"ipcn/seccoes"}` (l.32) e a listagem com `categoryName:"noticias"`; o vazio da l.42 é uma frase solta, sem a oferta de Secções (que está na mesma página, acima).
- `wp-content/themes/ipcn-fse/templates/archive.html` — serve as Secções **e** `/temas/<slug>/` (AD-8): o hero vem de `[ipcn_archive_hero]` e o vazio de `[ipcn_archive_vazio]` (`inc/listings.php:281-299`).
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — hero `navy` com `<h2>` (l.20, sem `h1` na página) e o vazio próprio da l.33.
- `wp-content/themes/ipcn-fse/templates/single.html` e `single-acervo_ipcn.html` — dois grupos de topo cada; o `wp:post-title` já é `level:1` em ambos.
- `wp-content/themes/ipcn-fse/inc/listings.php` — l.281-299 `[ipcn_archive_vazio]`: o ramo não-Tema devolve `<p>Ainda nao ha posts nesta secao.</p>` (l.285); o ramo do Tema (l.288-297) já explica e liga ao Acervo por `get_post_type_archive_link()` — o modelo a seguir para o link do hub.
- `wp-content/themes/ipcn-fse/style.css` — l.338-339 a regra `.wp-block-post-title.ipcn-hero-alone { display:none }` a apagar; l.915-919 o anel de foco (não tocar); l.1091-1183 o bloco REFLOW da 1.9 (não tocar); l.24 `.wp-site-blocks > header .wp-block-group { align-items:center }` — o skip link é um `p`, não um `group`, e sai do fluxo, pelo que não é afectado.
- `wp-content/themes/ipcn-fse/theme.json` — `useRootPaddingAwareAlignments: true` com padding de raiz 20px: é o que faz `align:full` de primeiro nível sangrar; um grupo `main` `align:full` herda o mesmo comportamento.
- `scripts/check-php.sh` — corre `php -l` e as duas verificações estruturais só sob `inc/`; **não lê `.html`** nem `.css`, pelo que nada do markup novo é observado. Estado aceite: 17 problemas (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`).

## Tasks & Acceptance

**Execution:**
- `wp-content/themes/ipcn-fse/parts/header.html` — acrescentar, como primeiro bloco e antes do grupo da marca, `<!-- wp:paragraph {"className":"ipcn-skip"} -->` com `<a href="#conteudo">Pular para o conteúdo</a>` — o skip link é o primeiro elemento focável de todas as páginas.
- `wp-content/themes/ipcn-fse/style.css` — vestir `.ipcn-skip` (fora do fluxo até receber foco; ao foco, um chip visível com `base`/`navy` e o raio do tema) e apagar a regra `.wp-block-post-title.ipcn-hero-alone` e o comentário da l.338, que ficam sem consumidor.
- `wp-content/themes/ipcn-fse/templates/404.html`, `templates/page.html`, `templates/index.html` — no grupo único de conteúdo, acrescentar `"tagName":"main"` e `"anchor":"conteudo"` — o landmark e o alvo do skip link, sem reestruturar nada.
- `wp-content/themes/ipcn-fse/templates/front-page.html`, `templates/page-noticias.html`, `templates/archive.html`, `templates/archive-acervo_ipcn.html`, `templates/single.html`, `templates/single-acervo_ipcn.html` — envolver a região de conteúdo (do hero até ao último bloco antes do part do rodapé) num `<!-- wp:group {"tagName":"main","anchor":"conteudo","align":"full","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->` — `align:full` mantém as secções full-bleed (o `alignfull` dos filhos deixa de casar com `.wp-site-blocks > .alignfull`) e `blockGap:0` impede que o `is-layout-flow` do novo grupo introduza 24px entre secções.
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — o `<h2>Memoria e historia do IPCN</h2>` da l.20 passa a `h1` (é o título da página) e a copy do vazio da l.33 passa a "Em breve, novos itens do acervo." — o AC nomeia a frase e a página não tinha `h1`.
- `wp-content/themes/ipcn-fse/templates/page-noticias.html` — a frase do vazio (l.42) passa a explicar a ausência; as Seções oferecidas são as do pattern `ipcn/seccoes`, já desenhadas acima na mesma página (um link para a própria página seria um laço).
- `wp-content/themes/ipcn-fse/templates/page.html` — no `wp:post-title`, acrescentar `"level":1` e remover `className:"ipcn-hero-alone"` — as páginas institucionais ganham o `h1` que hoje está escondido.
- `wp-content/themes/ipcn-fse/inc/listings.php` — no ramo não-Tema de `[ipcn_archive_vazio]`, devolver a explicação mais uma ligação ao hub das Seções, resolvida por slug (`get_page_by_path('noticias')` → `get_permalink()`), omitida quando não resolve — é o mesmo guard do ramo do Tema e mantém a AD-7 (nenhum endereço escrito à mão).

**Acceptance Criteria:**
- Given a Home sem publicações em `noticias`, when a página carrega, then a secção Notícias mantém-se e diz "Em breve, novidades por aqui.".
- Given `/noticias/` ou uma Secção sem publicações, when a página carrega, then a listagem explica a ausência e as restantes Seções são alcançáveis (pattern na página, ou ligação ao hub resolvida por slug).
- Given `/acervo/` sem itens, when a página carrega, then o vazio diz "Em breve, novos itens do acervo.".
- Given um endereço inexistente, when a 404 é servida, then diz uma frase e dá o caminho de volta ao início.
- Given qualquer página do tema, when se tabula a partir do topo, then o primeiro focável é o skip link, visível ao receber foco, e `#conteudo` existe e é o `main`.
- Given as nove templates, when cada painel é inspeccionado, then tem exactamente um `main` com `id="conteudo"` e um `h1` visível.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa e os 17 problemas aceites não crescem.

## Spec Change Log

Sem alterações ao contrato. As decisões do implementador ficam registadas durante a implementação.

### Decisões de implementação (Story 1.10)

- **Skip link.** O `p.ipcn-skip` entra como primeiro bloco do grupo exterior de `parts/header.html`, antes da marca; o `<header>` que o envolve vem da `area` de `theme.json`. Em `style.css`, o `p` é `position:absolute` (fora do fluxo, para não acrescentar um intervalo acima da marca) e a ligação fica 90px *acima* do topo — não à esquerda — a descer a chip visível em `:focus`; a escolha evita abrir scroll horizontal a 320px (piso fixado pela 1.9). O alvo é `#conteudo`; o chip usa `base`/`navy` e o raio do tema (`--wp--custom--radius`).
- **Landmark `main`.** Nas três templates de grupo único (`404.html`, `page.html`, `index.html`) o próprio grupo vira `tagName:main` + `anchor:conteudo` (o `<div>` salvo passa a `<main id="conteudo">`). Nas outras seis, um grupo envolvente `align:full` com `blockGap:0` envolve a região de conteúdo (hero → último bloco antes do part do rodapé), mantendo o sangramento full-bleed e sem introduzir 24px entre secções.
- **Vazios.** `archive-acervo_ipcn.html` sobe o `h2` do hero a `h1` e adopta "Em breve, novos itens do acervo."; `page-noticias.html` troca a frase solta por "Ainda não há notícias publicadas. Explore as Seções do IPCN logo acima." (as Secções oferecidas são o pattern já desenhado acima — um link para a própria página seria um laço). O ramo não-Tema de `[ipcn_archive_vazio]` (`inc/listings.php`) passa a explicar a ausência e a ligar ao hub `/noticias/` por `get_page_by_path('noticias')` → `get_permalink()`, com o mesmo guard do ramo do Tema: a ligação só sai quando a página resolve (AD-7).
- **`h1` das institucionais.** `page.html` levanta o `wp:post-title` a `level:1` e larga `className:"ipcn-hero-alone"`; a regra `.wp-block-post-title.ipcn-hero-alone` e o seu comentário saem de `style.css` por ficarem sem consumidor.

### 2026-09-29 — Correções da passagem de revisão (o contrato não muda; a cláusula do `blockGap` fica superada)

- **`blockGap:0` retirado das seis envolventes (BH6, `medium`).** A cláusula `Always` do contrato pede `blockGap:0` «para as secções full-bleed continuarem a sangrar e não nascer um espaçamento entre elas», e a nota de desenho dizia que ele impedia 24px. É falso para o repositório: `style.css:578-583` documenta que «o layout de fluxo do core põe `margin-block-start` a partir do segundo filho» e `style.css:657-661` que o tema conta com esse `blockGap` (`theme.json:27`, 1.5rem) — «os dois colapsam». As cinco secções da Home já tinham 24px entre si; o grupo envolvente herda o mesmo `--wp--style--block-gap`, logo `blockGap:0` só podia **retirar** essa separação. Removido de `front-page.html`, `page-noticias.html`, `archive.html`, `archive-acervo_ipcn.html`, `single.html` e `single-acervo_ipcn.html`; `align:full` mantém-se (é ele que preserva o sangramento). Estado mau evitado: 24px de cadência perdidos em silêncio em todas as superfícies de várias secções. **KEEP:** o wrapper `align:full` + `layout:default` e a nota de desenho sobre o sangramento.
- **A copy nomeada do vazio fica só nas Secções (BH3/EC1, `low`).** `templates/archive.html` serve também os archives de tag, autor e data, que recebiam «nesta Seção» e a ligação ao hub. Guarda `if ( ! is_category() )` devolve a frase de sempre, verbatim. **KEEP:** a explicação, a ligação ao hub resolvida por slug e o guard do `WP_Post`.
- **Docblock de `[ipcn_archive_vazio]` (BH13, `low`).** Dizia que «nos restantes archives devolve a frase actual verbatim» e que «a copy da Seccao continua a ser decidida na 1.10»; passou a descrever os três ramos.
- **Chip do skip link em `:focus-visible` (BH20, `low`).** O reveal estava em `:focus` enquanto o tema tem um só regime de foco (`:focus-visible`, `style.css:915-919`); alinhado. **KEEP:** o `top:-90px` (fora do ecrã pelo topo, para não abrir scroll horizontal a 320px).

## Review Triage Log

### 2026-09-29 — Review pass

- verdicts: 30 findings — high 0, medium 1, low 26, false 3, maybe-false 0
- findings:
  - `[low]` `[reject]` **BH1** citações de linha da spec desactualizadas (`style.css:915-919` cai agora noutra regra) — o remédio é prosa da spec; o anel de foco que a citação aponta não foi tocado e o harness afirma-o por conteúdo.
  - `[low]` `[reject]` **BH2** os ponteiros `style.css:129` e `:524-530` — mesma classe de BH1: são prosa da spec, e a dívida que nomeiam (declarações mortas do CTA, regra genérica `.has-background`) continua intacta.
  - `[low]` `[patch]` **BH3** `templates/archive.html` serve também os archives de tag, autor e data, logo a copy nova e a ligação ao hub apareciam fora das Secções — corrigido: guarda `if ( ! is_category() )` devolve a frase de sempre, verbatim; verificado por execução (`verify-vazios.php`, caso E).
  - `[low]` `[defer]` **BH4** os archives de autor e data ficam sem `h1` (o hero devolve `''` fora de `WP_Term`) — pré-existente (a guarda `WP_Term` já lá estava) e o AC fala de nove *templates*, cada um com uma fonte de `h1`; no `deferred`.
  - `[low]` `[defer]` **BH5** o AC do `h1` não tem verificação durável — é a infraestrutura sem dono já diferida pela 1.3 a 1.9 (NFR9); no `deferred`, com o que o fecharia.
  - `[medium]` `[patch]` **BH6** `blockGap:0` no grupo envolvente — **corrigido**: as cinco secções da Home já tinham 24px entre si (o layout de fluxo do core põe `margin-block-start` a partir do segundo filho, documentado em `style.css:578-583` e `:657-661`), e o wrapper herda o mesmo `--wp--style--block-gap`; o `blockGap:0` só podia **retirar** essa separação. Removido das seis envolventes; `align:full` mantido.
  - `[false]` `[reject]` **BH7** `#conteudo` sem `tabindex`/`scroll-margin` — a navegação por fragmento muda o ponto de partida do foco sequencial mesmo sem `tabindex`, e nada fixo cobre o topo da página (a única barra fixa, a de cookies, está no fundo); o mau resultado descrito não ocorre.
  - `[low]` `[defer]` **BH8** a emenda da frase do Accessibility Floor do par de UX («cabeçalho incluído» → «após a ligação de salto») — artefacto de planeamento congelado, corrigido na fonte, como já ficou registado para o `epic-1-context.md` e o `AGENTS.md`; no `deferred`.
  - `[low]` `[reject]` **BH9** o menu mobile aberto e o `#wpadminbar` — os estados não são a superfície do AC (visitante anónimo, menu fechado) e a parte do menu é o **F-18**, achado próprio que nenhuma história do Épico 1 reclama e que o bloco de navegação, hoje, nem renderiza (diferido da 1.8).
  - `[low]` `[defer]` **BH10** as restantes parcelas do UX-DR20 (estados de formulário, texto alternativo, movimento reduzido) — pertencem à superfície dos formulários (3.1-3.3) e ao cartão; nenhum AC do Épico 1 as nomeia; no `deferred`.
  - `[low]` `[patch]` **BH11** `deferred: []` contradizia as exclusões registadas em prosa — corrigido: o `deferred` do frontmatter foi preenchido com cinco entradas e `deferred-work.md` recebeu as entradas da 1.10, incluindo o fecho das duas que apontavam para esta história.
  - `[low]` `[reject]` **BH12** o 404 de `/temas/<slug>/page/N/` além da última página — é o comportamento do núcleo para páginas fora do intervalo, e a superfície que a 1.10 entrega é exactamente essa 404 (uma frase e o caminho de volta); forçar ali o estado vazio exigiria um `pre_handle_404` que nenhum AC pede. Fechado no ledger.
  - `[low]` `[patch]` **BH13** o docblock de `[ipcn_archive_vazio]` ficava falso («nos restantes archives devolve a frase actual verbatim», «a copy da Seccao continua a ser decidida na 1.10») — corrigido para descrever os três ramos.
  - `[false]` `[reject]` **BH14** regime de acento misto — é a entrega registada à 1.11 (a passagem global de acento, `deferred-work.md`), e a fronteira da história diz que só o texto **novo** é acentuado.
  - `[low]` `[reject]` **BH15** com o hub por resolver o vazio «não oferece» nada — o estado não é demonstrável (a página 2617 existe com slug `noticias`, verificado na 1.6), o mesmo padrão de degradação é o do ramo do Tema, e o remédio (cadeia com alternativa) é mais do que uma correcção directa.
  - `[low]` `[reject]` **BH16** as bases de caminho dos comandos da spec — o remédio é prosa da spec.
  - `[low]` `[defer]` **BH17** as únicas asserções são greps sobre o texto-fonte — a mesma infraestrutura sem dono (VG1-VG3); no `deferred`.
  - `[false]` `[reject]` **BH18** o vazio da Agenda sem cobertura — é entrega das histórias 2.2 e 2.3 («A agenda está sendo montada») e nenhum AC da 1.10 o nomeia.
  - `[low]` `[reject]` **BH19** os *manual checks* não percorrem todas as superfícies — o remédio é prosa da spec; as superfícies em falta ficam listadas nos riscos residuais do `## Auto Run Result`.
  - `[low]` `[patch]` **BH20** o chip do skip link revelava-se em `:focus` enquanto o tema tem um só regime, `:focus-visible` — corrigido (uma palavra).
  - `[low]` `[reject]` **BH21** o tema perder para uma cópia guardada na base de dados — refutado: `ipcn-hero-alone` só existia em `templates/page.html` e `style.css` (grep no repositório) e o modelo de entrega das histórias 1.5 a 1.9 é o dos ficheiros do tema.
  - `[low]` `[reject]` **BH22** frontmatter e `sprint-status.yaml` — o `## Review Triage Log` vazio era o estado desta própria passagem; a chave acentuada contra o ficheiro sem acento é a incompatibilidade de método já registada duas vezes (`deferred-work.md`), a reconciliar a montante, e nenhum passo deste workflow escreve aquele ledger.
  - `[low]` `[patch]` **EC1** — ver BH3 (mesma causa, mesma correcção).
  - `[low]` `[reject]` **EC2** `get_page_by_path` pode devolver uma página não publicada — o estado não é demonstrável (a página está publicada) e o remédio acrescenta uma condição a um guard cujo irmão, o do Tema, tem exactamente a mesma forma.
  - `[low]` `[defer]` **EC3** — ver BH4.
  - `[low]` `[defer]` **VG1** a guarda do hub só é exercitada por um harness de sessão — a camada demonstrou por mutação que o repositório fica verde; é a infraestrutura diferida pela 1.3 a 1.9 (o `check-php.sh` não lê `.html`, não corre o PHP do tema e o NFR9 declara «sem build step, testes ou CI»), e o remédio não é trivial nem entra em `patch`; no `deferred`.
  - `[low]` `[defer]` **VG2** o skip link e o `main#conteudo` afirmados só por greps — idem VG1; no `deferred`.
  - `[low]` `[defer]` **VG3** o `h1` por página sem verificação — idem VG1; no `deferred`.
  - `[low]` `[reject]` **VG4** o harness de sessão falha por `$SCRATCH` não exportado — artefacto da scratchpad, fora do diff revisto; correu 39/39 com o caminho exportado, e a verificação durável que ele substitui é a que está diferida.
  - `[low]` `[reject]` **IA1** o intento vive no plano e o diff no código: `sprint-status.yaml` intocado — o ledger não é artefacto deste workflow (nenhum passo o escreve) e a divergência ledger/disco é a mesma incompatibilidade de chave contra ficheiro; a leitura implementada — a história nomeada do épico, 1.10 = «Os estados vazios e a página 404» — é a que `epic-1-context.md` fixa.

**Encaminhamento.** Agrupado por causa: **patch** — A (BH6: o `blockGap:0`, removido das seis envolventes), B (BH3, EC1: a copy nomeada restringida às Secções), C (BH13: o docblock), D (BH20: o regime de foco do chip) e E (BH11: o `deferred` e o ledger). **defer** — F (BH4, EC3: o `h1` dos archives de autor e data), G (BH5, VG3: a verificação do `h1`), H (BH8: a emenda do par de UX), I (BH10: as restantes parcelas do UX-DR20) e J (BH17, VG1, VG2: a verificação durável do markup e do CSS). **reject** — 12 `low` cujo remédio era prosa da spec ou cujo defeito foi refutado, um deles o `false` (BH1, BH2, BH9, BH12, BH15, EC2, BH16, BH19, BH21, BH22, VG4, IA1) e 3 `false` (BH7, BH14, BH18). Sem `intent_gap` nem `bad_spec`: sem loopback.

## Design Notes

**Porquê a 1.10 toma o piso do UX-DR20.** Três histórias remeteram-no para aqui: a 1.8 ("quem fechar as superfícies (1.9 ou 1.10) deve tomá-lo — incluindo o `h1` por página") e a 1.9, cujo contrato escreve "implementar o skip link, o `main` com `id` ou o `h1` por página (vão para a 1.10)". Esta é a última história cujas superfícies são públicas (a 1.11 é copy, a 1.12 é o mu-plugin): sem isto, o achado **F-17 (HIGH)** de `review-accessibility.md:147-151` fecha o épico sem dono.

**Porquê o landmark é um grupo envolvente e não o primeiro grupo existente.** Marcar `main` só no primeiro grupo do template deixaria a maior parte do conteúdo fora do landmark, que é o defeito que o F-17 aponta (`1.3.1`). O grupo envolvente é `align:full` porque `.wp-site-blocks > .alignfull` (`theme.json:5`, `useRootPaddingAwareAlignments`) é a regra que faz sangrar as secções de topo: dentro de um grupo sem alinhamento, os `alignfull` descendentes deixam de casar com ela e o hero navy ganharia duas faixas brancas de 20px. O `blockGap:0` é o que impede o `is-layout-flow` do novo grupo de meter 24px (`theme.json:27`) entre secções que já têm o seu próprio padding.

**Porquê o `h1` sobe cabeçalhos existentes.** Só duas páginas não têm `h1`: `/acervo/` (hero com `h2`) e as páginas servidas por `page.html` (o `post-title` escondido por `.ipcn-hero-alone`, que a auditoria de 2026-09-17 já mandava reavaliar). Inventar um `h1` novo duplicaria a mensagem; subir o que já lá está dá uma orientação correcta sem copy nova.

**Porquê o vazio das Secções liga ao hub e o de Notícias não.** A Secção vazia (`/category/<seccao>/`) não tem nenhuma lista de Seções; a página Notícias tem o pattern `ipcn/seccoes` desenhado acima da grelha. Ligar ao hub na primeira e ligar à própria página na segunda seriam o mesmo gesto — por isso a segunda só explica.

**O que fica de fora, por decisão.** O `index.html` (archives por tag/data/autor) não recebe estado vazio: o UX-DR13 nomeia cinco vazios — Home, Notícias, Acervo, Tema, Agenda — e nenhum é um archive de tag. As declarações mortas do CTA (`style.css:524-530`) e a regra genérica `.has-background` dentro de grelhas (`style.css:129`) continuam sem dono: o remédio da segunda exige auditar conteúdo guardado na base de dados, que este repositório não vê.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 problemas aceites (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1, nenhuma entrada `sintaxe:`.
- `bash scripts/check-php.test.sh` — esperado: 14/14.
- `git diff --stat` — esperado: só `templates/` (nove ficheiros), `parts/header.html`, `style.css`, `inc/listings.php` e esta spec.
- `grep -c 'tagName":"main"' wp-content/themes/ipcn-fse/templates/*.html` — esperado: 1 em cada um dos nove templates.
- `grep -L 'anchor":"conteudo"' wp-content/themes/ipcn-fse/templates/*.html` — esperado: vazio (todas as templates têm o alvo do skip link).
- Harness de sessão na scratchpad: afirmar o skip link antes da marca no `header.html`, a ausência de `ipcn-hero-alone`, a copy dos vazios, o `h1` por template (`grep` de `<h2` onde era título de página) e o guard do link do hub quando `get_page_by_path()` devolve nulo.

**Manual checks (if no CLI):**
- Sem WordPress local e com o deploy na mão do dono, a passagem em `stagingredesign` após deploy e purga (`?nocache=1`): em `/acervo/` sem itens, `/category/<seccao>/` sem publicações e um endereço inventado; e que as secções full-bleed da Home continuam a sangrar até à margem (sem faixas brancas de 20px) e sem espaçamento novo entre elas.

## Auto Run Result

**O que mudou.** As superfícies públicas do Épico 1 fecham com vazios coerentes e com o piso de acessibilidade do UX-DR20. (1) **Vazios:** o Acervo passa a dizer «Em breve, novos itens do acervo.»; a página Notícias explica a ausência (as Seções da mesma página são a oferta, o pattern `ipcn/seccoes` já desenhado acima); a Secção sem publicações passa a explicar a ausência e a ligar ao hub das Seções, resolvido por slug (`get_page_by_path('noticias')` → `get_permalink()`, com guard do `WP_Post`); a Home e a 404 já cumpriam. (2) **Skip link:** `parts/header.html` ganha, como primeiro bloco antes da marca, `<a href="#conteudo">Pular para o conteúdo</a>`, vestido em `style.css` fora do fluxo (fora do ecrã *pelo topo*, para não abrir scroll horizontal a 320px) e revelado em `:focus-visible`. (3) **Landmark `main`:** as três templates de grupo único viram `main#conteudo`; as outras seis ganham um grupo envolvente `align:full` com `layout:default` (o `alignfull` mantém o sangramento e o `blockGap` herdado mantém a cadência). (4) **Um `h1` por página:** o hero do Acervo sobe a `h1` e o `wp:post-title` de `page.html` passa a `level:1` e larga a classe `ipcn-hero-alone` (a regra que o escondia sai do `style.css`).

**Ficheiros alterados** (`git diff --stat` desde a base `17300f26e3784a76cd84d03b44b05148e405310a`):
- `wp-content/themes/ipcn-fse/parts/header.html` — o skip link, primeiro bloco do cabeçalho.
- `wp-content/themes/ipcn-fse/style.css` — as regras `.ipcn-skip`; a regra e o comentário de `.ipcn-hero-alone` removidos por ficarem sem consumidor.
- `wp-content/themes/ipcn-fse/templates/404.html`, `index.html`, `page.html` — o grupo único existente vira `main#conteudo`; `page.html` leva ainda o `h1` visível.
- `wp-content/themes/ipcn-fse/templates/front-page.html`, `page-noticias.html`, `archive.html`, `archive-acervo_ipcn.html`, `single.html`, `single-acervo_ipcn.html` — grupo envolvente `main#conteudo`; nos dois primeiros, o vazio próprio; no terceiro, a guarda de categoria e o docblock; no quarto, o `h1` do hero e a copy do vazio.
- `_bmad-output/implementation-artifacts/spec-1-10-os-estados-vazios-e-a-pagina-404.md` — esta spec.
- `_bmad-output/implementation-artifacts/deferred-work.md` — as entradas novas da 1.10 e o fecho das duas que apontavam para esta história.

**Triagem da revisão.** 30 achados de quatro camadas — `high` 0, `medium` 1, `low` 26, `false` 3 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **6 achados corrigidos por patch em 5 correcções:** o `blockGap:0` retirado das seis envolventes (**a correcção `medium`**: a raiz já punha 24px entre as secções e o wrapper herda o mesmo `--wp--style--block-gap`, logo o override só podia retirar cadência — BH6); a copy nomeada do vazio restringida às Secções, com a frase de sempre verbatim nos archives de tag/autor/data (BH3, EC1); o docblock de `[ipcn_archive_vazio]` actualizado (BH13); o chip do skip link alinhado ao único regime de foco do tema, `:focus-visible` (BH20); e o preenchimento do `deferred`/ledger (BH11). **9 achados diferidos em 5 entradas:** o `h1` dos archives de autor e data (BH4, EC3), a verificação durável de markup e CSS (BH5, VG1, VG2, VG3, BH17), a emenda do par de UX (BH8), as restantes parcelas do UX-DR20 (BH10) e a passagem no browser pendente de deploy. **15 rejeitados:** 3 `false` — o `#conteudo` sem `tabindex` (BH7: a navegação por fragmento muda o foco sequencial e nada fixo cobre o topo), o regime de acento (BH14: é a entrega registada à 1.11) e o vazio da Agenda (BH18: é das histórias 2.2/2.3) — e 12 `low` cujo remédio era prosa da spec (BH1, BH2, BH16, BH19) ou cujo estado não é demonstrável ou o remédio não é uma correcção directa (BH9, BH12, BH15, EC2, BH21, BH22, VG4, IA1).

**Recomendação de passagem seguinte:** `followup_review_recommended: false` — nesta primeira passagem só uma entrada corrigida por patch foi `medium` (BH6); nenhuma `high` e menos de duas `medium`. Contagem por veredicto: `medium` 1, `low` 4.

**Verificação.** `bash scripts/check-php.sh` → 17 problemas, todos `block markup` (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1 e 0 entradas `sintaxe:` — idêntico ao baseline; `bash scripts/check-php.test.sh` → 14/14; `grep -c 'tagName":"main"'` → 1 em cada um dos nove templates e `grep -L 'anchor":"conteudo"'` → vazio; harness de sessão em 39/39 (fora do deliverable, como os das 1.5-1.9), com uma asserção por linha da matriz de I/O; e `php verify-vazios.php`, que carrega `inc/listings.php` com stubs e exercita o shortcode nos cinco casos — Secção com hub (frase + ligação), hub ausente e permalink vazio (frase, sem ligação morta), Tema (frase de sempre + Acervo inteiro) e archive de tag (frase de sempre, verbatim) — todos verdes. Sem WordPress local, a passagem no browser fica pendente de deploy e purga.

**Riscos residuais.** (1) A passagem no browser continua por fazer: o repositório não mede o resultado renderizado e o harness afirma markup e regras, não geometria. Superfícies a percorrer em `stagingredesign` depois da purga (`?nocache=1`): a Home (as cinco secções a sangrar até à margem **e** com os 24px de cadência entre elas, agora vindos do `blockGap` herdado), `/noticias/` sem publicações, uma `/category/<seccao>/` sem publicações, `/acervo/` sem itens, as páginas institucionais servidas por `page.html` (o `h1` que passa a estar visível — confirmar que não duplica um cabeçalho do próprio conteúdo), a 404 e o chip do skip link na primeira tabulação, a 320px e a 375px. (2) O `main#conteudo` depende de o WordPress injectar `id` e o layout de fluxo no render — assunção declarada pelo implementador e não observada por comando nenhum. (3) O `h1` dos archives de autor e data fica como estava (diferido). (4) A cláusula `Always` do contrato que pedia `blockGap:0` está superada pela correcção BH6: fica registada na entrada do `## Spec Change Log`, porque o `<intent-contract>` é imutável nesta passagem.
