# Adversarial Review — IPCN Architecture Spine

**Reviewed:** `ARCHITECTURE-SPINE.md` — 13 ADs, the Consistency Conventions table, the Structural Seed — against `.memlog.md`, `prd.md`, `DESIGN.md`, `EXPERIENCE.md`, `AGENTS.md`, `docs/auditoria-redesign-fse-2026-09-17.md`, and the code it governs (`wp-content/themes/ipcn-fse/`, `wp-content/mu-plugins/`).
**Date:** 2026-09-24 · **Reviewer role:** adversarial architect
**Target claim under attack:** *"two units built one level down, each obeying every AD to the letter, cannot be built incompatibly."*
**Method.** For each invariant I build two units (Unit A / Unit B) that obey the invariant's **rule text** to the letter and whose artifacts disagree. An AD counts as obeyed through its rule text, not its intent: where a rule is wider than its `Binds`, I take the wider reading, and where two clauses of one AD cannot both hold, I show the unit that satisfies each. Evidence is line-level in the real code, because the spine's own purpose is to prevent a second truth. Pairs are ranked by downstream impact, not by how easy they are to fix.

**Scope rule applied.** The spine's `sources:` are the PRD and the UX pair; those documents carry the acceptance criteria the ADs exist to serve. A divergence only counts if it is *silent* — if a human sees it in review, AD-13 catches it. Every finding below is silent at least once.

---

## Verdict

**The spine is not yet buildable as two independent units.** It governs *where* code lives and *which mechanism* to use, but it does not pin the **shared shapes that cross the file boundaries it creates**. Nine of nineteen findings are open contracts between `patterns/*.php`, `inc/*.php`, `templates/*.html`, `style.css` and the database — the exact surfaces AD-1/AD-3/AD-4 cut in two. Two competent units, each reading every AD literally, will disagree on: what the card contains, where the agenda's date comes from, who owns the tag slot, which template serves `/temas/`, what the form fields are called, what the consent object looks like, and how a render callback obtains shared markup that AD-1 forbids it to author.

Three ADs are internally unsatisfiable as written (AD-1 clause 2 vs clause 3; AD-5's rule vs AD-6's freeze; AD-10's `post_date` fallback vs FR-6's "um encontro sem data não aparece como próximo"), and one applied backlog item (audit T4/T9) directly contradicts AD-6.

The pattern to every finding is the same: **an AD that names a file/mechanism without naming the contract those files must share.** The remedy is mostly not new rules — it is giving ten ADs a "shape" clause.

Nothing here requires editing the spine's paradigm; block-first survives. AD-1 survives if it is given one exception class. AD-3 survives if it is given a slot table. AD-11 survives if it is given a contract table.

---

## Findings, ranked

| # | Severity | Pair (one line) | AD to tighten / add |
|---|---|---|---|
| F1 | **Critical** | Agenda card: `do_blocks()` route vs `render_block()` route vs plain-HTML route — three legal cards, one of them the exact defect AD-9 claims to kill | Tighten **AD-1**, **AD-3**; add **AD-14** |
| F2 | **Critical** | Agenda date: card reads `post_date` vs card reads `data_evento`; fallback per-post vs fallback per-query | Tighten **AD-9**, **AD-10** |
| F3 | High | `lugar` (FR-6) has no field, no key, no owner | Tighten **AD-10** |
| F4 | High | Card slot contract: tag slot (`category` vs `tema_acervo`) and the `IPCN` no-image marker — plus the class list `style.css` must match | Tighten **AD-3**, **AD-5** |
| F5 | High | "one destaque + two compactos" inside one `core/query`: two-query offset vs CSS-first-child | Tighten **AD-3**, **AD-2** |
| F6 | High | The agenda's *second* surface (FR-6 "lista completa"): one windowed block vs a second mechanism; `/category/agenda-ipcn/` has no owner | Tighten **AD-9**; amend the Seed |
| F7 | High | `/temas/<slug>`: the Seed omits the taxonomy template that audit **T6 deleted** — FR-4 acceptance regresses and AD-8 binds FR-4 | Tighten **AD-8**; amend the Seed |
| F8 | High | The archive hero: PHP map (still the code) vs term description (AD-8); two owners of the same string | Tighten **AD-8**, **AD-1** |
| F9 | High | The form contract's unpinned pieces: field names, `action` values, redirect base, one message pattern keyed by two different args | Tighten **AD-11**; add field names to the Conventions |
| F10 | High | AD-11's one-bit feedback cannot deliver FR-8's retention + error summary + `aria-invalid` binding | Tighten **AD-11** |
| F11 | High | AD-5's rule vs AD-6's freeze — and an unapplied P1 backlog item (T4/T9) that orders the removal AD-5 names | Tighten **AD-5**; correct the Deferred note |
| F12 | Medium-High | "verificação de origem **ou** referer" — absent header: reject (blocks real visitors) vs accept (honeypot only) | Tighten **AD-11** |
| F13 | Medium-High | AD-7 says `term_id`; the nav is bound by `ref:5358` and the footer by `data-id` | Tighten **AD-7** |
| F14 | Medium | `inc/` ownership: AD-4's file list omits `agenda-block.php`; `functions.php` "is a loader" vs "does the setup"; three claimants for `enqueue_block_editor_assets` | Tighten **AD-4** |
| F15 | Medium | Pattern registration: core auto-file (`ipcn-fse/ipcn-card`) vs `register_block_pattern('ipcn/card')` vs a `Slug:` header — and PHP inside a pattern runs before the CPT exists | Tighten **AD-3**; pin the slug |
| F16–F19 | Medium → Low | Consent object shape and TTL; the hero custom-property name; the palette question (terracota/chumbo not tokens); the pagination state channel; AD-13 guards neither defect class | See *Minor pairs* |

---

## F1 — Critical — The agenda card can be built three legal ways, and two of them are the defect AD-9 exists to prevent

**Unit A** obeys AD-1's exception clause: *"Quando PHP tem de produzir markup em tempo de render, passa pelo API de blocos (`do_blocks()` ou `render_block()`)"* → in `inc/agenda-block.php`'s render callback it calls

