---
title: 'Story 1.9 — Leitura no telemóvel até 320px'
type: 'feature'
created: '2026-09-29'
status: 'done'
baseline_commit: '3fb76d62ffde26879e2c88e0a845f5bd351eb0b2'
review_loop_iteration: 0
followup_review_recommended: false
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
warnings:
  - multiple-goals
  - oversized
deferred:
  - summary: >-
      Os controlos do interior do painel de cookies (caixas de seleção de ~19px) ficam abaixo dos 44px; o painel é superfície da história 3.6.
    evidence: >-
      Achado da camada de casos-limite (EC3). `inc/cookie-bar.php:40-41` emite `<label><input type="checkbox" ...></label>` sem estilo que lhe dê alvo; a história veste só `.ipcn-cookie-btn` (botões da barra e do painel) porque o AC nomeia "acções de cookies" (`DESIGN.md:323`) e o interior do painel pertence à 3.6 ("cookies com escolha real"), que o reconstrói.
    location: >-
      wp-content/themes/ipcn-fse/inc/cookie-bar.php:40-41
    severity: low
  - summary: >-
      As caixas largas que a história tornou regiões com scroll (`figure.wp-block-table`, `figure.wp-block-embed`, `.wp-block-code`) não são alcançáveis por teclado nem comportam o anel de foco que a 1.4 definiu para os seus descendentes.
    evidence: >-
      Achados da camada de casos-limite (EC4) e da revisão cega (BH9). `overflow-x:auto` cria um contentor de scroll não focável (WCAG 2.1.1) e recorta o `outline` de `2px` + `2px` de afastamento de um link focado lá dentro (`style.css:913-917`). O remédio (um wrapper com `tabindex="0"`/`role="region"`, ou `scroll-margin`) é decisão de componente fora do `style.css` e depende de existir conteúdo com tabela/embed/código largo, que este repositório não vê.
    location: >-
      wp-content/themes/ipcn-fse/style.css (bloco REFLOW 320px, §1)
    severity: low
  - summary: >-
      As regras novas do `style.css` não são observadas por verificação durável: uma mutação que apague o `display:inline-flex` ou mude os `44px` deixa o repo inteiro verde.
    evidence: >-
      Achado da camada de lacunas de verificação (VG1), pré-verificado por mutação (apagar `display: inline-flex;` do grupo da paginação deixa o harness a imprimir `OK`), mais BH13, VGo2, IA1 e IA8. É a mesma infraestrutura já diferida pela 1.3, 1.5, 1.6, 1.7 e 1.8: o `check-php.sh` só lê `.php` sob `inc/` e o NFR9 declara "sem build step, testes ou CI". O harness da sessão foi estendido (afirma agora `display:inline-flex`, `min-width` e o grupo inteiro da paginação) mas não fica no repositório.
    location: >-
      scripts/check-php.sh, wp-content/themes/ipcn-fse/style.css
    severity: low
  - summary: >-
      As regras de alvo do cabeçalho (nav, CTA, botões do menu) não são exercitáveis hoje: o bloco de navegação não emite markup no frontend.
    evidence: >-
      Desvio 6 do auditor de alinhamento (IA6), que cruza com o diferido da 1.8 (`deferred-work.md`): os itens vivem no navigation post `5358` da base de dados e nem a home nem uma 404 gerada no momento trazem `<nav class="wp-block-navigation">`. Enquanto o dono não corrigir os itens na BD, os selectores `header .wp-block-navigation*` não têm a quem se aplicar; as regras ficam prontas para quando o bloco renderizar.
    location: >-
      wp-content/themes/ipcn-fse/parts/header.html:13
    severity: low
  - summary: >-
      O componente `accordion` do `DESIGN.md` continua por implementar, embora o AC do épico nomeie "o cabeçalho do acordeão" entre os controlos de 44px.
    evidence: >-
      Achado da revisão cega (BH8/IA3) e verificação da fase de planeamento: nenhum `wp:details`/`summary`/`.wp-block-details` em `templates/`, `patterns/` ou `inc/`. Não há controlo a dimensionar, e construir o componente seria âmbito que nenhum AC desta história pede; a menção do AC fica registada como sem alvo.
    location: >-
      wp-content/themes/ipcn-fse/ (ausência), docs do `DESIGN.md`
    severity: low
