---
title: 'Story 1.4 — A identidade e o foco'
type: 'feature'
created: '2026-09-28'
status: 'done'
route: 'dispatch'
review_loop_iteration: 0
baseline_commit: 'b773cf9963af3ecde4c5d2c8472ddfe0ed5ccbe8'
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** O foco visível prometido pelo par de UX não existe no código: o `:focus-visible` de `style.css:755-763` é um anel simples terracota sobre uma lista fechada e deixa sem anel a navegação, a paginação, os restantes botões e os campos; nos campos (`style.css:282-287`) o `outline` é apagado. A identidade — treze tokens e a razão de contraste de cada par de texto — está no `DESIGN.md`, mas o código só lá chega por convenção.

**Approach:** Regra `:focus-visible` global com o **anel duplo** — `base` 2px dentro, `navy` 2px fora, 2px de afastamento — visível sobre base, subtle, ocre, navy, terracota e rodapé, e fora o `outline: none`; foco, não desenho.

## Decisions

- **Tokens:** o `theme.json` fica com **sete** slugs. Os treze tokens continuam declarados no `DESIGN.md` (frontmatter e secção *Colors*), com a razão por par, e os seis que não são slug (`ocre-hover`, `terracota`, `chumbo`, `text-muted`, `error`, `success`) usam-se por hex no `style.css`, como o `AGENTS.md` manda. Nada muda no `theme.json`.
- **Âmbito do contraste:** esta história cobre os tokens declarados e o foco. Os pares legados de formulário/cookies (`#991b1b`, `#166534`, `#94a3b8`, `#131736`, ocre sobre navy) ficam para as histórias donas (2.2, 3.x), registados em `deferred-work.md`.

## Boundaries & Constraints

**Always:** O anel é sempre `base` (interior) + `navy` (exterior), 2px cada, 2px de afastamento; nenhum focável fica sem ele. Sobre navy vale o `base`, sobre claro valem ambos. Cores por `--wp--preset--color--base`/`--navy`. Só `style.css` muda.

**Never:** Texto claro sobre ocre. Não alterar PHP, templates, patterns, mu-plugin (AD-6), `theme.json` nem os templates. Não tocar em `.ipcn-card-v2` (2.2) nem na barra de cookies (3.6). Não promover slug de cor sem decisão.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Foco sobre claro | Cartão, paginação, campo | Anel `navy` exterior a 2px | — |
| Foco sobre navy | Ligação do rodapé, botão de cookies | Anel `base` interior visível | — |
| Foco sobre ocre | Botão primário, acção de cookies | Os dois anéis; texto chumbo | — |
| Campo com foco | `input`/`textarea` | Anel duplo; `outline` não é `none` | — |

</frozen-after-approval>

## Code Map

- `style.css:282-287` — `.ipcn-form input/textarea:focus`: `outline: none` (l.286) + brilho ténue; único do tema.
- `style.css:755-763` — `:focus-visible` parcial (terracota, offset 2px) sobre lista fechada; substituir por regra global.
- `style.css:366-488` — `.ipcn-card`: elevação em `box-shadow` a vencer (l.375, 380). Sem anel hoje: `:332-347`, `:595-664` (navy), `:58-98`, `:524-550`, `:904-918`.
- `theme.json:7-15` — `palette`, sete slugs; **não muda**.
- Alvos: `parts/header.html:13,17,20` (navegação `ref:5358`, pílulas), `parts/footer.html`, `inc/forms.php:97-120`, `inc/cookie-bar.php:28-46`.
- Fontes: `DESIGN.md:219-224,237-255,317-336`; `AGENTS.md`; `deferred-work.md`.

## Tasks & Acceptance

**Execution:**
- [x] `wp-content/themes/ipcn-fse/style.css` — trocar o `:focus-visible` (l.755-763) por regra global (links, botões, `input`, `textarea`, `select`, `[tabindex]`) com o anel duplo e tirar o `outline: none` (l.282-287), mantendo a borda navy como reforço.
- [x] `wp-content/themes/ipcn-fse/style.css` — o anel vence os `box-shadow` de repouso/hover (cartão, CTAs, campos) sem apagar a elevação fora do foco.

**Acceptance Criteria:**
- Given o `style.css`, when procuro `outline: none`/`0`, then nenhum.
- Given um elemento focável (link, botão, campo, paginação, menu mobile), when recebe foco, then mostra o anel duplo 2px/2px.
- Given foco sobre navy, when o elemento recebe foco, then o anel `base` é visível.
- Given os fundos ocre do tema, when leio o texto que os assenta, then nenhum é claro.
- Given o `theme.json` e o `DESIGN.md`, when os leio, then o `theme.json` tem os sete slugs e o `DESIGN.md` declara os treze tokens com a razão de cada par de texto.

