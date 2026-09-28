---
title: 'Story 1.5 — A Home em cinco blocos, com a vitrine do Acervo'
type: 'feature'
created: '2026-09-28'
status: 'done'
route: 'dispatch'
review_loop_iteration: 0
baseline_commit: '26e340faec798213112e6b4314f01836e1e92c64'
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Duas grelhas de três cartões iguais não distinguem Notícias do Acervo, e o título do hero diz "há 50 anos" — uma idade que caduca. O `ipcn-card-feature` existe em `patterns/` sem consumidor.

**Approach:** Manter os cinco blocos e a ordem (hero, Notícias, Acervo, Agenda, contacto) e ganhar hierarquia: o hero perde o número de anos (o eyebrow `Desde 1975` dá a idade) e o Acervo troca a grelha de três por uma vitrine — uma peça em destaque (4:3, título headline) e duas secundárias, em dois `core/query` irmãos.

## Decisions

- **Hero:** a copy do `h1` passa a `Pesquisa, memória e cultura negra` (sai o "há 50 anos"); o eyebrow `Desde 1975 · Lapa, Rio de Janeiro` mantém-se e é ele que dá a idade.
- **Vitrine:** destaque = mais recente (`perPage:1`); secundárias = as duas seguintes (`perPage:2`, `offset:1`, mesma ordem), logo não repetem o destaque. Sendo `acervo_ipcn` e não `post`, nunca repete as Notícias.
- **Notícias:** fica com três cartões; expulsar conteúdo não-notícia é a história 1.6.
- **Vazio:** só a query do destaque leva `query-no-results`; a das secundárias não leva nenhum.
- **Contacto:** a faixa fica como está.

## Boundaries & Constraints

**Always:** cinco blocos na ordem hero, Notícias, Acervo, Agenda, contacto; filtro por slug (`noticias`, `acervo_ipcn`), nunca `term_id`; block markup; classes `ipcn-*`; CSS só em `style.css`; `ipcn-card-feature` uma vez e `ipcn/card` duas.

**Never:** `inc/agenda-block.php` (Épico 2), mu-plugin (AD-6), `inc/forms.php` e `inc/cookie-bar.php` (Épico 3); `WP_Query` novo ou `[ipcn_query_posts]`; `theme.json`; markup de cartão em PHP; `parts/header.html` e `parts/footer.html` (história 1.8).

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Acervo cheio | 3+ itens | Destaque = mais recente; secundárias = as duas seguintes | — |
| Acervo com 1 item | 1 item | Destaque mostra-o; coluna das secundárias vazia | Sem frase de vazio na query secundária |
| Acervo vazio | 0 itens | Uma só frase "Em breve, novos itens do acervo." | Só o destaque tem `query-no-results` |
| Imagens bloqueadas | `hero-bg.jpg` falha | Primeiro ecrã em texto: eyebrow, título, substância | — |
| Item sem imagem | Sem thumbnail | Cartão mantém altura, marcador `IPCN` | Já coberto pelo pattern |

</frozen-after-approval>

## Code Map

- `wp-content/themes/ipcn-fse/templates/front-page.html:3-26` — hero; só a copy do `h1` (l.10) muda.
- `templates/front-page.html:41-48` — Notícias: `perPage:3`, `categoryName:"noticias"`, `ipcn/card`. Mantém-se.
- `templates/front-page.html:52-72` — Acervo: fundo `subtle`, eyebrow + `h2` "Vozes do IPCN →" (l.58-61), query única `perPage:3` (l.65-70) → dois `core/query` irmãos.
- `templates/front-page.html:74-106` — Agenda (`[ipcn_home_agenda]`) e faixa de contacto em texto. Não mudam.
- `patterns/ipcn-card.php` e `patterns/ipcn-card-feature.php` — `Inserter: false`; 16:9 / 4:3, etiquetas `category` + `tema_acervo`. O docblock do feature (l.10) declara "sem consumidor" → actualizar.
- `style.css:480-488` — `.ipcn-card-feature` (4:3 + headline) já pronto.
- `style.css:123-151` — `.wp-block-post-template.is-layout-grid`: `gap:16px`, 1 coluna abaixo de 768px.
- `style.css:694-727` — overrides do hero e do `[style*="padding-top:96px"]` (só casa Notícias); o Acervo não é tocado.
- `inc/content-model.php:17-60` — CPT `acervo_ipcn` (`/acervo/`) e taxonomia `tema_acervo` (`/temas/`).
- `inc/setup.php:61-62` e `inc/listings.php:21-101` — `--ipcn-hero-bg`; `[ipcn_query_posts]` é só `post` e sem `offset`: não serve a vitrine.