---

<intent-contract>

## Intent

**Problem:** Nas superfícies do Épico 1 nada garante que a 320px a página não arraste para o lado nem que cada controlo chegue aos 44px de alvo: o `style.css` não tem `overflow-wrap` nem contenção para blocos largos, e a paginação, o cabeçalho e as acções de cookies ficam abaixo dos 44px. As duas grelhas de cartões colapsam em pontos diferentes (900/600px no shortcode, 768px nos blocos).

**Approach:** Fechar no `style.css` — único ficheiro onde a leitura móvel se resolve — três frentes: contenção de transbordo (palavras e caixas largas limitadas à coluna), alvo de toque de 44px nos controlos que ainda falham, e o colapso das grelhas de cartões unificado com o caminho dos blocos. Confirmar no markup que nenhuma superfície banida existe.

## Boundaries & Constraints

**Always:** o diff é só `wp-content/themes/ipcn-fse/style.css`; usar os tokens/classes do tema (`--wp--preset--color--*`, `.ipcn-*`, `.page-numbers`/`.ipcn-page`); o anel de foco da 1.4 (`style.css:909-913`) é o único regime de foco; a medida de leitura é a do `DESIGN.md` (`content` 720px, margem 20px); o único ponto de colapso das grelhas de cartões passa a ser 768px, o do caminho dos blocos; o `check-php.sh` mantém os 17 problemas aceites.

**Never:** tocar em `templates/`, `patterns/`, `inc/`, `theme.json`, `functions.php` ou no mu-plugin; introduzir JavaScript, um componente novo ou uma grelha nova; construir o acordeão (não existe `wp:details`/`summary` no tema e nenhum AC o pede); pôr o cabeçalho pegajoso; implementar o skip link, o `main` com `id` ou o `h1` por página (vão para a 1.10); tocar na referência de navegação da base de dados; redimensionar ligações inline dentro do texto ou do cartão; mexer no interior do painel de cookies (é da 3.6); qualquer superfície banida (carrossel, pop-up, auto-play).

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Palavra/URL longa sem espaços | `core/post-content` a 320px | a cadeia quebra dentro da coluna; a página não arrasta | — |
| Bloco largo no texto | `core/table`, `core/embed`, `core/code` a 320px | caixa limitada à coluna; scroll interno em vez de scroll da página | — |
| Paginação com muitas páginas | >5 páginas a 320px | a fila quebra em linhas; cada número com ≥44px | — |
| Cabeçalho a 320px | nav, pílulas CTA e hamburger | cada controlo com ≥44px de alvo | — |
| Barra de cookies visível | barra a 320px, sem consentimento | botões com ≥44px; a barra quebra sem transbordar | — |
| Nome de Tema longo | hero do archive / pílula do filtro a 320px | quebra dentro da caixa, sem empurrar o layout | — |
| Grelha de cartões a meio | 601–768px | as duas grelhas mostram uma coluna | — |

</intent-contract>

## Code Map