```php
do_blocks( '<!-- wp:pattern {"slug":"ipcn-fse/ipcn-card"} /-->' );
```

**Unit B** obeys AD-1's first sentence to the letter — *"PHP nunca escreve comentários de bloco como strings soltas"* — and therefore refuses the string, reaching for the other named escape hatch: `render_block()` with a parsed-block array, i.e. the card's markup re-expressed as PHP arrays inside `inc/agenda-block.php`.
**Unit C** obeys AD-1 read as "block **comments** are forbidden, plain HTML is not" (which is what `ipcn_query_posts` does today, `functions.php:196-208`: a card built from `<a class="ipcn-card">…`) and prints the compact card as HTML.

All three are readable out of AD-1. They are not compatible:

- **A violates AD-1's own rule text.** `do_blocks()` takes a string of block markup. AD-1's second clause demands the markup live in `templates/*.html` or `patterns/*.php`; its third clause hands the render callback the only two entry points that require that markup *as a PHP value*. For the one block AD-9 forces into existence, AD-1's clauses 2 and 3 cannot both hold.
- **B satisfies AD-1's letter and destroys the point of it.** The markup now lives in `inc/agenda-block.php` as an array — a second source of truth for the card, which is precisely AD-3's `Prevents` ("três implementações de cartão a divergir"), now four.
- **C re-creates the card AD-3 forbids**, and its markup is *not* the pattern's, so a change to `patterns/ipcn-card.php` never reaches the agenda.
- **And the pattern route fails silently in a *new* way.** `do_blocks()` of `wp:pattern` resolves the card's `core/post-title`/`post-date`/`post-featured-image` against **block context**, which the agenda's render callback does not have. Today's shortcode happens to call `$q->the_post()` at `functions.php:505-506` before printing its (dropped) block comments, so a "fix" that routes through `do_blocks()` *keeps* working **only if the unit also remembers `the_post()`**; drop it and every card renders the front page's own title, linked to the front page. That is *worse* than the blank card the memlog records (`memlog:15`), because it looks like content.

**Evidence this is the live failure class, not a hypothetical:** `functions.php:499-525` prints `<!-- wp:post-title -->` etc. inside `ob_start()` with no `do_blocks()` anywhere in the file, and `$thumb` is collected and never used (`functions.php:507`) — the memlog's observed defect. AD-3's `Prevents` names it ("a da agenda, que não renderiza"). The two ADs that exist to kill it both route a competent unit back into it.

**Which AD to tighten.** Tighten **AD-1** with one exception class and add **AD-14**:

> **AD-14 — Markup partilhado entre pattern e render callback.** Um componente que precisa de aparecer tanto num pattern como num render callback é um bloco registado no servidor (`ipcn/card`, atributo `variant`), com `uses_context: ["postId"]` e um `render_callback` que devolve HTML simples. Os patterns referem-no como `<!-- wp:ipcn/card {"variant":"compacto"} /-->`. Nenhum `render_callback` escreve block markup, em string ou em array. Um bloco só tem uma implementação: `do_blocks()` só aceita slugs; `render_block()` só aceita blocos.

This is the single highest-leverage edit in the review: it closes F1, F4's marker half and F5 in one move, and it keeps AD-1's rule intact rather than adding a second exception.

---

## F2 — Critical — The agenda's date has two sources and two fallbacks, and AD-10 contradicts FR-6

**Unit A** builds the card's date as `core/post-date` (AD-3's compact card, unmodified), so the agenda shows the **publication** date. **Unit B** builds the card's date as a dynamic slot reading `data_evento` (AD-9: *"lê `data_evento` (`Y-m-d`)"*; AD-3: *"A agenda usa o compacto, com a data vinda de `data_evento`"*), which AD-3's static pattern cannot host. Both units obey AD-3 and AD-9: AD-3 does not say which block occupies the date slot, and AD-9 does not say the date is rendered by the card or by the block.

Then the fallback. AD-9's rule text — *"lê `data_evento` … com `post_date` como alternativa"* — is **two-ways readable**: per post, or per query. The code picks per query, and the reading matters:

`functions.php:440-471` runs a `meta_query` for `data_evento >= today`; **if any post matches, that query is used and the `date_query` branch is skipped entirely.** A post with a future `post_date` and an empty `data_evento` therefore appears today and **disappears the day someone fills the first `data_evento`** — including posts the client just created for upcoming events. AD-10 says the opposite of the query: *"Deixar o campo vazio é válido e faz cair no `post_date`"* — a per-*field* fallback. Two units obeying each text produce two different agendas from the same data, and neither is contradicted by its AD.

Worse, **AD-10's rule contradicts its own sources.** `prd.md:152`: *"Um encontro sem data não aparece como próximo."* `prd.md:63`: *"Um encontro sem data não conta como próximo."* `EXPERIENCE.md:115`: *"O encontro sem data não conta como próximo. Se estiver listado, aparece sem data em vez de com uma data inventada."* Falling back to `post_date` for a dateless event does exactly what that ARC forbids: it lists it as upcoming **with an invented date** (today's publish date shown as the event date). A unit that implements AD-10 fails FR-6; a unit that implements FR-6 contradicts AD-10. Neither is wrong on its own document.

**Which AD to tighten.** Rewrite both:

- **AD-9** — the fallback is **per post**: the window is the union of "future by `data_evento`" and "future by `post_date` **for rows with no `data_evento`**", expressed as two queries merged (AD-9 already owns the only own-query privilege, so this is in scope), or as a `meta_query` with an explicit `NOT EXISTS` arm. Also state that the date slot is filled by the agenda block's component, not by `core/post-date` alone.
- **AD-10** — replace *"Deixar o campo vazio é válido e faz cair no `post_date`"* with: empty `data_evento` ⇒ the item is **not** "próximo" and, if it appears on the complete list, it appears **without a date** (no `post_date`). `post_date` is a **migration** rule for rows created before the field existed, must be named as such, and must not appear in the Home window.

---

## F3 — High — `lugar` has a requirement, no field, no key and no owner