## Implementation Notes

- **Duas edições, só em `style.css`.** (1) `style.css:284-287` — o `.ipcn-form input/textarea:focus` ficou só com a borda navy: saiu o `outline: none` e saiu o `box-shadow: 0 0 0 4px rgba(13,23,107,.10)`, que passava a declaração morta (o anel global `!important` vence-o sempre — os campos de texto casam `:focus-visible` em todos os motores, logo o brilho nunca apareceria). (2) `style.css:755-765` — a lista parcial `.ipcn-card-v2/.ipcn-card/.ipcn-cta-*/#ipcn-cookie-bar button` deu lugar a uma regra `:focus-visible` global com o anel duplo: `outline: 2px solid var(--wp--preset--color--navy)` (`outline-offset: 2px`) por fora e `box-shadow: 0 0 0 2px var(--wp--preset--color--base)` por dentro. O `base` (2px) encosta à caixa do elemento e o `navy` (2px) fica 2px mais fora — base dentro, navy fora, como o `focus-ring` do `DESIGN.md`.
- **Especificidade.** A spec pede «especificidade que vença o repouso/hover». O seletor nu `:focus-visible` é (0,1,0) e perderia para os donos do `box-shadow` do tema (`.ipcn-form input:focus` é (0,2,1), `header .ipcn-header-cta .wp-block-button__link:hover` é (0,3,1), `#ipcn-cookie-bar .ipcn-cookie-btn` é (1,1,0)), todos sem `!important`. Optou-se por `!important` **só** nas duas declarações do anel, guardadas ao estado `:focus-visible`, para não pagar a multiplicação de seletores e garantir o vencedor em qualquer fundo. Fora do foco nada é tocado: a elevação de repouso/hover (`.ipcn-card`, CTAs, campos) permanece intacta.
- **Cobertura.** O seletor nu cobre links, botões, `input`, `textarea`, `select`, `[tabindex]` e o hamburger/menu mobile do `wp:navigation` — nenhum focável fica sem o anel. Sobre claro só o `navy` é perceptível (o `base` funde com o fundo); sobre navy (rodapé, barra de cookies em `rgba(19,23,54,.97)`, CTAs navy) vale o `base` por dentro.
- **Fora de âmbito, confirmado.** O `outline: none !important` de `wp-content/mu-plugins/ipcn-optimizations.php:107` está escopado a `.et_pb_contact_form_container …` (contacto do Divi): não casa markup do tema FSE e o mu-plugin é congelado (AD-6) — não se toca. Os pares legados de formulário/cookies (`#991b1b`, `#166534`, `#94a3b8`, `#131736`, ocre sobre navy do `.ipcn-card-noimg`) continuam diferidos para 2.2/3.x, como a spec decidiu e o `deferred-work.md` registou.
- **Sem WordPress local.** As linhas de render da matriz (anel sobre cada fundo, menu mobile, campo) só fecham no browser em `stagingredesign`, após deploy e purga (`?nocache=1`) — que este passo não faz. As restantes linhas são cobertas por inspecção do `style.css`.
- **Auditoria da matriz (I/O).** O projecto não tem runner nem CI (NFR9), logo nenhuma linha da matriz tem teste a cobri-la e a verificação sancionada (AD-13) é o `check-php.sh` mais o browser. Cobertura por inspecção: as quatro linhas pelo seletor nu `:focus-visible` (anel duplo) e pela remoção do `outline: none` do campo; as linhas de render (anel sobre cada fundo, menu mobile) só o browser em `stagingredesign` as fecha.

## Spec Change Log