- `wp-content/themes/ipcn-fse/style.css` — **onde tudo entra.** l.17-21 salvaguarda global de `img` (única defesa de largura hoje); l.123-151 grelha de blocos (`.wp-block-post-template.is-layout-grid`, colapso a 768px); l.337-355 paginação de blocos; l.358-368 `.ipcn-grid` do shortcode (colapso a 900 e 600px — o que diverge); l.47-121 cabeçalho (nav l.50-52, CTA l.58-97, hamburger l.101-105); l.565-581 `.ipcn-page` da paginação do shortcode; l.583-664 pílulas das Secções (já 44px, l.601) e do filtro de Temas (já 44px, l.641); l.745-843 barra e painel de cookies (botões l.774-783, tabela l.822-832); l.909-913 anel de foco da 1.4 (não tocar); l.937-943 `.ipcn-prose`.
- `wp-content/themes/ipcn-fse/templates/{single,page,single-acervo_ipcn}.html` — a leitura longa: `.ipcn-prose` + `core/post-content` com `contentSize:720px`; o `page.html` **não** tem `.ipcn-prose` (o `core/post-content` é a classe comum). **Fora do diff:** leitura só.
- `wp-content/themes/ipcn-fse/templates/{archive,archive-acervo_ipcn,page-noticias,index}.html` e `patterns/ipcn-card.php` — listagens com `columnCount:3` e paginação de blocos (`core/query-pagination` com previous/next/numbers). **Fora do diff.**
- `wp-content/themes/ipcn-fse/inc/listings.php` — l.103 emite `.ipcn-grid`; l.129-137 a paginação manual `.ipcn-pagination`/`.ipcn-page`; l.178-186 o hero do archive (H1 = nome do termo por `esc_html`). **Fora do diff.**
- `wp-content/themes/ipcn-fse/inc/cookie-bar.php` — l.29-48 o markup da barra e do painel; botões `.ipcn-cookie-btn` na barra e no painel. **Fora do diff** (só as regras que os vestem).
- `wp-content/themes/ipcn-fse/theme.json` — `contentSize:720px`, `wideSize:1100px`, padding de raiz 20px; tokens de cor (não tocar).
- `scripts/check-php.sh` — a única verificação executável; não lê `.css` nem `.html`; conta 17 problemas aceites sob `inc/` (12 em `agenda-block.php`, 5 em `listings.php`).

## Tasks & Acceptance

**Execution:**
- `wp-content/themes/ipcn-fse/style.css` — bloco novo de reflow: `overflow-wrap: break-word` em `.ipcn-prose`, `.wp-block-post-content`, `.wp-block-heading` e `.wp-block-post-title`, e `max-width:100%` + `overflow-x:auto` nas caixas largas do `core/post-content` (`figure.wp-block-table`, `figure.wp-block-embed`, `.wp-block-code`) — sem isto uma palavra, uma tabela ou um `code` longo arrasta a página.
- `wp-content/themes/ipcn-fse/style.css` — na paginação, `flex-wrap: wrap` em `.wp-block-query-pagination` e `.ipcn-pagination` e `display:inline-flex;align-items:center;justify-content:center;min-height:44px;min-width:44px` em `.wp-block-query-pagination a`/`.page-numbers` e em `.ipcn-page` — a fila quebra em vez de estourar e os números deixam de ficar abaixo dos 44px.
- `wp-content/themes/ipcn-fse/style.css` — nos controlos do cabeçalho (`.wp-block-navigation-item__content`, `.ipcn-header-cta .wp-block-button__link`, `.wp-block-navigation__responsive-container-open`), `min-height:44px` e centragem (`inline-flex`) — hoje medem ≈32px e ≈42px.
- `wp-content/themes/ipcn-fse/style.css` — `.ipcn-cookie-btn`, `min-height:44px` e centragem — as acções de cookies medem ≈35px na barra e no painel.
- `wp-content/themes/ipcn-fse/style.css` — `.ipcn-grid`, `@media (max-width:768px) { grid-template-columns:1fr }` e remover o degrau de 900px — passa a colapsar no mesmo ponto que `.wp-block-post-template.is-layout-grid`.
- `wp-content/themes/ipcn-fse/style.css` — `max-width:100%` e `overflow-wrap:break-word` nas pílulas das Secções e do filtro de Temas — um rótulo comprido quebra em vez de empurrar a fila.