`prd.md:146`: *"O Visitante vê os próximos encontros com data, nome e, quando existir, lugar."* `EXPERIENCE.md:92`: `card-agenda` — *"Dia, nome e lugar. Sem lugar, não se inventa um."* `EXPERIENCE.md:114`: *"Encontro sem lugar → Mostra dia e nome; o lugar não aparece."*

AD-10 registers **only** the date; AD-9 reads **only** `data_evento`; AD-3's card slot list does not exist (see F4). So "lugar" arrives by accident:

- **Unit A** puts it in the post excerpt (today's agenda card uses `core/post-excerpt`, `functions.php:514`) → the "lugar" is the first ~30 words of the body text, truncated. It invents a place. FR-6's "o lugar não se inventa" fails.
- **Unit B** adds a meta key (`lugar_evento`, `local`, `onde`) with a registered field → the field exists but no AD names the key, so a third unit reading `data_evento`-style naming writes `evento_lugar`, and the card reads the other one. The card renders an empty place line, which is *indistinguishable* from "no place given" — FR-6's ARC passes by luck.

**Which AD to tighten.** **AD-10** becomes the event-field AD, not the event-date AD: name the full event shape (`data_evento` **Y-m-d**; `evento_lugar` **texto, opcional**) with the same registration mechanism, and say that neither is a custom-field by hand. Add both keys to the Conventions table next to `data_evento`.

---

## F4 — High — The card's *contents* are pinned nowhere, and the refactor erases the no-image marker

AD-3 pins each variant's image ratio and title scale. It never pins **which slots the card holds**, and the real surfaces need three different slot sets:

| Surface | Today | `EXPERIENCE.md:91` `card` | Slot needed |
|---|---|---|---|
| Home / Notícias / Secção | `post-terms term:"category"` + title + date (`front-page.html`) | "tag, título e data" | category tag |
| Acervo archive / Tema | `post-terms term:"tema_acervo"` + title + excerpt (`archive-acervo_ipcn.html`) | "tag, título e data" | **Tema** tag |
| Acervo card on the Home | `post-terms term:"tema_acervo"` + title + date | same component, "Acervo, Tema" | **Tema** tag |
| index.html | title only (`index.html`) | "tag, título e data" | tag + date |

**Unit A** pins `core/post-terms {"term":"category"}` inside `patterns/ipcn-card.php` — legal, AD-3 says nothing — and every Acervo/Tema listing loses its tag. FR-4's ARC catches it (`prd.md:124`: *"Um Item distingue-se de uma Notícia pela tag de Tema visível"*) but no AD does. **Unit B** makes the tag slot conditional, which means PHP inside a pattern (see F15: it is evaluated once, before the CPT exists, and frozen) or a dynamic slot. Both obey AD-3.

**The marker is worse, because AD-3 *deletes its only implementation*.** `prd.md:106`: *"Um cartão sem imagem mostra o marcador `IPCN`, sem encolher o cartão em relação aos vizinhos."* `EXPERIENCE.md:116` repeats it for `card` and `card-feature`. The only implementation in the repository is in the legacy shortcode AD-3 orders replaced:

```php
// functions.php:201-205
if ( $img ) { … } else { '<span class="ipcn-card-media ipcn-card-noimg" aria-hidden="true">IPCN</span>'; }
```

`core/post-featured-image` returns `''` when a post has no thumbnail — **no element at all** — so neither `:empty` nor `::after` in `style.css` can produce the marker, and AD-5 forbids the alternative homes. Unit A ships a card without the marker (FR-2 and FR-4's neighbour-height consequence regress silently, on exactly the posts the client's archive has most of); Unit B smuggles a `render_block` filter into `inc/listings.php` (AD-4-compliant, AD-1-compliant since plain HTML is not a block comment) and now there are two implementations again — or a doubled marker if Unit A also wrote an `:after` rule.

**And the class contract is split-brain already.** `style.css` currently ships **both** contracts: `.ipcn-card` (`:359-410`) and `.ipcn-card-v2` (`:479-513`). The Conventions table only says "classes CSS `ipcn-*`", which `.ipcn-card` and `.ipcn-card-v2` both satisfy. Unit A's pattern uses `ipcn-card`, Unit B's card block emits `ipcn-card-v2` (because that is the class the *existing* Acervo markup and CSS use), and AD-3's "Inserter: false" does not retire either stylesheet block. Two cards, same listing, half the rules.

**Which AD to tighten.** **AD-3** gains (a) a **slot table** per variant — required slots and the element that fills each, including "the tag slot takes `category` on Notícias surfaces and `tema_acervo` on Acervo/Tema surfaces", (b) the **no-image rule**: the card's image slot is a component that emits the `IPCN` marker when there is no thumbnail (this is the `ipcn/card` block of AD-14), and (c) the **class list** `ipcn-card`, `ipcn-card-media`, `ipcn-card-noimg`, `ipcn-card-title`, `ipcn-card-date` as the single contract `style.css` matches, with `ipcn-card-v2` named as removed.

---

## F5 — High — "one destaque + two compactos" is not expressible by the mechanism AD-2 mandates

`prd.md:105`: the Acervo showcase *"usa composição própria — uma peça em destaque e duas secundárias — diferente da grelha compacta de Notícias."* `DESIGN.md:305`: `card-feature` *"Aparece uma vez, ao lado de dois cartões compactos."* `EXPERIENCE.md:93`: *"Vitrine: uma peça em destaque com dois cartões compactos ao lado. Nunca duas grelhas iguais seguidas na mesma página."*

AD-2 sends listings to `core/query`; AD-3 gives two card variants. But `core/post-template` applies **one** template to every item — there is no "first item gets the other template". AD-2 and AD-3 therefore cannot be satisfied together by the obvious reading, and neither AD says who resolves it:

- **Unit A**: two `core/query` blocks inside the Acervo section — `perPage:1, orderBy:date, order:desc` and `perPage:2, offset:1`, the first containing `wp:pattern` with the feature card. Both queries are `core/query` (AD-2 satisfied), both cards are patterns (AD-3 satisfied). It works **only because** the two units agree on the `perPage`/`offset`/`order` triple across two sibling blocks — a shared shape pinned nowhere. Change one and the showcase repeats the featured item or drops one. Silent: the grid still looks like a 3-up grid.
- **Unit B**: one `core/query` perPage 3 with the compact pattern, and `style.css` promotes `:first-child` to the feature look (ratio 4:3, headline scale) — also AD-2- and AD-3-compliant (the feature *pattern* exists and is "the destaque of DESIGN.md"), and it makes `patterns/ipcn-card-feature.php` dead code on the very surface AD-3 assigns it to. Two thirds of `DESIGN.md:305`'s "imagem 4:3" is now a CSS override of a 16:9 block.

Today's code has neither: `front-page.html`'s Acervo section is **three identical `.ipcn-card-v2` cards**, i.e. FR-2's "composição própria" is unimplemented and AD-3's feature variant has **zero** consumers — so neither unit can be checked against reality.

**Which AD to tighten.** Add to **AD-3** the composition clause: the owner of "1 destaque + 2 secundários" (the Home Acervo section, in markup), the offset contract between the sibling queries (`perPage`, `offset`, `order` fixed in one place), and an explicit "the feature variant is not produced by CSS over the compact variant". If the two-query route is chosen, say so; if a single query is chosen, AD-14's card block must take a `variant="feature"` **and** an index-aware attribute, which is a different design decision than the one AD-3 implies.

---

## F6 — High — The agenda has two surfaces and AD-9 names one window; `/category/agenda-ipcn/` has no owner at all

`prd.md:146`: *"A Agenda tem superfície própria com a lista completa; a Home mostra os próximos."* Two surfaces, two semantics: complete list vs next N. AD-9 describes **one** block (*"o bloco da agenda"*) that *"corre a sua própria consulta em tempo de render"* — a window. It never says which surface the block serves, whether it takes a depth attribute, or where the complete list lives.

- **Unit A** places one windowed block on both surfaces (AD-9's only named mechanism). The Agenda page shows **three** events — the "lista completa" of FR-6 is unmet, and the failure is invisible unless someone opens the Agenda page with more than three upcoming events.
- **Unit B** places the block on the Home and builds the Agenda surface as a `core/query` with `inherit:true` (AD-2: an inherited archive query is explicitly the legitimate mechanism) → the Agenda page lists **every** event including past ones, which `prd.md:159` forbids (*"Não aparece uma grelha de encontros passados no lugar dos próximos"*). AD-9's `Prevents` (four divergent listing mechanisms) is re-created inside the agenda.

Worse, the **category archive** `/category/agenda-ipcn/` is bound by AD-8 (agenda-ipcn is a top-level category) and by FR-5/footer links, and the Structural Seed's template list contains **no** `category-agenda-ipcn.html` and **no** `category.html`. So `templates/archive.html` serves it — the notícias-flavoured archive: the hero shortcode's PHP map with a `ucwords` fallback (`functions.php:387-389`), `post-terms term:"category"`, and an **unfiltered, undated** grid. Past events are listed as if upcoming, in the place FR-7 forbids. Neither unit is at fault; the spine never assigns the surface.

**Which AD to tighten.** **AD-9** must name the surfaces and their semantics: the agenda block is the **only** owner of the temporal window, takes a depth attribute, and is what the Agenda surface and the Home both use (then FR-6's "lista completa" is the block with an "all upcoming" setting). Amend the **Structural Seed** to list the surfaces AD-8's Binds implies (`category-agenda-ipcn.html` or a shared agenda template), and add a rule: *every surface an FR names has exactly one owner file in the Seed.*

---

## F7 — High — The Seed canonizes a template deletion that breaks FR-4, and the repo documents the broken state as correct

The Seed lists eight templates: front-page, single, page, archive, archive-acervo_ipcn, single-acervo_ipcn, index, 404. **No taxonomy template.** `AGENTS.md:19` documents the consequence as normal: *"Não existe template de taxonomia — `/temas/<slug>` cai no template do archive."*

That state is the applied result of **audit T6** (`docs/auditoria-redesign-fse-2026-09-17.md:174-192`), which instructed deleting `taxonomy-tema_acervo.html` on the claim that *"WordPress vai cair no `archive-acervo_ipcn.html` como fallback (hierarchy de templates FSE)"* — **which is false**. The FSE hierarchy for a taxonomy request is `taxonomy-{tax}.html` → `taxonomy.html` → `archive.html` → `index.html`. `archive-acervo_ipcn.html` is the archive for the `acervo_ipcn` post type and is not in that chain. So `/temas/<slug>` lands on **`archive.html`**: the notícias hero (`[ipcn_archive_hero]`, `archive.html:3`) and `post-terms term:"category"` in the card — for **Acervo items**, which have no category. FR-4's acceptance regresses on both halves (`prd.md:122-124`): the Tema tag disappears, and the fallback hero prints the generic "Selecao de conteudo publicado pelo IPCN." for every Tema.

Now the two units:

- **Unit A** obeys the Seed (a closed-looking list) + `AGENTS.md` → does not add a taxonomy template. FR-4 fails, and the AD that binds FR-4 (AD-8) is silent about the surface.
- **Unit B** obeys AD-8 (Tema is its own axis, `rewrite: /temas/`) + FR-4 → re-adds `taxonomy-tema_acervo.html`, which **reverts a just-applied P1 backlog item** and re-creates the duplication T6 removed.

Both obey every AD. The spine has no rule about template-surface ownership, so the conflict is invisible until the Contratante opens `/temas/<slug>`.

**Which AD to tighten.** **AD-8** gains: the model's surfaces are part of the model — each axis (Secção, Agenda, Tema) names the template that serves it, and `/temas/<slug>` is served by the Acervo templates, not by the Notícias archive. Then correct the **Seed** (add the taxonomy template, or state explicitly that `archive-acervo_ipcn.html` is cloned/aliased for it) and record the correction to audit T6, since T6's rationale is factually wrong about the hierarchy and remains in the repo as instruction.

*(Verify in the review environment: an empty Tema — `prd.md:123` "Um Tema sem itens explica-se" — may 404 before any template is consulted, because taxonomy archives with zero posts are 404'd by the main query. If it does, FR-4's "explains itself" needs its own mechanism, and this becomes a ninth finding.)*

---

## F8 — High — The archive hero has two owners of the same string, and AD-1 reads two ways about whether the current one is legal

`functions.php:361-411` renders the hero: `ob_start()`, prints block comments, returns. **No `do_blocks()`, no `render_block()`, no dynamic block.** Under AD-1's rule text that is a violation (PHP wrote block comments as loose strings and nothing renders them as blocks) — yet the memlog's decision is to *re-engineer* this shortcode into a thin wrapper (`memlog:19`, `memlog:23`), not to delete it, and AD-9's dynamic-block exception is scoped to the agenda.

So: **Unit A** keeps a hero wrapper in `inc/listings.php` that `do_blocks()`es a hero pattern — AD-1 clause 3 satisfied, AD-4 satisfied, and it is the only route that can reproduce the current hero's `IPCN · <eyebrow · name>` line, which `core/query-title` cannot express. **Unit B** deletes the shortcode and uses `core/query-title` + `core/term-description` in `archive.html` — the pattern already proven in `index.html:3`, AD-1-clean by construction, and the only route AD-8's "o texto de apresentação vive na descrição do termo" can honour without a second truth. Both obey every AD. The two produce different hero markup, different strings and different sources.

Because AD-8 and the memlog move the section title/description **into the term**, the hero also has two owners **today**: the PHP map wins (`functions.php:387-391` never reads `$q->description`), so editing the term in the database has no effect on the page while the map stands. And the map is **stale against the spine's own model diagram**: it holds six slugs (`destaques, diaspora, colunistas, notas, noticias, editorial`) while the spine's content-model lists **seven** children — `drops` and `memorias` are missing, so those two sections fall to `ucwords(str_replace('-',' ',$slug))` and the generic description. Two units shipping the two sources produce different section pages, and only one of them is the "single source" AD-8 requires.

**Which AD to tighten.** **AD-8** — say it once: the term description is the only source for a Secção's presentation text, the hero is a **component** (not a shortcode), and list all seven children in the model diagram *and* the Seed. **AD-1** — resolve the hero the way F1 resolves the card: either the hero becomes a registered block (AD-14) or AD-1 names an explicit exception for wrapper shortcodes that return `do_blocks(...)` of a pattern. Do not leave the current hero legal-by-omission, because a reviewer reading AD-1 will delete it.

---

## F9 — High — The form contract's pieces are enumerated in prose and pinned nowhere

AD-11 enumerates the contract as **handler + redirect arg + message pattern**. The agreement that actually has to hold across `patterns/*.php` (markup, AD-1) and `inc/forms.php` (handler, AD-4) is wider, and the Conventions table pins exactly one of the pieces (`honeypot ipcn_hp`) plus the `ipcn_*` family for hooks/shortcodes/classes:

| Piece | Pinned? | Where it lives / crosses to | Divergence |
|---|---|---|---|
| field names | **no** | pattern markup ↔ handler validation (`functions.php:98-100,136-138`) | Unit A's pattern names `nome`/`email`; Unit B's handler requires `ipcn_nome`/`ipcn_email` → **every** submission lands on `?cadastro=erro`. AD-11 makes `erro` a legitimate outcome, so the failure looks like a validation rule, not a bug. |
| `action` values | **no** (only `ipcn_*` family) | pattern hidden input ↔ `admin_post_{action}` | `ipcn_assoc` / `ipcn_contact` vs `assoc` / `fale-conosco` → `admin-post.php` renders "-1" or a blank page; AD-13's browser check at `?nocache=1` would catch it only if someone submits. |
| redirect base | **no** | handler ↔ header/footer/front-page markup and `data-id="3902"` | `home_url('/associe-se/')` (`functions.php:90`) vs the page identified by ID elsewhere (`parts/footer.html`) → moving the page silently breaks the message; the user lands on a 404 and never sees the feedback. |
| which arg is which | **half** — AD-11 says "`cadastro` ou `contato`", not "cadastro ⇒ Associe-se" | handler ↔ message pattern | the *singular* "o pattern da mensagem" of AD-11 (and "pattern do formulário", Capability Map; "o pattern da mensagem", memlog:25) must serve **both** forms, but `ipcn_contact_message` reads `$_GET['contato']` and `ipcn_assoc_message` reads `$_GET['cadastro']` (`functions.php:328,345`). Unit A builds **one** message pattern (AD-11's singular) → it is blank on one of the two forms; Unit B builds two patterns → contradicts the singular phrasing and the memlog decision. |
| field-level error state | **no** | handler → pattern | see F10. |

**Which AD to tighten.** Give **AD-11** a contract table (piece → single owner → exact value), the way the Conventions table pins `ipcn_hp`, and add the field names and `action` values to the Conventions rows. Say explicitly whether the message pattern is one pattern keyed by both args or two patterns.

---

## F10 — High — AD-11's one-bit feedback cannot deliver FR-8's acceptance criteria

AD-11's mechanism: no nonce, no server state, feedback carried by a query argument with values `ok`/`erro`, and the message pattern renders from it. That is **one bit**.

`prd.md:175` requires, for a failed send: *"erro na mesma página, campos preenchidos mantidos, resumo de erro no topo e possibilidade de repetir. Nunca confirma o que não aconteceu."* `EXPERIENCE.md:119` repeats it and `EXPERIENCE.md:141` adds *"Erro de campo identificado por `aria-invalid` e ligado por `aria-describedby`; um resumo de erro no topo recebe o foco."* `EXPERIENCE.md:98` requires `autocomplete` on `name`/`email`/`tel` and a visible associated label.

- **Unit A** implements AD-11 as written: redirect with `?cadastro=erro`, one message block, **no** field retention, **no** per-field state, **no** error summary. FR-8's acceptance fails on three of four clauses, and AD-11's rationale ("não guarda estado no servidor") is the reason it *cannot* work: a one-bit, stateless channel cannot say *which* field failed, cannot restore values, and cannot focus a summary.
- **Unit B** implements FR-8: to keep values it needs server-side state (a transient/session — which AD-11 forbids precisely because LiteSpeed serves cached HTML and it endangers the form itself) **or** client-side repopulation keyed off the same query arg (which is stateless and legal, but needs JavaScript in a place no AD names: AD-5 governs CSS only, and the Seed's `assets/` contains `hero-bg.jpg` and nothing else).

So the two units differ not in style but in *whether FR-8 is met at all*, and the spine gives the second unit nowhere legal to put its JS.

**Which AD to tighten.** **AD-11** must fix the retention mechanism and the error-state pieces, in the AD (not the code comment AD-11 complains about): e.g. *"the failed state is carried by the query argument plus a client-side repopulation keyed off the same argument; the script is enqueued from `inc/forms.php` as `assets/forms.js`; the summary is a block that receives focus; per-field state is derived from the same argument, never from a stored field list."* Add `assets/forms.js` to the Seed, or state "no JS asset" and accept FR-8 partially — but say it.

---

## F11 — High — AD-5 names the mu-plugin's `<style>` blocks as the defect; AD-6 freezes the file that contains them; and the repo has an open P1 task ordering their removal

AD-5's `Prevents`: *"CSS em quatro sítios — `style.css`, um `wp_add_inline_style` e três blocos `<style>` no `wp_head` a partir do mu-plugin."* Its rule: *"Nenhum PHP emite `<style>`. A única excepção é a propriedade personalizada do fundo do hero."*
AD-6: *"Não se acrescenta lógica FSE ao mu-plugin, não se divide, e não se faz deploy dele numa alteração só de tema"*, `Prevents`: *"Partir o staging Divi, que está congelado."*
The Deferred section: *"O CSS Divi que o mu-plugin serve ao site FSE. Aceite: o ficheiro está congelado por política. Só se reabre se a linha Divi for retirada."*

AD-5's rule, read at its letter, **orders the removal of a file AD-6 forbids touching** — and the spine waives it out-of-band in Deferred instead of scoping the AD. Two units, both compliant:

- **Unit A** reads AD-5 unconditionally (a rule is a rule; the exception list is exhaustive and does not mention the mu-plugin) and removes the three `wp_head` blocks (`ipcn-etmodules-fix` at priority 1, `ipcn-form-style` at 2, `ipcn-footer-logo-fix` at 3 — `ipcn-optimizations.php:51-205`) → AD-6's `Prevents` breached, Divi staging broken.
- **Unit B** reads AD-5 as bounded by "o CSS **do tema**" and by its `Binds: FR-11..13`, and by AD-6 → leaves them → AD-5's rule text breached, and the spine's promise is a promise it cannot keep.

This is not hypothetical. **The repository already carries the work order.** `docs/auditoria-redesign-fse-2026-09-17.md` **T4** (*"Limpar mu-plugin `ipcn-optimizations.php` — remover código legado Divi"*, acceptance: `grep -c "et_pb\|ETmodules\|et_builder"` returns 0) and **T9** (*"Mover CSS inline do mu-plugin para style.css do tema … O mu-plugin resultante deve ter apenas filtros PHP … zero `echo "<style>"`"*) are **P1, unapplied**, and `STATUS.md`/`57825f5` show backlog items do get applied. A story-thread that pulls T4/T9 (the spine says such stories exist: *"Remoção das três implementações de cartão actuais. Faz parte das histórias"*) is **blocked by AD-6** and no AD says so.

Two further facts for the fix: (a) the memlog says *"Dois desses 3 são só para Divi"* — **all three are Divi-only** (`.et_pb_*`, `.et-l--footer .et_pb_*`, and the ETmodules `@font-face`); (b) the ETmodules block builds its URL from `get_template_directory_uri()`, which on the FSE site resolves to `/wp-content/themes/ipcn-fse/core/admin/fonts/ETmodules*` — a path that does not exist in this theme. The FSE site therefore ships a 404-ing `@font-face` it never uses, inside a `<style>` block, emitted from the frozen file, and AD-5's `Prevents` sentence describes it correctly while AD-6 protects it.

**Which AD to tighten.** **AD-5** must name its boundary: *"AD-5 binds `wp-content/themes/ipcn-fse/`. Nada em `mu-plugins/` é superfície de AD-5; o tema não emite `<style>`."* Then either **AD-6** gains an explicit amendment path for T4/T9 (*"a limpeza do mu-plugin exige decisão; enquanto não houver, o Deferred vale como satisfação de AD-5"*) or those backlog items are cancelled. Correct the Deferred/memlog count from two Divi-only blocks to three.

---

## F12 — Medium-High — "verificação de origem **ou** referer" is undefined where it matters: the header is absent

AD-11: *"A protecção é o honeypot mais uma verificação de origem ou referer, que não guarda estado no servidor."* An origin/referer check has one hard case and AD-11 does not decide it: **the request arrives with no `Referer`/`Origin`/`Sec-Fetch-Site`.** That happens for the client's own privacy browser, for a cached page served with stripped referrers (`?nocache=1` is an aid, not a guarantee), and for every user who pastes the URL.

- **Unit A** rejects on absence (strict; zero bot false-negatives; and it is what the AD's `Prevents` implies — *"protecção documentada que não existe no código"* must become protection that exists). Real visitors get `?cadastro=erro`, i.e. FR-8's *"Nunca confirma o que não aconteceu"* passes while the form becomes unusable for a subset nobody measures.
- **Unit B** accepts on absence (lenient), which in practice reduces the protection to the honeypot — the exact state AD-11's `Prevents` calls a defect, reached by obeying AD-11.

Which header counts as "origem", which value is acceptable (the review host? `home_url()`?), and whether absence is a reject or an accept are all unpinned. Two units ship two different protection strengths under the same AD, and neither is contradicted by it.

**Which AD to tighten.** **AD-11** gains one line: *"A verificação aceita `Origin`/`Sec-Fetch-Site` de `home_url()`; a ausência de qualquer cabeçalho é [rejeitada/aceite] porque [razão]."* Pick one.

---

## F13 — Medium-High — AD-7's rule is narrower than its purpose: object identity in markup

AD-7: *"`category_name` e `categoryName` com slug; consultas de taxonomia por slug. O `term_id` não aparece em código nem em template."* Its `Prevents`: *"Ruptura entre ambientes — o `term_id` muda entre a base de dados do redesign e a do Divi, e mudaria outra vez em produção."*

The rule covers **queries**; the stated risk covers **identity**. Markup already carries identity:

- `parts/header.html:26`: `<!-- wp:navigation {"ref":5358,…} -->` — `EXPERIENCE.md:23` documents that `5358` **is** the navigation post of the redesign database, and `AGENTS.md:20` adds *"`5358` no staging Divi é outro objecto"*. On production, post 5358 is whatever it is there; the header nav resolves to a foreign or empty navigation entity. `STATUS.md` even records `5358` as the Apoia-se PIX QR **attachment** on the other database — the ID is already ambiguous inside the repo's own documentation.
- `parts/footer.html`: `data-id="3902"` and `data-id="4387"` sit beside the portable `href="/associe-se"` and `href="/projetos"` — two identities for the same page, one of which moves.

- **Unit A** reads AD-7 as a query rule (its rule text is about queries) and keeps `ref:5358`; AD-7 passes.
- **Unit B** reads AD-7's purpose and needs a slug-based navigation owner; the spine offers **no mechanism** (`EXPERIENCE.md:23` tells the implementer to edit the navigation in the database, i.e. the portable identity is the navigation's slug, and nothing in the spine resolves it to a `ref`). So Unit B either invents a mechanism in `inc/setup.php` or reaches for `core/page-list`.

**Which AD to tighten.** **AD-7** becomes "identity is by slug, always": no `term_id`, no fixed `ref`, no `data-id` in code or template; the navigation is resolved (or authored) by slug with one named owner.

---

## F14 — Medium — `inc/` splits leave three hooks/entities with more than one owner

**(a) AD-4's file list is silently incomplete.** AD-4: *"Comportamento novo entra num ficheiro de preocupação em `inc/` — setup, forms, listings, content-model, cookie-bar."* **Five files; `agenda-block.php` is not among them**, while the Seed and the Capability Map both require it (`inc/agenda-block.php` → FR-6, FR-7). AD-4 is the normative sentence; the Seed is a diagram. So a unit reading AD-4 as the closed list puts the agenda block in `inc/listings.php` ("helpers de consulta" is the nearest claim), and a unit reading the Seed puts it in `inc/agenda-block.php` → two files registering the same block name and the same `init` window (`register_block_type` on a taken name emits `_doing_it_wrong` and the later registration wins — silently, since both look correct in isolation), and two files claiming AD-9's "único lugar em que o tema corre a sua própria consulta".

**(b) `functions.php`'s boundary is stated twice, differently.** AD-4: *"`functions.php` é um carregador… Não acumula lógica"* **and** *"`functions.php` carrega-os e trata do setup do tema"*, while the Seed puts "enqueue, fontes, preconnect" in `inc/setup.php`. Enqueue therefore has two lawful owners; the file today registers `wp_enqueue_scripts` twice (`functions.php:15,51`) and `enqueue_block_editor_assets` once (`:69`), and AD-4 has no rule that a hook has one owner.

**(c) `enqueue_block_editor_assets` has three claimants and no owner.** AD-10 needs an editor surface for `data_evento`; the Deferred excuses only *"Script de editor para o bloco da agenda"*; `inc/setup.php` owns enqueue per the Seed; `inc/agenda-block.php` is the block's file. Two units add the same editor asset from two files (two handles, doubled CSS/JS in the editor), and neither violates anything.

**Which AD to tighten.** **AD-4**: make the Seed's file list normative (or list all six concerns in the AD), state *one owner per hook and per entity*, forbid two files registering the same block/hook, and settle the setup boundary ("`functions.php` = `require` only; `inc/setup.php` owns every enqueue").

---

## F15 — Medium — The card pattern's slug and registration mechanism are unpinned, and PHP inside a pattern is evaluated before the CPT exists

AD-3 says `patterns/ipcn-card.php`, "registados com `Inserter: false`"; the Design Paradigm says patterns are "Registado no `init`"; the Conventions table says *"Patterns `ipcn/<variante>`"*; the Seed says `patterns/ipcn-card.php`. Those four statements admit at least three implementations with two different slugs:

1. a core auto-registered pattern **file** → slug `ipcn-fse/ipcn-card` (theme-slug namespace, filename-derived);
2. `register_block_pattern( 'ipcn/card', … )` called from `inc/` → slug `ipcn/card` (matching the conventions table, not the filename);
3. a file with a `Slug: ipcn/card` header → either.

Nothing pins which. The referencing markup `<!-- wp:pattern {"slug":"…"} /-->` must match exactly, and `core/pattern` renders **empty** for an unregistered slug — a silent empty listing, in every card, on every surface, on a cached page. Two units, both AD-compliant, one slug each.

The same file is also where AD-1 and the paradigm note bite: core registers theme pattern files on `init`, and core's `init` callbacks are queued **before** the theme's `functions.php` runs, so PHP inside `patterns/*.php` executes **before** `inc/content-model.php` registers `acervo_ipcn`. A pattern that branches on `post_type_exists('acervo_ipcn')`, `is_singular()`, or `get_the_ID()` is evaluated **once** at that moment and frozen in the registry for every post thereafter. A unit that follows AD-3's "PHP lives in patterns" family and puts a per-post branch in the card is deterministically wrong, not randomly wrong. *(Verify once in the review environment: `add_action('init', fn() => error_log(var_export(post_type_exists('acervo_ipcn'), true)), 20)`.)*

**Which AD to tighten.** Pin in **AD-3** (or a setup line) the slug form and the registration mechanism (I would pick: files in `patterns/`, `Slug: ipcn/card` header, one slug form everywhere) and add: *"Um pattern não contém PHP dependente do loop, do post type actual ou de estado de request. A variante é escolha de ficheiro ou atributo do bloco de AD-14."*

---

## Minor pairs

| # | Severity | Pair | AD to tighten |
|---|---|---|---|
| F16 | Medium | **The consent object's shape/TTL.** Conventions pin the key only (`localStorage['ipcn_cookie_consent_v1']`). Unit A writes `{ts, necessary, analytics, marketing, all}` (today's code, `functions.php:583`) and gates analytics on `analytics`; Unit B writes a different object under the same key (e.g. `{analytics:'granted'}`) and rehydrates the panel's checkboxes from it. Both satisfy the convention; the writer and the reader disagree, so `prd.md:205`'s "antes de analítica ou marketing dispararem" holds or fails depending on who shipped what — and the code today **never reads the object back** (`consented()` only tests existence, `functions.php:575-577`), and the comment's "180d" expires nothing. | Extend the Conventions row to the full shape, the TTL, and the single reader; say whether the legacy CookieYes sync (`functions.php:587-589`) stays. |
| F17 | Medium | **Cross-file names the Conventions do not reach.** (a) The hero background is one custom property shared by `inc/setup.php` (emits `--ipcn-hero-bg`, AD-5's only exception) and `style.css:167` (consumes `var(--ipcn-hero-bg, none)` — a `none` fallback, so a rename in one file loses the image **silently**). (b) `DESIGN.md:248-249` says terracota `#a85a32` and chumbo `#2d2418` *"não é slug de `theme.json`"* and `AGENTS.md:35` repeats it, while AD-5's *"tokens em `theme.json`"* invites a unit to add them — changing the palette the editor offers, and hardcoded inline in `front-page.html`/`single.html`/`archive.html` in the meantime. | Conventions: name the custom property and the asset path as a pair; state which values are tokens and which are markup-local constants, and forbid adding palette slugs without a DESIGN change. |
| F18 | Medium-Low | **The pagination state channel.** Conventions say "paginação em `/page/N/`"; the wrapper's card grid reads `$_GET['paged']`, then `get_query_var('paged')`, then `page` (`functions.php:176-179`), while `core/query-pagination` uses the query's own `paged`/`page` var. Unit A keeps the private `$_GET['paged']` reader (nothing forbids it; the convention fixes only the URL shape); Unit B drops it and lets `core/query` paginate. On a surface carrying both (AD-2's listing + AD-3's wrapper), the two read different page state. | Extend **AD-2**: one pagination channel per surface, owned by `core/query`; wrappers do not read page state. |
| F19 | Low | **AD-13 guards neither defect class the spine exists to prevent.** `php -l` catches neither block-comments-in-strings (the memlog's observed defect, `memlog:15`) nor `<style>` emitted from PHP (AD-5's `Prevents`). Both pass the whole verification envelope. Also "todo o PHP alterado" has no definition (git diff? explicit list?), so two units ship two scripts. | **AD-13**: name the script and the checks — fail on `<!-- wp:` inside a PHP string literal under `inc/`, fail on `echo "<style` / `<style id=` under the theme, and define the file set as the git diff against the deploy base. |

---

## What I would change in the spine, in order

1. **Add AD-14 (shared render component).** One registered block per shared component (`ipcn/card` with `variant`, `uses_context: ["postId"]`, plain-HTML `render_callback`). Patterns reference it. No render callback authors block markup. Closes F1, most of F4, and gives F5 a place to live.
2. **Tighten AD-1** with that single exception class, and add the wrapper-shortcode rule for the hero (F8) so the current code is not legal-by-omission.
3. **Tighten AD-3** with the slot table, the no-image rule, the class list, the composition clause, the slug/registration mechanism, and "no request-dependent PHP in patterns". Closes F4, F5, F15.
4. **Tighten AD-9 and AD-10 as a pair** (per-post fallback; union query; the event shape including `evento_lugar`; empty `data_evento` ⇒ not "próximo", appears dateless). Closes F2, F3.
5. **Tighten AD-11** with a contract table and the absent-header policy, and fix the retention/error-state mechanism. Closes F9, F10, F12.
6. **Add AD-15 (surface ownership):** every surface an FR names has exactly one owner file, listed in the Seed — and fix the Seed for `/temas/` (F7) and the agenda surface (F6).
7. **Scope AD-5 to the theme directory** and amend AD-6 with the mu-plugin's decision, cancelling or explicitly deferring audit T4/T9 (F11).
8. **Widen AD-7 to object identity** (F13) and **tighten AD-4** to one-owner-per-hook with the full file list (F14).
9. **Extend the Conventions table** to the shapes it currently only names: form fields, `action` values, the consent object, `evento_lugar`, the hero custom property, the card class list. This one edit closes F16, F17, F18 and half of F9.
10. **Add the lint to AD-13** (F19) — the cheapest guard on the two highest-frequency defects.

## Assumptions and limits

- **The spine was not edited**, as instructed. Everything above is a proposal about the spine, not a change to it.
- **Two API behaviours are asserted from WordPress semantics and marked for one-shot verification in the review environment** rather than assumed silently: (a) theme pattern files are registered on `init` before the theme's own `init` callbacks, so PHP inside `patterns/*.php` sees no `acervo_ipcn` (F15); (b) whether `core/pattern` forwards a query-loop `postId` context, which decides whether Unit A of F1 renders the loop post or the front page. F1's conclusion (three legal cards, one of which reproduces the defect class) holds on either answer; only the *symptom* differs.
- **`patterns/` and `inc/` do not exist yet** in the checkout — confirmed by `AGENTS.md:38` (*"`patterns/` não existem"*) and by the directory listing. Every file-level claim about them is a claim about the Seed, not about code.
- **Severity ranks downstream impact** — how far a divergence travels before a human sees it, and how plausible it looks when it arrives. F1/F2 are Critical because they are silent in every listing and in the agenda's data, and because the failure surface (cards, dates) is the spine's stated reason to exist. F11 is High as a *spec* defect (an AD that cannot be obeyed alongside another, with a live P1 work order attached) but Low as today's build impact; I ranked it High because it is the one finding that makes a story thread unwritable.
- **Not reviewed here:** FR-10 (Apoia-se) beyond its asset note, FR-14–FR-16 (correctly deferred), and the operational envelope beyond F11/F19. The mu-plugin's `ipcn-mail-from.php` is consistent with the Conventions' "From forçado pelo mu-plugin".