Vazio: nenhum achado da revisão encaminhou para `bad_spec` nem para `intent_gap`, logo não houve loopback.

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo; **EC** = caça a casos-limite; **VG** = lacunas de verificação.
Veredictos: `high`/`medium`/`low` (defeito real), `false` (refutado). Agrupamento e encaminhamento no fim.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 1 | BH | O ledger diz `in-progress` e a spec `in-review`; o vocabulário do ledger é `review` | `low` | Real e transitório por desenho: o passo 3 sincroniza `in-progress`, o passo 5 sincroniza `review`. Sincronizado já, como na 1.3 (#16). |
| 2 | BH | O ficheiro `spec-1-4-…` não coincide com a chave do ledger | `low` | Real, mas dívida a montante já registada para a 1-1; o prefixo `spec-` é imposto pelo passo 1 do método. Repetida em `deferred-work`. |
| 3 | BH | O AC5 universal contradiz o recorte das *Decisions* | `low` | Real (redacção do AC). Remédio edita esta spec. Rejeitado. |
| 4 | BH | O universal do *Intent* refutado pelo próprio diferimento | `low` | Mesma raiz do #3. Remédio edita esta spec. Rejeitado. |
| 5 | BH | O AC5 não tem tarefa de execução | `low` | Real (redacção): o AC é de verificação, não de produção. Remédio edita esta spec. Rejeitado. |
| 6 | BH | Os dois registos append-only ficaram em branco | `low` | O `Review Triage Log` é preenchido nesta passagem e o `Spec Change Log` ganha a linha "Vazio" das irmãs. Rejeitado. |
| 7 | BH | `(Q2)` pendente no `deferred-work.md` | `low` | Real: a spec deixou de numerar as decisões ao respondê-las. Corrigido (passa a apontar à secção *Decisions*). |
| 8 | BH | A citação do AC está mal atribuída | `low` | Real: a frase é o AC da 1.4 no `epics.md`. Corrigido. |
| 9 | BH | Donos imprecisos no registo diferido; o marcador ocre-sobre-navy fica sem dono | `low` | Real. Corrigido (a 2.2 passa a ser nomeada dona do marcador). |
| 10 | BH | `#131736` e `#c9a86a` mortos em `style.css:524-526` | `low` | Pré-existente: o bloco `REFINAMENTO v2.1` vence por ordem de ficheiro e a 1.4 não tocou nessas linhas. Diferido. |
| 11 | BH | O `base` sobre ocre dá 2.26:1, abaixo dos 3:1 | `false` | O anel pinta-se fora da caixa, sobre o fundo do elemento-pai: sobre claro e ocre vale o `navy` (6.85:1, medido a partir dos hex) e sobre navy vale o `base` (15.6:1). Ambos ≥3:1. |
| 12 | BH | A matriz confunde o fundo do elemento com a superfície onde o anel pinta | `low` | Real enquanto redacção; o código está certo (o anel pinta-se sobre o pai). Remédio edita esta spec. Rejeitado. |
| 13 | BH | A matriz cobre quatro das seis superfícies prometidas | `low` | Real; a `Verification` manda percorrer "cada fundo". Remédio edita esta spec. Rejeitado. |
| 14 | BH | O foco substitui a elevação de repouso | `false` | É o pretendido: a substituição dura só o `:focus-visible` e o `Always` garante a elevação fora do foco, que o diff confirma intacta. |
| 15 | BH | A justificação do `!important` é inexacta e conta mal as declarações | `low` | Real: o `outline` não tem concorrente e são três `!important`. Remédio edita esta spec. Rejeitado. |
| 16 | BH | "2px de afastamento" descrito como intervalo, implementado colado | `low` | Leitura dupla do `focus-ring` do `DESIGN.md`; o `navy` fica a 2px do elemento e o `base` preenche esses 2px. Remédio edita esta spec. Rejeitado. |
| 17 | BH | «Só `style.css` muda» é falso na árvore de trabalho | `false` | A fronteira é sobre o código do tema; os restantes ficheiros (`deferred-work.md`, `sprint-status.yaml`, esta spec) são registo do método. |
| 18 | BH | A mudança do anel em `.ipcn-card-v2` e na barra de cookies não está registada | `low` | Real e pretendida: o anel é global e o AC2 exige todos os focáveis, incluindo essas superfícies. Remédio edita esta spec. Rejeitado. |
| 19 | BH | A regra genérica `.wp-block-post-template…has-background` continua sem dono | `low` | Real: a 1.3 tinha-a atribuído à 1.4 e o âmbito congelado da 1.4 é o foco. Diferido para 1.8/1.10. |
| 20 | BH | O comentário órfão de `style.css:753` passa a parecer o rótulo da regra | `low` | Pré-existente: já precedia o bloco de foco em `b773cf9`. Diferido com a documentação. |
| 21 | BH | A expectativa do `grep` da `Verification` não reproduz | `low` | Real: o `grep` apanha `is-style-outline` e comentários; só metade da expectativa ("nenhum `none`/`0`") se cumpre. Remédio edita esta spec. Rejeitado. |
| 22 | BH | Referências de linha do Code Map misturam estados pré/pós-edição | `low` | Real (as linhas acima do ponto de edição deslocaram-se). Remédio edita esta spec. Rejeitado. |
| 23 | BH | `context:` omite a espinha, o `DESIGN.md` e o `AGENTS.md` | `low` | Real, mas o Code Map nomeia-os com caminho. Remédio edita esta spec. Rejeitado. |
| 24 | BH | O carimbo do mu-plugin só nomeia metade das declarações divergentes | `low` | Fora de âmbito pela própria intenção: o `Never` exclui o mu-plugin (AD-6) e a superfície Divi é congelada. Rejeitado. |
| 25 | BH | A entrada do ledger foi inserida a meio, sem regra de ordem | `low` | Real: as irmãs acrescentam ao fim. Movida para o fim do `deferred-work.md`. |
| 26 | EC | As checkboxes do painel de cookies ficam sem anel visível | `false` | O painel é `#131736`; o anel `base` (branco) desenha-se nos 0–2px à volta da caixa nativa e é visível sobre o escuro. |
| 27 | EC | O `style.css` também carrega no editor, que perde o `box-shadow` próprio | `maybe-false` | Confirmado que `inc/setup.php:69-78` o enfileira no editor e que a regra é nua com `!important`; falta ver o editor no browser. Severidade `medium` por confirmar. Diferido. |
| 28 | EC | O par ocre-hover+chumbo (CTA em hover) sem razão no `DESIGN.md` | `low` | Real: o `DESIGN.md` só dá a razão do repouso; a de 8.25:1 vive no `README.md:40`. Diferido. |
| 29 | EC | O ledger diz `in-progress` e a spec `in-review` | `low` | Duplicado do #1; mesma correcção. |
| 30 | EC | O par ocre-hover não tem razão declarada | `low` | Duplicado do #28 (mesma raiz). Diferido com ele. |
| 31 | VG | O anel não tem verificação executável; voltar à lista fechada passa no mesmo verde | `medium` | Real e pré-verificado pela camada: o `check-php.sh` nunca lê CSS e não há runner nem CI (NFR9). Disposição `defer`: a passagem no browser em `stagingredesign` fecha; a alternativa mecânica é infraestrutura. |
| 32 | VG | A expectativa do `grep` da `Verification` | `low` | Duplicado do #21. Rejeitado. |
| 33 | VG | `README.md:44` e a auditoria datada ainda descrevem o anel terracota | `low` | Real: deriva de documentação causada pela mudança. Diferido. |

**Agrupamento e encaminhamento**

- **Grupo A — desalinhamento do ledger** (#1, #29): `patch` — o ledger passou a `review`.
- **Grupo B — redacção e ordem do registo diferido** (#7, #8, #9, #25): `patch` — a entrada da 1.4 foi reescrita (sem `(Q2)`, com a atribuição ao `epics.md` e a 2.2 nomeada do marcador) e movida para o fim do ficheiro.
- **Grupo C — coerência interna desta spec** (#3, #4, #5, #6, #12, #13, #15, #16, #18, #21, #22, #23): `rejeitar` — o remédio edita esta spec.
- **Grupo D — declarações mortas do CTA primário** (#10): `defer`.
- **Grupo E — a regra chega ao editor** (#27): `defer` — severidade `medium` por confirmar no browser.
- **Grupo F — razões declaradas em falta nos CTAs/cookies** (#28, #30): `defer`.
- **Grupo G — verificação executável do anel** (#31): `defer`.
- **Grupo H — documentação do regime de foco** (#20, #33): `defer`.
- **Grupo I — a regra genérica da grelha sem dono** (#19): `defer`.
- **Grupo J — o nome do ficheiro não casa com a chave do ledger** (#2): `defer` (dívida a montante, repetida).
- **Refutados:** #11, #14, #17, #24, #26, #32.

**Cascata:** nenhum grupo encaminha para `intent_gap` nem `bad_spec` — a intenção congelada não foi desmentida e não há defeito de spec que o código devesse ter evitado. Não há loopback (`review_loop_iteration` fica 0). Os grupos A e B foram aplicados; D a J vão para `deferred-work.md`; C e os refutados ficam rejeitados. Depois do *patch*, o `check-php.sh` mantém os mesmos 17 problemas aceites.

## Design Notes

**Anel duplo vs. `box-shadow` já gasto.** O `outline` não empilha e o tema gasta `box-shadow` no cartão, no campo e no hover dos CTAs — dois `box-shadow` como anel seriam cobertos. `outline: 2px solid var(--wp--preset--color--navy); outline-offset: 2px;` + `box-shadow: 0 0 0 2px var(--wp--preset--color--base)` dá `base` dentro + `navy` fora; precisa de especificidade que vença o repouso/hover.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 `block markup` aceites (`inc/listings.php`, `inc/agenda-block.php`), saída 1.
- `grep -rn -- 'outline' wp-content/themes/ipcn-fse/` — esperado: só o `:focus-visible` novo; nenhum `none`/`0`.

**Manual checks (if no CLI):**
- Sem WordPress local, o anel sobre cada fundo e o menu mobile só fecham no browser em `stagingredesign`, após deploy e purga (`?nocache=1`).
- Por inspecção: nenhum fundo ocre com texto claro; a regra do anel usa `base` e `navy`.