**Acceptance Criteria:**
- Given uma superfície do Épico 1 a 320px, when há uma palavra/URL longa, uma tabela, um `embed` ou um `code` no texto, then nada arrasta a página para o lado.
- Given a paginação com mais de cinco páginas a 320px, when a fila é desenhada, then quebra em linhas e cada número/alvo tem alvo de toque ≥44px.
- Given o cabeçalho a 320px, when se mede o nav, as pílulas `Associe-se`/`Apoia-se` e o hamburger, then cada um alcança ≥44px; e a barra de cookies faz o mesmo nos seus botões.
- Given uma listagem a 601–768px, when as duas grelhas de cartões são comparadas, then mostram o mesmo número de colunas (uma).
- Given a leitura longa, when é aberta a 375px, then mantém a medida do `DESIGN.md` (720px de coluna) e não obriga a scroll horizontal.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa e os 17 problemas aceites não crescem.

## Spec Change Log

Sem alterações ao contrato. As decisões abaixo são do implementador e ficam registadas para poderem ser contestadas.

- **O acordeão do AC não tem alvo.** O AC nomeia "o cabeçalho do acordeão", mas não existe acordeão no tema — nenhum `wp:details`/`summary`/`.wp-block-details` em `templates/`, `patterns/` ou `inc/`; o componente `accordion` do `DESIGN.md` (metadado do Item) está por implementar. Não há controlo a dimensionar e construir o componente seria âmbito inventado (nenhum AC o pede); fica registado.
- **Alvos inline (cartão, prosa) ficam como estão.** O AC enumera paginação, filtros e acordeão, e o `DESIGN.md` acrescenta as acções de cookies; a etiqueta de termo do cartão (`.ipcn-card .wp-block-post-terms a`, ≈15px) e as ligações na prosa são alvos inline, que a 2.5.8 do WCAG 2.2 isenta. O cartão já tem alvo total pelo `::after` do título (`style.css:471-476`). Esticá-las mudaria a cadência do cartão sem o AC o pedir.
- **Cabeçalho pegajoso: decidido não fazer.** O diferido da 1.8 mandava a decisão para esta história. Fica de fora: não está em nenhum AC, consome altura vertical a 320px e o `EXPERIENCE.md:88` ("mantêm-se visíveis ao rolar") é requisito próprio, não desta leitura. Registado para não se perder.
- **Skip link, `main` com `id` e `h1` por página: reatribuídos à 1.10.** São UX-DR20, não AC desta história (que é leitura a 320px); a 1.10 fecha as superfícies. Fica no `deferred-work.md`.
- **Interior do painel de cookies: da 3.6.** As caixas de seleção do painel (~19px) ficam abaixo dos 44px, mas o painel é superfície da história 3.6 ("cookies com escolha real"), que o reconstrói; o `style.css` desta história só veste os botões (`ipcn-cookie-btn`), que são o controlo visível na barra.
- **Tipografia do cartão: mantida.** O diferido da 1.3 notava que o cartão não tem ajuste abaixo dos 768px; o `DESIGN.md` fixa `card-title` a 19px e `card-feature` em `clamp(26px,3vw,34px)`, que já quebram por espaços — mudá-la contradiria o `DESIGN.md`, pelo que a história só garante a quebra (`overflow-wrap`).
- **Grelhas unificadas a 768px.** O caminho dos blocos é o dominante (todos os templates) e o `DESIGN.md` descreve duas colunas possíveis ("três no computador, uma no telemóvel"); alinhar `.ipcn-grid` a ele é a mudança menor.
- **`multiple-goals`.** A história junta duas metas entregáveis em separado — não transbordar a 320px e elevar todos os controlos a 44px — e ambas cabem no `style.css`.
- **Posto em prática — contentor dos números da paginação.** Além do `flex-wrap` nos dois invólucros, o contentor `.wp-block-query-pagination-numbers` passa a ser uma fila flexível explícita (`display:flex; flex-wrap:wrap; justify-content:center; gap:8px`). O núcleo só garante a quebra porque `paginate_links` (`type: plain`) separa os números por `\n` e o contentor é `display:block`; tornar a fila explícita evita depender desse espaço (que uma minificação de HTML pode colapsar).