## Tasks & Acceptance

**Execution:**
- [x] `templates/front-page.html` — trocar a copy do `h1` (l.10) por `Pesquisa, memória e cultura negra`, sem número de anos.
- [x] `templates/front-page.html` — trocar a query única do Acervo (l.65-70) por dois `core/query` irmãos num wrapper de vitrine: `perPage:1` → `ipcn/card-feature` com o `query-no-results`; `perPage:2`+`offset:1` → dois `ipcn/card` sem `no-results`; `queryId` distintos.
- [x] `style.css` — pôr o destaque ao lado das duas secundárias, a uma coluna no telemóvel, neutralizando a margem do layout de fluxo do wrapper.
- [x] `patterns/ipcn-card-feature.php` — actualizar o docblock "sem consumidor".

**Acceptance Criteria:**
- Given a Home, when carrego, then cinco blocos na ordem hero, Notícias, Acervo, Agenda, contacto.
- Given o `h1`, when leio, then não contém número de anos.
- Given as imagens bloqueadas, when vejo o primeiro ecrã, then leio em texto o eyebrow, o título e a substância.
- Given o markup do Acervo, when inspecciono, then dois `core/query` irmãos: `perPage:1` com `ipcn/card-feature` e `perPage:2`/`offset:1` com dois `ipcn/card`.
- Given 3+ itens, when vejo a vitrine, then o destaque é o mais recente e as secundárias os dois seguintes, sem repetições.
- Given um acervo vazio, when vejo a secção, then a frase de vazio aparece uma só vez.
- Given a faixa de contacto, when leio, then endereço, telefone e e-mail são texto.

## Implementation Notes

