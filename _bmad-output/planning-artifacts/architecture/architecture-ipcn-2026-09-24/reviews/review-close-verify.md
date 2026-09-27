# Close-out verification — revised spine (2026-09-24)

Spine unedited.

1. **F1 — CLOSED (AD-14).** AD-1's two exceptions cover a pattern's markup passed to the block API and a registered `render_callback` printing plain HTML; AD-3 pins two patterns; AD-14 names the one card path (`core/post-template` in listings, `render_block()` with `postId` outside a loop).
2. **F2 — CLOSED (AD-9, AD-10).** `data_evento` only; an empty field "não aparece como próximo", "nem por poste nem por consulta"; AD-10 pins registration, `Y-m-d`, validation.
3. **F4 — PARTIAL (AD-3's table).** Slots and the per-surface tag (`category`/`tema_acervo`) are pinned. The marker is required but no AD names the element emitting it (AD-3 notes `core/post-featured-image` returns `''`), and the class contract `style.css` matches is unpinned — `ipcn-*` admits both `ipcn-card` and the still-live `ipcn-card-v2`.
4. **F5 — CLOSED (AD-15).** Two sibling `core/query` (perPage 1; perPage 2 + offset 1, date descending), numbers fixed, CSS-over-grid forbidden.
5. **F6/F7 — PARTIAL (AD-8, AD-9).** `/temas/<slug>` closes: archive template, term-description hero, `tema_acervo` tag. AD-9 closes Agenda/Home (same block, different limit), but no file owns the Agenda page, and `/category/agenda-ipcn/` is only disowned ("não serve a lista de encontros") — no owner, no behaviour.
6. **F9/F10/F12 — PARTIAL (AD-11).** The table pins actions, five field names, redirect bases and arg mapping (F9); the retention paragraph names transient + token, re-fill, error summary, `aria-invalid`/`aria-describedby`, delete (F10). F12 untouched — still "origem **ou** referer", nothing for the absent-header case.
7. **F11 — CLOSED (AD-5 + AD-6).** AD-5 scopes to "PHP **do tema**" and hands the mu-plugin to AD-6, which carries the authorised Divi-only guard; the Deferred waiver is gone, so all three agree.

## Adversarial pass

**One pair survives.** AD-9 says the Agenda and the Home section use "o mesmo bloco, com um limite diferente", but no AD names that block or its limit attribute (AD-3 pins pattern files; the Conventions pin hooks/handlers/shortcodes, not block names). Unit A (`inc/agenda-block.php`) registers `ipcn/agenda` with `limite`; Unit B's usage sites emit `{"limit":3}`. Both obey every AD; the unknown attribute leaves the default limit on both surfaces and FR-6's "lista completa" vanishes — and if the name diverges instead, nothing renders, indistinguishable from AD-9's "estado vazio". AD-13's check is a look, not a count.

Same shape, lesser: Unit A emits the marker as `ipcn-card__noimg`; Unit B's `style.css` reserves height on `.ipcn-card-noimg`. Both obey AD-3 (marker required, no element named) and AD-5 — the card still shrinks.