## Review Triage Log

### 2026-09-29 — Review pass

- verdicts: 34 findings — high 0, medium 1, low 32, false 1, maybe-false 0
- findings:
  - `[low]` `[reject]` **BH1 / IA4** o AC das superfícies banidas não tem tarefa, linha na matriz, critério nem verificação — refutado: carrossel/pop-up/auto-play são ausentes no tema (0 `<iframe>`/`<video>`/`<audio>`, 0 `autoplay`, 0 `wp_enqueue_script`, nenhuma biblioteca de slider), recolha da fase de planeamento; o remédio seria texto da spec, que a triagem rejeita.
  - `[low]` `[reject]` **BH2** o AC de origem não é citado com `ficheiro:linha` — o remédio é prosa da spec; o corpo cita `DESIGN.md`, `EXPERIENCE.md:88`, `epics.md` indirectamente e `style.css` por linha onde importa.
  - `[low]` `[reject]` **BH3 / IA2** "todo o controlo" é lido como a lista enumerada (paginação, filtros, cabeçalho do acordeão) mais as acções de cookies do `DESIGN.md:323`, e a isenção dos alvos inline está registada no Change Log — a leitura é seleccionável pela própria enumeração do AC; a 2.5.5 e a 2.5.8 isentam ambas alvos inline, pelo que a citação da norma não cria defeito; remédio seria prosa da spec.
  - `[low]` `[patch]` **BH4** o botão de fechar do menu mobile não estava dimensionado — acrescentado ao grupo de alvos do cabeçalho (`.wp-block-navigation__responsive-container-close`); o toggle de submenu fica de fora porque o nav da BD não tem submenus.
  - `[medium]` `[patch]` **BH5** `display:inline-flex` no botão de abrir o menu venceria o `display:none` do núcleo em `@media (min-width:600px)` (a regra do tema é (0,2,1) contra (0,2,0) do núcleo) e faria o hamburger reaparecer em desktop — verificado no `style.css` do núcleo; o grupo foi dividido e os botões de abrir/fechar levam só `min-height`/`min-width`.
  - `[low]` `[reject]` **BH6** as regras de alvo não são condicionais à largura — o alvo de 44px é, no `DESIGN.md`, independente da largura ("Dar 44px de alvo a todo o controlo"); o crescimento em desktop é o efeito pretendido e nenhum contrato de altura do cabeçalho existe; remédio seria prosa da spec.
  - `[low]` `[reject]` **BH7 / VGo1 / IA7** a banda 769–900px passa a 3 colunas — é a unificação pretendida com o caminho dos blocos (que já colapsava a 768px) e o `DESIGN.md` manda três colunas no computador; nenhum defeito, e o remédio seria texto da spec.
  - `[low]` `[patch]` **BH8** a contenção não cobria parágrafos — o eyebrow e a descrição do hero do archive (`inc/listings.php:178,186`), os parágrafos do hero da Home e os do rodapé são `<p>` fora da prosa e dos títulos; `p` acrescentado à lista de `overflow-wrap`.
  - `[low]` `[defer]` **BH9 / EC4** as novas regiões com scroll não são alcançáveis por teclado e recortam o anel de foco; o remédio é decisão de componente (fora do `style.css`) e depende de conteúdo largo que o repositório não vê — diferido.
  - `[low]` `[reject]` **BH10** a barra de cookies fixa cresce ~9px — a superfície é da 3.6, o defeito é marginal e o remédio não é uma correcção directa (`scroll-padding` condicionado à visibilidade da barra).
  - `[low]` `[patch]` **BH11** o `deferred: []` contradizia o corpo e o handoff não chegava ao ledger — a lista `deferred` do frontmatter foi preenchida (5 itens) e o `deferred-work.md` recebeu as entradas desta história.
  - `[low]` `[reject]` **BH12** `oversized` declarado sem reconciliação — o aviso é mecânico (o template manda acrescentá-lo acima de 1600 tokens e continuar); o remédio seria prosa da spec.
  - `[low]` `[defer]` **BH13 / VGo2 / IA1 / IA8** a verificação regista expectativas e o harness era cego a mutações — o harness da sessão foi estendido (afirma `display:inline-flex`, `min-width` e o grupo inteiro da paginação, além do guard do botão do menu), mas a verificação durável de CSS é a infraestrutura já diferida (NFR9) — diferido.
  - `[low]` `[reject]` **BH14** o AC da leitura a 375px é literalmente insatisfazível (720 > 375) — o código entrega o que o AC quer (medida limitada a 720px, sem scroll horizontal a 375px) e o remédio seria reescrever a spec.
  - `[low]` `[reject]` **BH15** alturas estimadas sem método, duplicação com o núcleo não distinguida e linhas do mapa de código desactualizadas pelo próprio diff — verdadeiro mas sem defeito de código; o remédio seria prosa da spec.
  - `[low]` `[patch]` **EC1 / EC7** `overflow-wrap:break-word` não reduz o min-content, logo um rótulo sem espaços ainda empurra a fila — `min-width: 0` acrescentado às pílulas das Secções e do filtro de Temas.
  - `[low]` `[patch]` **EC2 / VG2** os parágrafos do hero do archive fora da contenção — mesma correcção de BH8 (`p` na lista).
  - `[low]` `[defer]` **EC3** as caixas de seleção do painel de cookies ficam abaixo dos 44px — superfície da 3.6, que reconstrói o painel; diferido com `location` e severidade.
  - `[low]` `[patch]` **EC5** o botão de fechar do menu não casava com regra nenhuma — mesma correcção de BH4.
  - `[low]` `[patch]` **EC6** o hamburger media 42px de largura (ícone 24px + padding e borda do tema) — `min-width: 44px` acrescentado no grupo dos botões do menu.
  - `[low]` `[defer]` **VG1** os alvos de 44px da paginação presos só a uma comparação de texto que sobrevive a mutações — pré-verificado pela camada (apagar `display:inline-flex` deixava o harness verde); o harness da sessão foi estendido para afirmar a propriedade e todos os selectores do grupo, e a forma durável é a infraestrutura diferida — diferido.
  - `[false]` `[reject]` **IA5** "o diff não define o alvo dos filtros" — refutado: os filtros cumprem os 44px por regras pré-existentes (`style.css:641`, `:601`) que a história não precisou de tocar; o requisito do AC está satisfeito e nada de mau acontece na superfície citada.
  - `[low]` `[defer]` **IA3** o `accordion` do `DESIGN.md` continua por implementar enquanto o AC nomeia o seu cabeçalho — não há controlo a dimensionar e construir o componente seria âmbito inventado; diferido.
  - `[low]` `[defer]` **IA6** as regras do cabeçalho pendem de markup que hoje não renderiza (o bloco de navegação não emite `<nav>`) — pré-existente, já no ledger desde a 1.8; diferido.

