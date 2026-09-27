# Spine Pair Review — IPCN

- **DESIGN.md:** `_bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/DESIGN.md`
- **EXPERIENCE.md:** `_bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/EXPERIENCE.md`
- **Sources checked:** `prds/prd-ipcn-2026-09-24/prd.md`; `wp-content/themes/ipcn-fse/theme.json`; `wp-content/themes/ipcn-fse/style.css`

## Overall verdict

The pair is source-extractable and decision-complete at the identity, IA and flow layers: tokens resolve cleanly, all five UJs get a real Key Flow, the Home/menu/no-search decisions are committed, and both files' shapes match the canonical forms exactly. The contract thins at the **component layer** — the two spines specify different inventories (behavioral components with no visual row, visual tokens with no behavioral row) — and a few inheritances drift: an invented `Memórias` section, component names that do not key across the files, and zero requirement (FR) traceability. No critical or high-severity findings; nothing blocks architecture or story-dev from extracting, but a component-side consumer will infer and invent where the visual row is absent.

## 1. Flow coverage — strong

Sources frontmatter resolves to the PRD; §2.3 defines UJ-1…UJ-5. Every one has a Key Flow with a named protagonist, numbered steps, an explicit `**Clímax:**` beat, a `Resolução` and a `Falha:` path:

| PRD | EXPERIENCE.md Key Flow | Protagonist | Climax | Failure |
|---|---|---|---|---|
| UJ-1 | Flow 1 — Camila lê o IPCN no autocarro | Camila ✓ | step 8 ✓ | Tema sem itens ✓ |
| UJ-2 | Flow 2 — João quer saber se há encontro esta semana | João ✓ | step 3 ✓ | Agenda vazia ✓ |
| UJ-3 | Flow 3 — Lúcia pede para se associar | Lúcia ✓ | step 4 ✓ | envio falha ✓ |
| UJ-4 | Flow 4 — Rita deixa uma notícia por aprovar *(fase seguinte)* | Rita ✓ | step 4 ✓ | publicar directo não existe ✓ |
| UJ-5 | Flow 5 — A contratante decide se pode ir para produção | Contratante ✓ | step 4 ✓ | bloqueio de leitura = devolução ✓ |

No misses. Flow 4 correctly carries the `*(fase seguinte)*` marker so a phase-1 consumer does not build it. Flow-level traceability to FR IDs is absent but is scored under §7 (examples do not tag either).

### Findings

No findings that add information.

## 2. Token completeness — strong

Every frontmatter token is defined per `design-md-spec.md`. All **11 color tokens carry hex** — no missing hex, so nothing critical. `theme.json` confirms the 7 slug-backed colors (navy, ink, muted, ocre, base, subtle, body); DESIGN.md correctly marks `terracota`, `chumbo`, `ocre-hover`, `text-muted` as non-slugs. Typography entries are valid subsets (`wordmark`/`button` omit `lineHeight`; allowed). `rounded` and `spacing` are well-formed.

Prose `{path.to.token}` references all resolve: 22 distinct paths used in DESIGN.md body (`colors.*` ×10, `typography.*` ×11, `spacing.*` ×8, `rounded.*` ×4 — all defined), plus 2 in EXPERIENCE.md (`{spacing.wide}`, `{spacing.content}`, both defined in DESIGN.md). No dangling reference in either file.