- **Quatro edições, três ficheiros.** `templates/front-page.html` — o `h1` (l.10) perdeu o número de anos e a query única do Acervo (l.65-70) deu lugar a dois `core/query` irmãos dentro de um `wp:group.ipcn-vitrine`. `style.css` — a regra `.ipcn-vitrine` (grid de duas colunas, uma no telemóvel) e a neutralização da margem do layout de fluxo (inserida depois de `.ipcn-card-feature`, `style.css:490-509`). `patterns/ipcn-card-feature.php` — docblock actualizado.
- **Vitrine.** `perPage:1` (`queryId:2`) → `ipcn/card-feature` com o `query-no-results`; `perPage:2`+`offset:1` (`queryId:3`) → `ipcn/card` **sem** nenhum vazio; ambos `postType:"acervo_ipcn"`, `order:"desc"`, `orderBy:"date"`, `inherit:false`. O `offset:1` impede que as secundárias repitam o destaque; por o CPT ser `acervo_ipcn` (e não `post`), nunca repete as Notícias. Filtro por CPT/slug, nunca `term_id`. `ipcn/card-feature` aparece uma vez e `ipcn/card` duas (a de Notícias e a das secundárias).
- **Armadilha do wrapper.** Um `wp:group` sem `layout` explícito cai no default do core (`block-supports/layout.php`, WP 6.4) → classe `is-layout-flow` e, por não ter `max-width`, ocupa a largura plena dos 1100px da secção; o layout de fluxo põe `margin-block-start` nos filhos. Com o wrapper em `display:grid`, essa margem desalinharia a coluna das secundárias — neutralizada com `.ipcn-vitrine > * { margin-block-start: 0 !important }` (o `!important` vence as regras de gap do core, que não o têm).
- **Layout.** Duas colunas `minmax(0, 1.4fr) minmax(0, 1fr)`, `align-items:start`; a partir de `max-width:768px` passa a uma coluna, alinhado com o breakpoint já usado por `.wp-block-post-template.is-layout-grid` (`style.css:147-151`). O `gap:16px` existente nessa regra separa as duas secundárias entre si. `minmax(0, …)` evita overflow horizontal a 320px (FR-13).
- **Vazio.** Só a query do destaque leva `query-no-results` ("Em breve, novos itens do acervo."); a das secundárias não leva nenhum, para a frase aparecer uma só vez quando o acervo está vazio.
- **Fronteiras respeitadas.** Não se tocou em `inc/` (incluindo `agenda-block.php`), no mu-plugin, em `theme.json`, em `parts/header.html`/`parts/footer.html`, na Agenda nem na faixa de contacto. Sem `WP_Query` novo, sem `[ipcn_query_posts]` e sem markup de cartão em PHP. Os cinco blocos mantêm a ordem hero → Notícias → Acervo → Agenda → contacto.
- **Verificação.** `bash scripts/check-php.sh` → os mesmos 17 `block markup` e saída 1 (idêntico ao baseline: nada sob `inc/` foi tocado). O `grep -c 'wp:query '` da secção *Verification* **não reproduz**: a `pattern` casa tanto as aberturas `<!-- wp:query { … } -->` como os fechos `<!-- /wp:query -->`, pelo que dá 6, não 3; o número de blocos `core/query` é 3 (`grep -c '<!-- wp:query {'` → 3). A expectativa da spec conta blocos, não linhas. Ver `Review Triage Log` para o encaminhamento desta deriva, como na 1.4 (#21).
- **Sem WordPress local.** As linhas de render da matriz (vitrine com 3+, 1 e 0 itens; primeiro ecrã com imagens bloqueadas) só fecham no browser em `stagingredesign`, após deploy e purga (`?nocache=1`); as restantes são cobertas por inspecção do markup e do `style.css`.
- **Patches da revisão (4 grupos).** `style.css` — o `!important` e o `> *` deram lugar a `.ipcn-vitrine > .wp-block-query + .wp-block-query { margin-block-start: 0 }` (especificidade basta) e o breakpoint alinhou com os 782px do tema; além disso, `.ipcn-vitrine:not(:has(> .wp-block-query:last-child > *))` colapsa a grelha quando não há secundárias, para o destaque (ou a frase de vazio) não ficar a meia largura com uma coluna morta. `templates/front-page.html` — o comentário da secção passa a fixar a tripla `perPage`/`offset`/`order` e a sua fragilidade (AD-15), e a citar o `ipcn/card-feature`/`ipcn/card`. Nenhum destes patches toca no bloco congelado nem abre superfície nova.

## Spec Change Log

Vazio: nenhum achado da revisão encaminhou para `bad_spec` nem para `intent_gap`. A deriva do `grep` da `Verification` fica registada nas *Implementation Notes* (conta linhas de abertura e fecho), sem alterar a intenção congelada.

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo; **EC** = caça a casos-limite; **VG** = lacunas de verificação.
Veredictos: `high`/`medium`/`low` (defeito real), `false` (refutado), `maybe-false`. Agrupamento e encaminhamento no fim.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 1 | BH | O `Review Triage Log` fica vazio e as *Implementation Notes* apontam-lhe | `low` | Real e resolvido neste passo (o registo é preenchido agora). Rejeitado: o remédio edita esta spec. |
| 2 | BH | O `grep` da `Verification` não reproduz (6, não 3); anotado, não corrigido | `low` | Real: o padrão casa aberturas e fechos. Rejeitado: o remédio edita esta spec (ver #22). |
| 3 | BH | O único comando CLI (`check-php.sh`) não lê nada do que esta história muda | `low` | Real: o script limita-se a `inc/` e o diff não toca lá. Grupo F. |
| 4 | BH | A tripla `perPage`/`offset`/`order` está "pinned nowhere" | `low` | `false` quanto ao "nowhere": o AD-15 (`epics.md:81`), o AC da 1.5 (`epics.md:244`) e as *Decisions* fixam-na. Verdadeiro o residual: nada no template o diz. Grupo C. |
| 5 | BH | O `offset` do core só vale dentro do ramo do `perPage` e isso não está registado | `low` | Real (o código de blocos do core aplica o `offset` sob o guarda do `perPage`); hoje o `perPage` existe, logo não há defeito vivo. Grupo C. |
| 6 | BH | `!important` onde o controlo de `blockGap` do tema bastava | `low` | Real: `theme.json:27` dá `blockGap: "1.5rem"`. Grupo D. |
| 7 | BH | `.ipcn-vitrine > *` é mais largo que o seu propósito | `low` | Real: o alvo é o segundo filho. Grupo D. |
| 8 | BH | O comentário novo descreve mal o mecanismo; o `gap:16px` ganha dois donos | `low` | Real a primeira parte (o core usa `.is-layout-flow > * + *`, não "os filhos"); a segunda não tem dano — coluna e pilha são elementos distintos. Grupo D. |
| 9 | BH | Breakpoint 768 introduzido sem reconciliar com o 782 do tema | `low` | Real: `style.css` alterna o ritmo das secções a `min-width:782px`. Grupo E. |
| 10 | BH | O render com 0 e 1 itens fica sem dono (coluna morta) | `medium` | Real: sem posts o `post-template` não imprime nada e o `div.wp-block-query` fica vazio mas ocupa a coluna de 1fr. O acervo é pequeno hoje (EXPERIENCE/.memlog). Grupo B. |
| 11 | BH | O `deferred-work.md` fica intocado e a passagem no browser não aconteceu | `low` | Real: o registo diferido da 1.3 fecha-se com essa passagem e ela está pendente. Grupo F. |
| 12 | BH | O estado afirmado e a verificação registada discordam | `low` | Real enquanto redacção: `in-review` é o estado certo; a passagem no browser é verificação manual. Grupo F. |
| 13 | BH | O `Code Map` não foi recalculado e mistura estados de linha | `low` | Real: o Acervo passou a 53-81. Rejeitado: o remédio edita esta spec (como na 1.4 #22). |
| 14 | BH | Nada diz ao leitor ou à tecnologia assistiva qual item é o destaque | `low` | Real, mas sem dano WCAG: o conteúdo é todo acessível e o destaque é ênfase visual. Rejeitado: o remédio exigiria copy nova, isto é, editar esta spec. |
| 15 | BH | O `context:` omite os artefactos que a intenção cita | `low` | Real. Rejeitado: o remédio edita esta spec (como na 1.4 #23). |
| 16 | BH | A idade fixa foi trocada por uma data fixa, sem dono para a próxima caducidade | `false` | O eyebrow `Desde 1975` é o ano de fundação, estável, e a EXPERIENCE §Home decide-o explicitamente ("o eyebrow já dá a idade, o título não caduca"). |
| 17 | BH | Só um dos dois docblocks foi actualizado | `false` | `patterns/ipcn-card.php` nunca enumerou consumidores: descreve as casas e a proveniência do contexto, e já nomeia a Home. Nada nele ficou falso. |
| 18 | EC | Com 0-1 itens a coluna das secundárias fica vazia | `medium` | Duplicado do #10 (mesma raiz). |
| 19 | EC | Datas empatadas com `offset:1` e sem desempate podem repetir uma peça e perder outra | `maybe-false` | Não decidível do repositório: depende do `ORDER BY` do core e do plano do MySQL. Diferido com o que o fecharia. Grupo H. |
| 20 | EC | Um `layout` constrained no wrapper quebraria o `1.4fr`/`1fr` | `false` | O wrapper não tem `layout` e nada no diff o torna constrained: resolve a fluxo. |
| 21 | EC | `?query-2-page=2` repete um cartão | `low` | Real, mas precisa de URL forjado e o remédio não se exprime em block markup. Rejeitado. |
| 22 | VG | O `grep` da spec não reproduz nem fixa o AC | `low` | Pré-verificado e real. Rejeitado: o remédio (corrigir a spec) edita esta spec; a alternativa de infra está no Grupo F. |
| 23 | VG | A superfície do template e do CSS não é lida por verificação nenhuma | `low` | Pré-verificado e real (sem runner nem CI, `AGENTS.md:26`). Grupo F. |
| 24 | VG | O apontador para o `Review Triage Log` e a linha "Vazio" do `Spec Change Log` | `low` | Real enquanto redacção do mesmo passo. Rejeitado: o remédio edita esta spec. |

**Agrupamento e encaminhamento**

- **Grupo A — redacção desta spec** (#1, #2, #13, #14, #15, #22, #24): `rejeitar` — o remédio de cada um edita esta spec.
- **Grupo B — a vitrine com menos de três itens** (#10, #18): `patch` — a grelha colapsa quando a coluna das secundárias não tem conteúdo.
- **Grupo C — o contrato frágil dos dois `core/query`** (#4, #5): `patch` — comentário no template a fixar a tripla e a dependência.
- **Grupo D — a neutralização da margem no CSS** (#6, #7, #8): `patch` — selector escopado, sem `!important`, e comentário correcto.
- **Grupo E — o breakpoint da vitrine** (#9): `patch` — alinhado com os 782px do tema.
- **Grupo F — cobertura executável e passagem no browser** (#3, #11, #12, #23): `defer` — sem runner nem CI (NFR9) e sem WordPress local, a infra de verificação é dívida pré-existente (já diferida na 1.3) e a passagem em `stagingredesign` é manual.
- **Grupo G — semântica do destaque** (#14): ver Grupo A.
- **Grupo H — datas empatadas** (#19): `defer` — severidade por confirmar, sem remédio em block markup.
- **Refutados:** #16, #17, #20, #21.

**Cascata:** nenhum grupo encaminha para `intent_gap` nem `bad_spec` — a intenção congelada não foi desmentida e não há defeito de spec que o código devesse ter evitado. Não há loopback (`review_loop_iteration` fica 0). Os grupos B a E foram aplicados no `style.css` e no template; F e H vão para `deferred-work.md`; A e os refutados ficam rejeitados. Depois do *patch*, o `check-php.sh` mantém os mesmos 17 problemas aceites.

## Design Notes

**Wrapper da vitrine.** Os dois `core/query` irmãos (`postType:"acervo_ipcn"`, `date desc`, `inherit:false`) vivem num `wp:group` (ex.: `className:"ipcn-vitrine"`); o `offset:1` da segunda impede a repetição do destaque. **Armadilha:** um `wp:group` sem `layout` explícito recebe o layout de fluxo e põe `margin-block-start` no segundo filho — com o wrapper em `display:grid` isso desalinha a coluna das secundárias; neutralizar essa margem ou fixar o layout. O `post-template` de cada query fica a uma coluna e o `gap:16px` existente separa as secundárias.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 `block markup` aceites, saída 1.
- `grep -c 'wp:query ' wp-content/themes/ipcn-fse/templates/front-page.html` — esperado: 3.

**Manual checks (if no CLI):**
- Sem WordPress local, a vitrine e o primeiro ecrã com imagens bloqueadas só fecham no browser em `stagingredesign`, após deploy e purga (`?nocache=1`).