**Encaminhamento.** Agrupados por causa comum e rota: **patch** — A (BH4, BH5, EC5, EC6: o grupo de alvos do cabeçalho, dividido para não vencer o `display:none` do núcleo, com o botão de fechar e `min-width` no hamburger), B (BH8, EC2, VG2: `p` na contenção), C (EC1, EC7: `min-width:0` nas pílulas) e D (BH11: `deferred` no frontmatter e as entradas no ledger). **defer** — E (BH9, EC4: o teclado e o anel de foco nas novas regiões com scroll), F (EC3: o interior do painel de cookies, da 3.6), G (VG1, BH13, VGo2, IA1, IA8: a verificação durável de CSS), H (IA3: o acordeão sem alvo) e I (IA6: o nav que não renderiza). **reject** — 19 achados: o único `false` (IA5) e 18 `low` cujo remédio era prosa da spec ou cujo defeito foi refutado (BH1, BH2, BH3, BH6, BH7, BH10, BH12, BH14, BH15, IA2, IA4, IA7, VGo1). Sem `intent_gap` nem `bad_spec`: sem loopback.

## Design Notes

**Porquê só o `style.css`.** As três frentes da história — transbordo, alvo de toque e colapso de grelha — são todas de apresentação; o markup (`templates/`, `patterns/`) e o `inc/` já entregam as classes de que o CSS precisa (`core/post-content`, `.ipcn-grid`, `.ipcn-cookie-btn`, `.page-numbers`). Manter o diff num ficheiro é o que a 1.8 já fez e o que o `check-php.sh` continua a não observar — daí o harness de sessão na Verificação.