Contrast: target stated ("manter contraste WCAG 2.2 AA em texto e controlos", DESIGN.md → Do's; "WCAG 2.2 AA em texto, controlos e estados de foco", EXPERIENCE.md → Accessibility Floor) and the load-bearing combination is named ("não usar texto claro sobre ocre — não passa contraste"). Verified by computation: ocre `#c9a86a` on chumbo `#2d2418` ≈ 6.75:1 (AA pass), navy/base ≈ 15.5:1, terracota-on-white ≈ 5.0:1, text-muted-on-white ≈ 4.8:1 — all pass.

### Findings

- **low** The load-bearing pair is named but no numeric ratio is stated (DESIGN.md → Do's and Don'ts; EXPERIENCE.md → Accessibility Floor). A consumer must recompute to defend the "passes" claim against the contratante's SM-1 identity gate. *Fix:* add the computed ratios beside the ocre/chumbo and terracota-on-base rules (e.g. "6.75:1").

## 3. Component coverage — thin

Inventories do not match across the two files.

DESIGN.md.Components / frontmatter `components`: `button-primary`, `button-secondary`, `button-pill`, `button-ghost`, `card`, `eyebrow`, `tag`, `input`, `cookie-bar`.
EXPERIENCE.md.Component Patterns: Cabeçalho, Cartão de notícia, Cartão de acervo, Filtro de Tema, Paginação, Cartão de agenda, Acordeão de Tema no item, Formulário, Faixa de contacto, Barra de cookies.

Matched: Barra de cookies ↔ `cookie-bar`; Formulário ↔ `input` (partial — form vs. field); Cartão ↔ `card` (partial — see below). Unmatched in DESIGN: Cabeçalho, Paginação, Filtro de Tema, Cartão de agenda, Acordeão de Tema, Faixa de contacto. Unmatched in EXPERIENCE: `button-primary`, `button-secondary`, `button-pill`, `button-ghost`, `eyebrow` (eyebrow/tag need no behavior; the buttons do).

### Findings

- **medium** Cabeçalho is specified behaviorally (EXPERIENCE.md → Component Patterns, "todas" surfaces) but has no DESIGN.md.Components row — no container, sticky/scroll behavior, wordmark layout or mobile-collapse appearance. It is the shell of every surface (memlog: first-level menu, pill buttons, nav from navigation post `5358`). *Fix:* add a `header` row to DESIGN.md.Components (background, container, wordmark placement, pill placement, collapsed state).
- **medium** Three EXPERIENCE cards collapse to one DESIGN `card` whose spec is "imagem 16:9 recortada, tag, título em card-title, data em caption". The agenda card (dia, nome, lugar; "Sem lugar, não se inventa um") has no image and does not fit that anatomy; the acervo card (Tema, título, data) only half-fits. *Fix:* split `card` into `card` (news) + `card-agenda` (no image, day/name/place), or add an explicit "no-image variant" clause.
- **medium** Paginação, Filtro de Tema, Acordeão de Tema no item and Faixa de contacto are behavioral-only. Pagination ("Página actual é texto, não ligação") and the Theme filter are load-bearing IA controls (memlog: discovery "depende de o Tema estar preenchido"). *Fix:* give each a visual row (or map Theme filter → `tag` and pagination → a new `pagination` row).
- **medium** `tag` is the one component whose behavior is genuinely load-bearing and unstated: DESIGN.md → Components says the tag carries "categoria da notícia ou Tema do acervo", but nothing says whether the card's Tema tag is tappable and navigates to the filtered Acervo (EXPERIENCE.md → Filtro de Tema). *Fix:* one line — the Tema tag is a link to `/temas/<slug>`; the news category tag is not.
- **low** `button-ghost` is defined in DESIGN.md frontmatter but has no row in the body Components section; it appears only as the "ghost (só necessários)" cookie option. *Fix:* add a body row, or note the ghost as a cookie-bar sub-variant.

## 4. State coverage — adequate

IA walks 15 surfaces (Home, Notícias, Secção, Notícia, Acervo, Tema, Item de Acervo, Agenda, Quem somos, Projetos, Fale connosco, Associe-se, Apoia-se, Política de privacidade, 404). State Patterns covers 11: Home empty, Acervo empty, Tema sem itens, Agenda empty, Encontro sem lugar, Sem imagem de destaque, Envio aceite, Envio falhado, Submissão automatizada, Cookies pendentes, Endereço errado. Focus is covered once for all surfaces in Accessibility Floor ("Foco visível … contorno terracota … 2px"), and permission-denied is correctly N/A ("Sem contas", Foundation) — both handled well.

### Findings

- **medium** No in-flight/submitting state for forms. Success, failure and honeypot exist, but nothing shows a pending state or prevents double-submit — the only protection against a duplicate `Associe-se` request is the honeypot. *Fix:* add "Envio em curso" (disabled submit, no duplicate request).
- **medium** Apoia-se has no state row. The expired/missing PIX QR is FR-10's load-bearing testable consequence and appears only as microcopy in Voice and Tone ("Código PIX em atualização"), not in State Patterns. *Fix:* add "QR vencido/em falta" → Apoia-se to the State Patterns table.
- **low** Cold-load and other empty states are uncovered: no loading state on any surface, no Notícias/Secção empty (only Home), and the UJ-2 edge case "encontro sem data" is absent (the table has "sem lugar" only). Server-rendered web makes cold-load low-impact, but the two empties are real. *Fix:* add Notícias/Secção empty rows and an "encontro sem data não é próximo" line.

## 5. Visual reference coverage — adequate

`imports/` exists but is empty; there are no `mockups/` or `wireframes/` directories anywhere under `ux-designs/`. There are **zero visual artifacts to reference**, so there are zero orphans and zero unspecific references. This is consistent with the memlog (`Sem ecrãs de referência.`). The spines-win-on-conflict rule is stated exactly once, in the EXPERIENCE.md header blockquote ("Onde este documento e um mock discordarem, este documento manda."); it is not restated per-section.

### Findings

- **low** The single conflict statement names an artifact class that does not exist (mocks), and neither spine carries the `→ Composition reference:` line the examples use. A consumer may hunt for mocks that were never produced. *Fix:* either drop "mock" from the sentence or state "no mockups in this delivery; tokens + prose are the spec."

## 6. Bloat & overspecification — strong

DESIGN.md carries editorial voice only where permitted (Brand & Style, Colors, Typography rationale). No token covered by a token is re-specified as pixels — the only raw values are the elevation shadows (`0 6px 20px rgba(13,23,107,.08)`), which is legitimate shadow language, not a tokenized value. EXPERIENCE.md prose is terse and decision-bearing: tables where a table works (IA, Voice and Tone, Component Patterns, State Patterns), bullets where a list is right (Interaction Primitives). No persona bios, FR text or scope paragraphs are restated. No section exists that a downstream consumer would skip. The two non-default sections (Inspiration & Anti-patterns, Responsive & Platform) both carry decisions (rejected login/carousel/search; 375px/782px/1440px).

### Findings

No findings that add information. (Inspiration & Anti-patterns lightly overlaps PRD §11 and memlog rejects, but the "why" it adds is the section's job — not bloat.)

## 7. Inheritance discipline — adequate

`sources` resolve: `prd.md`, `theme.json`, `style.css` and the sibling `DESIGN.md` all exist. The PRD path uses the `{planning_artifacts}` token; the theme paths are bare repo-relative and the sibling DESIGN.md is bare. UJ names are verbatim for UJ-1…UJ-4. Glossary terms are used as defined (Notícia ≠ Item, Tema is acervo-only, Associe-se is a message, Apoia-se is not checkout). EXPERIENCE.md token references resolve to DESIGN.md by name (spot-checked).

### Findings

- **medium** `Memórias` appears as a Notícias section in EXPERIENCE.md → Information Architecture ("Secção (Destaques, Diáspora, Colunistas, Notas, Editorial, Drops, Memórias)") but is in neither the PRD (§12 lists Destaques, Diáspora, Colunistas, Notas, Editorial, Drops) nor the memlog (which lists …Drops, Projetos). It is an invented taxonomy entry with no upstream source; a consumer building the section set will create a page nobody asked for. *Fix:* drop `Memórias`, or add it to the PRD/memlog section list.
- **medium** Component names are not identical across the two files and share no stable key. DESIGN.md uses kebab keys (`button-pill`, `card`, `cookie-bar`); EXPERIENCE.md uses Portuguese display names with different granularity (`Cartão de notícia`/`Cartão de acervo`/`Cartão de agenda` vs a single `card`). There is no cross-file identifier linking "the same component" — a story-dev consumer cannot join the two inventories mechanically. *Fix:* key both files to one name per component (e.g. EXPERIENCE rows cite the DESIGN key).
- **medium** No requirement traceability. Neither spine cites an FR ID; Flow titles carry no UJ IDs. UJ→flow is recoverable by matching titles, but FR→surface/pattern is not — and architecture/stories are the named downstream consumers (PRD §0). *Fix:* tag each Key Flow with its UJ-N and add an FR-N→surface/pattern column (or footnotes) to State/Component patterns.
- **low** UJ-5 flow title is near-verbatim, not verbatim: EXPERIENCE.md says "A contratante **decide**…"; PRD UJ-5 says "A contratante **diz** se pode ir para produção". *Fix:* match the source title.
- **low** Source path style is inconsistent: the PRD uses `{planning_artifacts}/…` while `wp-content/themes/ipcn-fse/theme.json`, `…/style.css` and `DESIGN.md` are bare. A resolver that keys on tokens finds the PRD but must know repo root / sibling context for the others. *Fix:* give every source a resolvable path (token or explicit relative base).

## 8. Shape fit — strong

DESIGN.md sections are present and in canonical order: Brand & Style → Colors → Typography → Layout & Spacing → Elevation & Depth → Shapes → Components → Do's and Don'ts (all eight, order-locked). EXPERIENCE.md has all eight required defaults in order (Foundation, Information Architecture, Voice and Tone, Component Patterns, State Patterns, Interaction Primitives, Accessibility Floor, Key Flows) plus the two required-when-applicable sections — Inspiration & Anti-patterns (triggered by the memlog's reference products and rejects) and Responsive & Platform (triggered by multi-surface + explicit 375/782/1440 breakpoints). No required-when-applicable section is missing; no default is dropped; no invented section lacks a reason.

### Findings

No findings that add information.

## Mechanical notes

- **Frontmatter completeness.** DESIGN.md: `name`, `description`, `status`, `sources`, dates, and all token blocks present. EXPERIENCE.md: `name`, `status`, `sources`, dates present (no `description` — matches the example experience files). No Mermaid diagrams in either file (n/a).
- **`rounded.full: 999px`** (DESIGN.md frontmatter) deviates from the spec's conventional `9999px`. Harmless in practice (still a pill at any real width) but a resolver expecting the convention sees an outlier.
- **Token reference count.** 22 distinct `{…}` paths in DESIGN.md body + component-object references; 2 in EXPERIENCE.md. All resolve; no typos, no undefined paths.
- **Sources existence re-verified on disk:** `wp-content/themes/ipcn-fse/theme.json` (7 color slugs, 3 font families, `blockGap` 1.5rem, navy link) and `style.css` both present; `theme.json` slugs match DESIGN.md's non-slug claims.
- **Component keys absent.** No component in either file carries a slug that both files share; join is by human-readable name only (see §7).
- **`status: draft`** on both files. Fine at draft; the pair should be promoted together (they are declared a pair in EXPERIENCE.md's header) so a consumer never reads a `final` EXPERIENCE against a `draft` DESIGN.