**Porquê 768px e não 900/600.** O `style.css:147-151` já colapsa `.wp-block-post-template.is-layout-grid` a 768px por força da regra `!important` do tema; o `.ipcn-grid` do shortcode é o único com dois degraus. Tirar o degrau de 900px faz as duas grelhas concordarem em toda a gama 320–1100px sem inventar um ponto novo.

**Porquê limitar as caixas largas em vez de as esconder.** `overflow-x:auto` num contentor de bloco (o `figure` de `core/table`/`core/embed`, o `pre` de `core/code`) dá scroll interno ao componente sem arrastar a página — o padrão corrente para conteúdo largo que não deve ser cortado; uma tabela de cookies que precise de largura continua legível.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 problemas aceites (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1, nenhuma entrada `sintaxe:`.
- `bash scripts/check-php.test.sh` — esperado: 14/14.
- `git diff --stat` — esperado: um ficheiro de tema (`style.css`) e esta spec.
- `awk '{o+=gsub(/\{/,"{"); c+=gsub(/\}/,"}")} END{print o, c}' wp-content/themes/ipcn-fse/style.css` — esperado: chaves equilibradas.
- Harness de sessão na scratchpad: afirmar que cada regra nova existe no `style.css` (contenção, 44px, colapso a 768px), que o degrau de 900px desapareceu, e que o anel de foco da 1.4 e a medida 720px estão intactos.

**Manual checks (if no CLI):**
- Sem WordPress local e com o deploy na mão do dono, a passagem final confirma no browser em `stagingredesign` após deploy e purga (`?nocache=1`): a 320px, nenhuma superfície arrasta para o lado (Home, `/noticias/`, uma Secção, `/acervo/`, um Tema, uma Notícia, um Item, uma institucional, a 404); a paginação, os filtros, o cabeçalho e a barra de cookies alcançam 44px; a leitura mantém a coluna a 375px.

## Auto Run Result

**O que mudou.** A leitura a 320px passou a ter rede em três frentes, tudo em `wp-content/themes/ipcn-fse/style.css`: (1) contenção de transbordo — `overflow-wrap: break-word` na prosa, no conteúdo do post, nos títulos e nos parágrafos, e `max-width:100%` + `overflow-x:auto` nas caixas largas (`figure.wp-block-table`, `figure.wp-block-embed`, `.wp-block-code`), para uma palavra, uma tabela ou um `code` largo não arrastarem a página; (2) alvos de 44px — paginação dos blocos e do shortcode (com `display:inline-flex` e a fila dos números a quebrar em `display:flex`), pílulas CTA e itens do nav do cabeçalho, botões de abrir/fechar o menu mobile e botões da barra/painel de cookies; (3) as duas grelhas de cartões passam a colapsar no mesmo ponto (768px), removendo o degrau de 900px do `.ipcn-grid`. As pílulas das Secções e do filtro de Temas ganharam `min-width:0` para um rótulo sem espaços não empurrar a fila.

**Ficheiros alterados** (`git diff --stat` desde a base `3fb76d6` imprime três: um do tema, esta spec e o ledger):
- `wp-content/themes/ipcn-fse/style.css` — o bloco `REFLOW 320px (Story 1.9)`, o colapso do `.ipcn-grid` e a contenção das pílulas.
- `_bmad-output/implementation-artifacts/spec-1-9-leitura-no-telemovel-ate-320px.md` — esta spec.
- `_bmad-output/implementation-artifacts/deferred-work.md` — as entradas novas da 1.9 (patch da revisão, BH11).

**Triagem da revisão.** 34 achados de quatro camadas — `high` 0, `medium` 1, `low` 32, `false` 1 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **10 achados corrigidos por patch em 4 correcções:** o grupo de alvos do cabeçalho, dividido para não vencer o `display:none` do núcleo em desktop e com o botão de fechar e `min-width` no hamburger (BH4, BH5, EC5, EC6 — **a correcção `medium`**), o `p` na contenção (BH8, EC2, VG2), o `min-width:0` nas pílulas (EC1, EC7) e o preenchimento do `deferred`/ledger (BH11). **5 entradas diferidas, 11 achados:** o teclado e o anel de foco nas novas regiões com scroll (BH9, EC4), o interior do painel de cookies da 3.6 (EC3), a verificação durável de CSS (VG1, BH13, VGo2, IA1, IA8), o acordeão sem alvo (IA3) e o nav que não renderiza (IA6). **19 rejeitados:** o único `false` (IA5, os filtros já cumprem 44px por regras pré-existentes) e 18 `low` cujo remédio era prosa da spec (BH1, BH2, BH3, BH6, BH7, BH10, BH12, BH13, BH14, BH15, IA2, IA4, IA7, VGo1) ou cujo defeito foi refutado (BH1/IA4: as superfícies banidas são ausentes; BH7/VGo1/IA7: 3 colunas em desktop é a unificação pretendida).

**Recomendação de passagem seguinte:** `followup_review_recommended: false` — nesta primeira passagem só uma entrada corrigida por patch foi `medium` (BH5); nenhuma `high` e menos de duas `medium`.

**Verificação.** `bash scripts/check-php.sh` → 17 problemas, todos `block markup` (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1 e 0 entradas `sintaxe:` — idêntico ao baseline; `bash scripts/check-php.test.sh` → 14/14; `style.css` com 190 `{` e 190 `}`; frontmatter da spec lido como YAML (`uv run --with pyyaml`) com um só campo `deferred` de cinco itens. Harness de sessão (fora do deliverable, como os das 1.5/1.6/1.7/1.8) em 45/45, agora cobrindo as sete linhas da matriz uma a uma e com um guard de mutação para o botão do menu (que não pode levar `display`); uma mutação que apague `display:inline-flex` do grupo da paginação passa a falhar o harness. O `style.css` do núcleo foi lido para confirmar o `@media (min-width:600px){ …-open:not(.always-shown){display:none} }` que motivou a divisão do grupo do cabeçalho. Sem WordPress local, a passagem no browser fica pendente de deploy e purga.

**Riscos residuais.** (1) A passagem no browser a 320px/375px continua por fazer: o AC pede geometria (sem scroll horizontal, alvos de 44px) e o repositório não a mede — o harness afirma as regras, não o resultado renderizado. (2) As regras de alvo do cabeçalho pendem de um bloco de navegação que hoje não emite markup (1.8). (3) As novas regiões com scroll têm a lacuna de teclado/foco registada no diferido. (4) O alvo de 44px nas pílulas, no cabeçalho e nos cookies vale a todas as larguras — é o pretendido pelo `DESIGN.md`, mas é uma mudança de ritmo em desktop que só o browser confirma.
