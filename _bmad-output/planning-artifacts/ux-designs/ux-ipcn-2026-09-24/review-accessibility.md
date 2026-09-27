# Accessibility Review — IPCN UX Spine Pair (adversarial)

**Reviewed:** `DESIGN.md` (design spine), `EXPERIENCE.md` (experience spine), with `prd.md` as context.
**Date:** 2026-09-24 · **Reviewer role:** accessibility (adversarial) · **Target claim:** WCAG 2.2 AA, public site, mobile-first, no login, client-approval gate before production.
**Scope rule applied:** only what the two spines actually commit to. A claim that is not measurable in the spine does not count as a commitment — it counts as a finding.

---

## Verdict

**Not approvable as written.** The pair makes a WCAG 2.2 AA claim (EXPERIENCE.md → *Accessibility Floor*; PRD §15; FR-12) but states **no contrast ratio for a single pairing**, so the claim is currently unfalsifiable. Computed against the exact hex values in the front matter, **three used pairings fail outright** and two more are untestable by construction:

| # | Used pairing | Computed | Needs | Result |
|---|---|---|---|---|
| 1 | `{colors.ink}` on `{colors.navy}` — cookie bar "só os necessários" (ghost) | **1.15:1** | 4.5:1 text | **FAIL (critical)** |
| 2 | `{colors.muted}` `#e2e8f0` border on `{colors.base}` — form inputs | **1.23:1** | 3:1 non-text | **FAIL (critical)** |
| 3 | `{colors.terracota}` `#a85a32` on `{colors.ocre}` — terracota accents/focus ring on the ocre band | **2.23:1** | 3:1 non-text / 4.5:1 text | **FAIL (high)** |
| 4 | `{colors.navy}` link inside `{colors.body}` body text | **2.05:1** | 3:1 vs surrounding text, else non-colour cue | **FAIL (high)** |
| 5 | `{colors.text-muted}` on `{colors.subtle}` — captions on alternating bands | **4.55:1** | 4.5:1 | passes by 0.05 — no margin (medium) |

The load-bearing pairings the spines actually *use* are fine where they are used correctly and are **not** the problem: `chumbo on ocre` = **6.75:1**, `base on navy` = **15.53:1**, `body on base` = **7.58:1**, `ink on base` = **17.85:1**, `terracota on base` = **5.03:1**, `terracota on subtle` = **4.81:1**, `chumbo on ocre-hover` = **8.25:1**. The stated rule "não usar texto claro sobre ocre" is correct (`base on ocre` = **2.26:1**) — the failures are elsewhere.

**Counts:** 3 critical · 9 high · 11 medium · 6 low. Two WCAG 2.2 criteria are verifiably satisfied by omission (3.3.8, 2.5.7 — see §7).

---

## 1. Contrast

### F-1 · **CRITICAL** — The cookie bar's third action is unreadable: 1.15:1
- **Location:** DESIGN.md → *Components* → `cookie-bar` (`backgroundColor: {colors.navy}`, `textColor: {colors.base}`) and → `button-ghost` (`textColor: {colors.ink}`); DESIGN.md → *Components* → *Barra de cookies* ("acções em ocre (primário) e outline (gerir) e ghost (só necessários)").
- **WCAG:** 1.4.3 Contrast (Minimum) AA — 4.5:1; 1.4.11 Non-text Contrast AA — 3:1.
- **What fails:** `button-ghost` carries **no background and no border** — only `textColor: {colors.ink}` (`#0f172a`). On the `cookie-bar`'s navy (`#0d176b`) that is **ink on navy = 1.15:1**. The token pair is internally coherent on a white page and catastrophic on the one surface the spine puts it on. "Só os necessários" is not text the user can be assumed to read; it is a control, so 1.4.11's 3:1 applies *in addition* to 1.4.3.
- **Fix:** Give the cookie bar its own action set instead of reusing `button-ghost`. On navy, render "Só os necessários" as `{colors.base}` text with a 1px `{colors.base}` underline or a 1px base border (**15.53:1**) — legally neutral, visually neutral, and it removes the dependency on `button-ghost` inheriting a surface it was never specified against. Add a `cookie-bar__action` component with an explicit `backgroundColor`/`textColor`/`borderColor` triple so no variant can be dropped onto navy unverified.

### F-2 · **CRITICAL** — A translucent cookie bar cannot have a testable contrast ratio
- **Location:** DESIGN.md → *Components* → *Barra de cookies* ("navy **translúcido** no fundo do ecrã, texto base"); EXPERIENCE.md → *Accessibility Floor*.
- **WCAG:** 1.4.3, 1.4.11, 2.4.11 Focus Not Obscured (Minimum) AA.
- **What fails:** The effective background of a translucent bar is a function of the page pixels scrolling *behind* it. Computed over `{colors.base}` the `base` text ranges from **15.53:1 (α=1.00) down to 4.46:1 (α=0.60)** — i.e. the text itself stays (barely) legal only for α ≳ 0.62, while the terracota focus ring falls from **3.09:1 to 1.13:1**. Over the ocre band (α=0.70) the ring is **1.86:1**. Nothing in the spine fixes α, the blur, or the scrim, so the ratio is unknowable at review time and drifts with scroll position. WCAG does not accept "it depends what is behind it".
- **Fix:** Make the bar **opaque** `{colors.navy}` (or `rgba(13,23,107,1)` with a solid fallback media query), or if translucency is the design intent, commit a floor: **α ≥ 0.95 with a documented `backdrop-filter`-free fallback**, plus a stated guarantee that the bar is never composited over the ocre band. Record the computed worst case in DESIGN.md next to the component.

### F-3 · **CRITICAL** — Form field boundaries are 1.23:1 (below the 3:1 non-text floor)
- **Location:** DESIGN.md → *components.input* (`borderColor: {colors.muted}`); DESIGN.md → *Colors* ("**Muted** — bordas de cartão **e de campos**").
- **WCAG:** 1.4.11 Non-text Contrast AA — 3:1 for the visual boundary of a user-interface component.
- **What fails:** `#e2e8f0` on `#ffffff` = **1.23:1**; on a `{colors.subtle}` band, **1.18:1**. Cards are arguable (not controls); **inputs are not** — the field boundary is the only thing that tells a user where the control begins and ends. `Associe-se` and `Fale connosco` (FR-8, FR-9) both depend on this border, and the field is white-on-white with no fill differentiation.
- **Fix:** Split the token: keep `{colors.muted}` for card borders only, and give inputs a boundary colour with ≥3:1 — **`#64748b` (4.76:1)** is already in the palette as `text-muted`, or `#556274` (6.20:1). State the requirement inline: "borda de campo ≥ 3:1 contra o fundo; `muted` serve cartões, não controlos."

### F-4 · **HIGH** — No contrast ratio is stated anywhere; the AA claim is unfalsifiable
- **Location:** DESIGN.md → *Do's* ("manter contraste WCAG 2.2 AA em texto e controlos") and *Don'ts* ("Não usar texto claro sobre ocre — não passa"); EXPERIENCE.md → *Accessibility Floor* ("O contraste vive em `DESIGN.md`"); PRD FR-12 ("O contraste dos textos e dos controlos cumpre WCAG 2.2 AA").
- **WCAG:** 1.4.3, 1.4.11 (the criteria being claimed).
- **What fails:** FR-12 and the Accessibility Floor both point at *DESIGN.md* for contrast, and *DESIGN.md* provides one qualitative rule and zero numbers. A reviewer at the client gate (UJ-5, FR-17) has nothing to check against, and neither does a future contributor. This is the root cause of F-1, F-3, F-5 and F-6: without ratios, nobody notices when a token lands on the wrong surface.
- **Fix:** Add a **contrast matrix** section to DESIGN.md pairing every text token against every surface token it may legitimately sit on, with the computed ratio and the required minimum (4.5:1 text / 3:1 large text and non-text). Include the *forbidden* combinations as rows too. The table in the Verdict above is a ready-made draft; the numbers are already computed from the declared hex values.

### F-5 · **HIGH** — The ocre contact band has no exception rules; four token pairings fail on it
- **Location:** DESIGN.md → *Colors* ("**Ocre** — Fundo de botão primário e da faixa de contacto"); DESIGN.md → *Components* → *Botão primário* (`backgroundColor: {colors.ocre}`); EXPERIENCE.md → *Component Patterns* → *Faixa de contacto* (address, phone, e-mail, "Botão para a página de contacto").
- **WCAG:** 1.4.3, 1.4.11.
- **What fails:** The spine assigns ocre to both the primary button *and* the contact band, then never says what may sit on the band. On `#c9a86a`: `body` text = **3.35:1** (fails 4.5), `text-muted` caption = **2.11:1** (fails), `terracota` eyebrow/tag = **2.23:1** (fails), and a `button-primary` (ocre) placed inside the ocre band is **1.00:1** — a button that does not exist. `eyebrow` is defined as terracota and is a rhythm element usable on any section, so this is not hypothetical.
- **Fix:** Add a surface rule to *Colors*: **on the ocre band only `{colors.chumbo}` text at ≥6.75:1**; no terracota accent, no `{colors.text-muted}` caption, no `{colors.body}` paragraph; any control inside the band must be `{colors.navy}` on ocre (**6.87:1**) or chumbo (6.75:1), never ocre. If the band must carry an eyebrow, put it above the band on `base`, not inside it.

### F-6 · **HIGH** — Terracota focus ring on ocre is 2.23:1
- **Location:** EXPERIENCE.md → *Accessibility Floor* ("contorno terracota e afastamento de 2px"); DESIGN.md → *Colors* ("Terracota — acento de rótulo, categoria e tag. Nunca fundo") and *Do's* ("Usar terracota só como acento de rótulo, tag e hover").
- **WCAG:** 1.4.11 Non-text Contrast AA (3:1 focus indicator against adjacent colours); 2.4.7 Focus Visible AA; 2.4.13 Focus Appearance (AAA) if the 2px offset is meant to satisfy it.
- **What fails:** Terracota `#a85a32` is prescribed as a single global ring with no per-surface handling. Computed against every surface in the pair: base **5.03:1** ✓, subtle **4.81:1** ✓, navy **3.09:1** ✓ (marginal), **ocre 2.23:1 ✗**, ocre-hover **2.72:1 ✗**. A `2px` offset actually *makes it worse* on the ocre band, because the ring sits on the band itself. Note also that terracota is explicitly "não é slug de `theme.json`", so this ring cannot be applied through global styles and is at risk of being dropped or inconsistently hardcoded.
- **Fix:** Replace the single-colour ring with a **dual ring** that is surface-independent: `2px solid {colors.base}` inner + `2px solid {colors.navy}` outer (navy on base = **15.53:1**, on subtle **14.85:1**, on ocre **6.87:1**, on ocre-hover **8.40:1**, on muted **12.60:1**; the white inner ring separates it from navy surfaces where navy itself is 1.00:1). Specify it in DESIGN.md as a component (`focus-ring`) rather than only in EXPERIENCE.md prose, and state in EXPERIENCE.md that on the ocre band the ring is the navy outer edge, not terracota.

### F-7 · **HIGH** — Navy body links are distinguished by colour alone (2.05:1 against the surrounding text)
- **Location:** DESIGN.md → *Colors* ("**Navy** — … links …"); DESIGN.md → *Components* — there is **no `link` component at all**; the front matter never declares `textDecoration`, `underlineOffset` or a link-hover treatment.
- **WCAG:** 1.4.1 Use of Color AA; 1.4.3 (link text itself is fine at 15.53:1).
- **What fails:** `{colors.body}` body text (`#475569`) and a navy link (`#0d176b`) differ by **2.05:1** — under the 3:1 that would let colour alone carry the distinction. In running prose (notícias use `{typography.reading}` at 19px, which is *not* large text at 400 weight for the 18.66px/24px rule), in-paragraph links will therefore be invisible as links to anyone who does not perceive the hue shift. `Playfair Display` long-read body makes this worse: a serif at 19px/1.75 is exactly the context where colour-only links get lost.
- **Fix:** Add a `link` component: underline by default inside body copy (`text-decoration: underline; text-underline-offset: 0.12em; text-decoration-thickness: 1px`), colour-only permitted only for standalone links in navigation/cards. Also decide link-visited styling, which is currently unspecified.

### F-8 · **MEDIUM** — `text-muted` captions pass on subtle by 0.05
- **Location:** DESIGN.md → *Colors* ("**Text-muted** — data e metadado em voz baixa"); DESIGN.md → *Elevation & Depth* ("Faixas de secção alternam `{colors.base}` e `{colors.subtle}`").
- **WCAG:** 1.4.3 AA (4.5:1).
- **What fails:** `#64748b` on `#ffffff` = **4.76:1**; on `#f8fafc` = **4.55:1**. Dates and metadata are `{typography.caption}` at **13px** — small text, 4.5:1 required, no large-text relief. Because the same caption appears on both band types, the passing value is the 4.55:1 one, and any drift (a 12px render, subpixel compositing, a browser gamma difference, an ocre-tinted overlay) drops it under.
- **Fix:** Darken `text-muted` to **`#5a6b80` (5.46:1 on base / 5.21:1 on subtle)** and re-state the token, or mandate that captions sit only on `{colors.base}`. Either closes the gap with real margin.

### F-9 · **MEDIUM** — The `button-pill` outline variant has no declared colours
- **Location:** DESIGN.md → *Components* ("a segunda em variante outline") — `button-pill` declares `backgroundColor: {colors.ocre}` and `textColor: {colors.chumbo}` but no outline border colour or text colour.
- **WCAG:** 1.4.11 (3:1 for the control boundary), 1.4.3 (its label).
- **What fails:** "Apoia-se" in the header is a control whose entire appearance is undefined. Whether it passes depends on an implementation decision nobody has made. The border is also the only thing marking the control's extent, so 1.4.11 applies to it.
- **Fix:** Declare the outline variant fully: `backgroundColor: transparent`, `borderColor: {colors.navy}`, `borderWidth: 1px`, `textColor: {colors.navy}` — and note that on the navy cookie bar or an ocre band the outline variant must swap to a base border.

---

## 2. Cookie bar — prominence and testability

### F-10 · **HIGH** — The two spines directly contradict each other on the three actions' prominence
- **Location:** EXPERIENCE.md → *Component Patterns* → *Barra de cookies* ("Três caminhos de **igual peso visual possível**: aceitar tudo, gerir, só os necessários") **vs** DESIGN.md → *Components* → *Barra de cookies* ("acções em **ocre (primário)** e outline (gerir) e **ghost (só necessários)**").
- **WCAG:** no direct AA criterion; this is FR-11 consent validity (LGPD) surfacing as an accessibility-of-choice problem. EXPERIENCE.md's own preamble says "Onde este documento e um mock discordarem, este documento manda" — it does not say the same about the *design spine*, so the conflict is unresolved by the pair's own rules.
- **What fails:** "Igual peso visual possível" is not testable — it names no measure. And it is contradicted by a three-tier hierarchy (filled ocre → outline → ghost) that *decreases* prominence in exactly the order that resists consent: accept-all is the filled primary, necessary-only is the faintest. The three paths are also not equal in WCAG terms: per F-1 the least prominent one is the one that fails contrast entirely. Given F-6, the middle one (outline) is also only *approximately* specified.
- **Fix:** Make it testable and consistent. Either (a) commit **three visually equivalent controls** — same button class, same size, same border weight, distinguish by label only — and write the acceptance test ("all three actions have identical computed `background-color`, `border-width`, `font-*` and rendered area"), or (b) drop the "igual peso" sentence from EXPERIENCE.md and accept an explicit, documented asymmetric hierarchy. Do not ship both sentences. Either way, add a **persistent way to re-open preferences** after a choice ("A escolha persiste neste browser" currently has no return path) — a footer link, specified in the footer contract with the other help mechanism (§3.2.6).

### F-11 · **MEDIUM** — No checkbox/switch/toggle component exists for "gerir" preferences
- **Location:** DESIGN.md → *Components* — the list is exhaustive (button-primary, button-secondary, button-pill, button-ghost, card, eyebrow, tag, input, cookie-bar) and contains **no checkbox, radio, switch or toggle**; EXPERIENCE.md → *Component Patterns* → *Barra de cookies* names a "gerir" path but no preference UI.
- **WCAG:** 1.4.11 (control boundary and on/off states at 3:1), 4.1.2 Name, Role, Value AA, 2.5.8 Target Size (Minimum) AA.
- **What fails:** The manage-preferences panel is the one place a user exercises FR-11's "gerir preferências", and it has no visual spec at all: no control boundary colour, no checked/unchecked state colours, no size. Unspecced controls default to browser styling, which will not match the theme and — more importantly — cannot be reviewed. Cookies are the compliance-sensitive surface; it is the wrong one to leave as an implementation detail.
- **Fix:** Add `checkbox`/`switch` and `radio` components to DESIGN.md with explicit `borderColor`, `checkedBackgroundColor`, `checkedIndicatorColor` (all ≥3:1 against their surface), a stated 44×44px hit area (F-20), and a visible focus treatment using the dual ring from F-6.

---

## 3. Forms without nonce / honeypot-based

### F-12 · **HIGH** — The honeypot's "silêncio" state breaks the spine's own confirmation promise and has no accessible outcome
- **Location:** EXPERIENCE.md → *State Patterns* ("**Submissão automatizada** | formulários | **Silêncio**. Não gera pedido, não diz que gerou.") vs EXPERIENCE.md → *Component Patterns* → *Formulário* ("ou confirma, ou devolve erro na mesma página") and EXPERIENCE.md → *Key Flows → Flow 3* ("**Nunca confirma o que não aconteceu**"); EXPERIENCE.md → *Voice and Tone* ("Recebemos seu pedido…").
- **WCAG:** 3.3.1 Error Identification A, 3.3.3 Error Suggestion AA, 4.1.3 Status Messages AA.
- **What fails:** The honeypot is the *only* anti-spam mechanism the spine relies on (FR-8: "Submissão automatizada (honeypot) não gera pedido"; no nonce, no CAPTCHA, no rate limit stated). Honeypot fields are tripped by real users in the wild — password managers, browser autofill, and screen-reader users who tab into anything focusable. The spine's prescribed response to a tripped honeypot is **silence**: no confirmation, no error, no announcement. For a human who trips it, that is a 3.3.1 failure (the error is identified nowhere), a 3.3.3 failure (no suggestion to recover), a 4.1.3 failure (no status message reaches assistive tech), and a direct contradiction of UJ-3's climax ("vê que o pedido foi aceite"), Flow 3's failure clause, and the voice-and-tone table. Lúcia would press Enviar and watch nothing happen.
- **Fix:** (a) Specify the honeypot's accessible construction: `aria-hidden="true"`, `tabindex="-1"`, `autocomplete="off"`, `type="text"` with an off-screen-not-`display:none` treatment only if it must be fillable, and **never** a `required` or `aria-describedby`-linked field — screen readers must not announce it and keyboard users must not be able to reach it. (b) Change the response contract: a tripped honeypot must **either** produce the normal error state ("Não foi possível enviar agora. Seus dados continuam aqui — tente de novo.", which is already written), **or** be gated on signals that no human generates (sub-1-second time-to-fill, no pointer events) before going silent. (c) State which of the two applies. "Silêncio" alone is not a defensible outcome against a real user, and the pair already has the correct copy for the alternative. (d) Since there is no nonce, also state that losing a submission never loses the typed data — "campos mantidos" is committed, make it explicit for the dropped case too.

### F-13 · **HIGH** — Error handling is described in prose, never in testable field semantics
- **Location:** EXPERIENCE.md → *Accessibility Floor* ("erro identificado por texto, não só por cor"; "Leitor de ecrã anuncia o resultado do envio de formulário"); EXPERIENCE.md → *State Patterns* → *Envio falhado*; PRD FR-8 ("Erro visível quando o envio falha, com possibilidade de repetir").
- **WCAG:** 3.3.1 Error Identification A, 3.3.2 Labels or Instructions A, 3.3.3 Error Suggestion AA, 4.1.3 Status Messages AA, 1.3.1 Info and Relationships A, 2.4.6 Headings and Labels AA.
- **What fails:** Three separate gaps.
  1. **"Erro identificado por texto"** says the error is textual, but not that it is *programmatically associated* with the field that failed, nor that `aria-invalid` is set, nor that focus moves to the first invalid field. A text paragraph that looks like an error and is not linked to its input is not an identified error under 3.3.1 for a screen-reader user.
  2. **"Leitor de ecrã anuncia o resultado do envio"** covers the *submission* outcome only. Nothing commits to announcing *field-level* errors, which is where 3.3.1/3.3.3 actually bite, and nothing commits to a live region for the success state beyond the one sentence.
  3. **Label association** is committed ("Todo o campo de formulário tem etiqueta associada"; "Campos com etiqueta associada") — that part is fine and is the one piece that is testable. But there is no commitment to **required-field indication** ("Preenche nome, correio e telefone" — are they all required?) and no instruction text, which 3.3.2 requires to be conveyed programmatically, not only visually.
- **Fix:** Turn the prose into an I/O contract in EXPERIENCE.md's *Formulário* row (or a small *Forms* subsection):
  - visible `<label for>` on every field (already committed) and no placeholder-only labels;
  - on failure: `aria-invalid="true"` + `aria-describedby` pointing at the field's own message; the message text states what is wrong **and** what to do;
  - an **error summary** at the top of the form after a failed submit, each item a link to its field, focus moved to the summary, `role="alert"`;
  - success and failure both announced via a persistent live region (`role="status"` / `aria-live="polite"`), never a transient toast;
  - required fields marked in the label text ("Nome (obrigatório)") *and* `required`/`aria-required="true"`;
  - `autocomplete="name"`, `autocomplete="email"`, `autocomplete="tel"` on the three fields (see F-25).
  Each of these is one testable line; together they close 3.3.1, 3.3.2, 3.3.3 and 4.1.3.

### F-14 · **HIGH** — No error or success component and no error colour token exists
- **Location:** DESIGN.md → front matter `colors` (navy, ink, body, muted, subtle, base, ocre, ocre-hover, terracota, chumbo, text-muted — **no error/danger/success**); DESIGN.md → *Components* (**no `field-error`, no `validation-summary`, no `confirmation`**).
- **WCAG:** 1.4.3 AA (error text must itself be readable), 1.4.1 A (not colour alone), 3.3.1 A.
- **What fails:** EXPERIENCE.md requires a visible error state and a visible confirmation state on both forms, and neither has a colour or a component. Whatever the implementer picks for error text — almost certainly a red — is unreviewed against a white card and against the `{colors.subtle}` band, and is at risk of carrying the error by colour (`1.4.1`) rather than by text + icon + association. The `input` component declares only `borderColor` and `focusBorderColor`: it has **no invalid state at all**, so a failing field is visually identical to a passing one.
- **Fix:** Add to DESIGN.md: `error` and `success` colour tokens (each verified ≥4.5:1 on `base` and on `subtle`), plus `input.invalidBorderColor`, `field-error` (colour, typography, spacing, and a non-colour marker such as a leading text glyph) and `validation-summary` components. State in the component note that the message text is the primary carrier and colour is reinforcement only.

### F-15 · **MEDIUM** — The `input` focus treatment contradicts the Accessibility Floor
- **Location:** DESIGN.md → *components.input* (`focusBorderColor: {colors.navy}`) vs EXPERIENCE.md → *Accessibility Floor* ("contorno terracota e afastamento de 2px"); DESIGN.md → *Do's* ("Nunca `outline: none` sem substituto").
- **WCAG:** 2.4.7 Focus Visible AA, 2.4.13 Focus Appearance AAA (if the 2px offset is meant to satisfy it), 1.4.11 AA.
- **What fails:** One spine says the focused input changes its **border colour to navy**; the other says every interactive element gets a **terracota outline at 2px offset**. These are different treatments on the same element with no precedence rule (the "este documento manda" clause covers mocks, not the design spine). A 1px border-colour swap is also the weakest possible indicator: it changes no area, adds no offset ring, and fails any focus-appearance reading.
- **Fix:** Pick one and delete the other. Preferred: the `input` gets the same dual-ring focus treatment as everything else (F-6), with `focusBorderColor: {colors.navy}` retained only as an additional reinforcement. State the precedence explicitly: "EXPERIENCE.md governs focus treatment; DESIGN.md's `focusBorderColor` is supplementary."

---

## 4. Focus visibility

### F-16 · **HIGH** — Fixed cookie bar + header can obscure the focused element
- **Location:** DESIGN.md → *Components* → *Barra de cookies* ("navy translúcido **no fundo do ecrã**"); EXPERIENCE.md → *Component Patterns* → *Cabeçalho* ("No telemóvel, a navegação colapsa e não empurra o conteúdo de forma permanente") and → *Accessibility Floor* ("Navegação por teclado percorre a página pela ordem de leitura").
- **WCAG:** 2.4.11 Focus Not Obscured (Minimum) AA — *new in WCAG 2.2*, 2.4.7 AA.
- **What fails:** The cookie bar is pinned to the bottom of the viewport and persists until a choice is made ("Nada de analítica ou marketing dispara antes de uma escolha"), so it is present for the entire risky window. A collapsed/collapsible header sits at the top. Together they can cover a focused control near either edge — typically pagination and footer links at the bottom on mobile, exactly the "paginação acessível com o polegar" surface from *Responsive & Platform*. Nothing in the pair commits to scroll padding, `scroll-margin`, or the bar yielding to focus.
- **Fix:** Add to *Accessibility Floor*: "Nenhum elemento focado fica coberto pelo cabeçalho colapsado ou pela barra de cookies." Implementation contract: `scroll-padding-top` = header height, `scroll-padding-bottom` = cookie bar height while the bar is present, `scroll-margin` on focusable elements, and the bar hides or shifts while a control behind it has focus. This is a one-line commitment that makes 2.4.11 testable at the review gate.

### F-17 · **HIGH** — No bypass mechanism (skip link)
- **Location:** EXPERIENCE.md → *Accessibility Floor* ("Navegação por teclado percorre a página pela ordem de leitura, **cabeçalho incluído**"); EXPERIENCE.md → *Component Patterns* → *Cabeçalho* (brand + navigation + two pills); DESIGN.md → *Components* → `button-pill` (used twice in the header).
- **WCAG:** 2.4.1 Bypass Blocks A, 1.3.1 A (landmarks), 2.4.6 AA.
- **What fails:** "Cabeçalho incluído" reads as an intentional statement that keyboard users traverse the header first on **every** page — brand mark, four first-level nav items, the `Associe-se` pill and the `Apoia-se` pill, then the mobile nav control. That is the classic bypass-blocks failure, stated as a feature. No skip link, no landmark contract, no heading outline is committed anywhere in the pair.
- **Fix:** Add to *Accessibility Floor*: a visible-on-focus "Pular para o conteúdo" link as the **first tab stop**, before the brand; a `main` landmark with `id`, and one `h1` per page; note that when the mobile navigation is expanded, the control closes it and focus returns. Amend "cabeçalho incluído" to "cabeçalho após a ligação de salto".

### F-18 · **MEDIUM** — Mobile navigation control has no announced state
- **Location:** EXPERIENCE.md → *Interaction Primitives* ("Navegação do telemóvel abre e fecha pelo controlo do cabeçalho; não sequestra o ecrã").
- **WCAG:** 4.1.2 Name, Role, Value AA, 2.4.6 AA.
- **What fails:** The disclosure control's state is described behaviourally ("abre e fecha") with no commitment to `aria-expanded`, a persistent accessible name that does not change with state, or `aria-controls`. A bare icon button with a toggling label is the standard WP failure mode here.
- **Fix:** Specify: `<button aria-expanded="true|false" aria-controls="…">Menu</button>` with the name staying "Menu" (or "Abrir menu"/"Fechar menu" consistently), plus Escape-to-close and focus return.

### F-19 · **MEDIUM** — Selected state of Tema filter and current page is only partly committed
- **Location:** EXPERIENCE.md → *Component Patterns* → *Filtro de Tema* ("Escolher um Tema muda a lista e o endereço") and → *Paginação* ("Página actual é texto, não ligação"); DESIGN.md → *components.tag* (`textColor: {colors.terracota}` only).
- **WCAG:** 1.4.1 A, 4.1.2 AA, 2.4.4 A.
- **What fails:** Pagination correctly removes the current page as a link — good — but nothing marks it programmatically (`aria-current="page"`). The Tema chips are `tag`-styled terracota text with no declared selected/unselected distinction, so if selection is expressed by colour it is a 1.4.1 risk; the pair also does not say whether the active Tema is a link or text. Card links are likewise unprotected: a card contains a tag/category, a title and a date, and nothing states the accessible-name rule (title as the link, category link distinct, date not a link).
- **Fix:** Commit `aria-current="page"` on the active pagination item and `aria-current="true"` (or a filled state with a border, not colour alone) on the selected Tema chip; add a card accessible-name rule ("o nome acessível do cartão é o título; a tag é uma ligação separada com nome próprio").

---

## 5. Mobile — the 44px target

### F-20 · **MEDIUM** — "44px" is asserted globally but not attached to any surface, and the small-type surfaces are exactly where it will be missed
- **Location:** EXPERIENCE.md → *Accessibility Floor* ("Alvo mínimo de toque de 44px"); EXPERIENCE.md → *Responsive & Platform* ("paginação acessível com o polegar"); DESIGN.md typography: `label` **11px**, `eyebrow` **12px**, `caption` **13px**; PRD FR-13 ("Botões e links cabem no polegar").
- **WCAG:** 2.5.8 Target Size (Minimum) AA — 24×24 CSS px (WCAG 2.2); 2.5.5 Target Size (Enhanced) AAA — 44×44. The spine's 44px claim *exceeds* the 2.2 AA floor, which is good, but the claim is not per-component, so nothing prevents the smaller surfaces from shipping at 24px or less.
- **What fails:** The components most likely to miss 44px are the ones whose typography is defined at 11–13px with no padding in the front matter:
  - **Paginação** ("Numerada") — page numbers are links set at label/caption scale; nothing declares their padding or minimum box.
  - **Filtro de Tema** chips — `tag`/`label` at 11px, with `rounded.full` and no declared height.
  - **Cartão de agenda** day/date line ("Dia, nome do encontro e lugar") — `Dia` is a `{typography.caption}` element at 13px, and if the whole row is the tap target the target is only as tall as one caption line (~18px). This is the single most likely failure, because it is on the primary mobile journey (UJ-2: "numa olhadela").
  - **Acordeão de Tema no item** — a disclosure summary with no declared height.
  - **Header `button-pill`** in versaletes at 11–12px.
  - The cookie bar's three actions at `{typography.caption}` (13px) — three controls crammed into a bottom bar at 375px is where 44px is genuinely hard.
- **Fix:** Replace the global sentence with a per-component target table in DESIGN.md giving each of the above a minimum **44×44px rendered box** (achieved with padding, not with font size) and a minimum 8px gap between adjacent targets; add the mobile acceptance test to *Responsive & Platform*: "a 375px, todo o alvo interactivo da Agenda, da Paginação, dos chips de Tema e da barra de cookies mede ≥44×44 CSS px." Targets that cannot be enlarged (e.g. an inline link inside a paragraph) should be named as explicit 2.5.8 exceptions rather than left silently undersized.

### F-21 · **MEDIUM** — Reflow is verified at 375px, not the 320px WCAG requires
- **Location:** EXPERIENCE.md → *Responsive & Platform* ("Telemóvel **a partir de 375px** de largura, sem scroll horizontal"); PRD FR-13 (same).
- **WCAG:** 1.4.10 Reflow AA (320 CSS px equivalent, i.e. 1280px at 400% zoom), 1.4.4 Resize Text AA (200%).
- **What fails:** 375px is the common phone width; 320px is what 1.4.10 actually tests (and what a 200%/400% zoom on desktop produces). The spine commits only to the easier number, so nothing catches the ocre band's address block, the three-column-to-one grid, or the cookie bar's three actions wrapping badly at 320px.
- **Fix:** Change the commitment to **320px** ("funciona a partir de 320px de largura, sem scroll horizontal — o mínimo do critério de reflow") and keep 375px as the design reference width. Add "sem scroll horizontal a 320px" to the review checklist for UJ-1/2/3.

---

## 6. Motion

### F-22 · **MEDIUM** — The reduced-motion commitment covers two transitions and no other animation
- **Location:** EXPERIENCE.md → *Accessibility Floor* ("Movimento reduzido: **transições de cartão e de foco** tornam-se instantâneas quando o sistema o pede"); DESIGN.md → *Elevation & Depth* ("Hover: sombra… com **transição curta**"); EXPERIENCE.md → *Interaction Primitives* (bans carousel/opening animation/auto-play video).
- **WCAG:** 2.2.2 Pause, Stop, Hide A (auto-updating/moving content), 2.3.1 Three Flashes A, 2.3.3 Animation from Interactions AAA. Reduced motion is not itself an AA criterion, but the spine presents it as an AA-floor commitment, so it must be complete to be credible.
- **What the spine does get right (credit):** banning auto-carousels, opening animation, article counters, subscribe pop-ups and video autoplay pre-empts 2.2.2 and 2.3.1 almost entirely. That is a genuine strength of the pair.
- **What fails:** The commitment names exactly two things — **card** transitions and **focus** transitions — and says "quando o sistema o pede" without naming the mechanism (`prefers-reduced-motion: reduce`) or the property set. Uncovered by that wording: the mobile header open/close animation (a *permanent* navigation control, the one users hit most), the `Acordeão de Tema` expand/collapse, the hover shadow transition ("transição curta"), hover/focus transforms, and `scroll-behavior: smooth` (common in themes, and a vestibular trigger). A litmus test: an implementer who satisfies the sentence literally still ships four animated behaviours.
- **Fix:** Rewrite as: "Com `@media (prefers-reduced-motion: reduce)`, **todas** as transições e animações — cartões, foco, hover, abertura da navegação do telemóvel, acordeão e `scroll-behavior` — ficam instantâneas (`transition-duration: 0.01ms`, `animation: none`, `scroll-behavior: auto`), e nenhum conteúdo fica dependente da animação para aparecer." Add `scroll-behavior: smooth` to the *Do's/Don'ts* as a banned default. That is one testable rule instead of one and a half.

### F-23 · **LOW** — Cookie bar timing is not constrained
- **Location:** EXPERIENCE.md → *State Patterns* → *Escolha de cookies pendente* ("Nada de analítica ou marketing dispara antes de uma escolha").
- **WCAG:** 2.2.1 Timing Adjustable A (if the bar self-dismisses), 2.2.2 A.
- **What fails:** Nothing forbids the bar auto-hiding after N seconds, which would drop the user into the "no choice" state with no way back and no re-entry point (see F-10).
- **Fix:** State: "A barra de cookies não desaparece sozinha; só sai com uma escolha, e a escolha pode ser revista depois."

---

## 7. Omissions against WCAG 2.2 AA (and criteria that are *satisfied by omission*)

### F-24 · **MEDIUM** — 3.2.6 Consistent Help (A, new in WCAG 2.2) is unaddressed
- **Location:** EXPERIENCE.md → *Information Architecture* lists `Fale connosco` (reached "Home, rodapé") and DESIGN.md → *Components* → *Faixa de contacto* (Home only). No footer contract exists anywhere in the pair.
- **WCAG:** 3.2.6 Consistent Help **A** — where a help mechanism (contact details, contact link, self-help) appears on multiple pages, it must appear in the **same relative order** each time.
- **What fails:** Help is a first-class part of this product — the whole promise of the site is that a human answers (`Associe-se`, `Fale connosco`, `Apoia-se` manual, "espera contacto humano"). The contact mechanism appears on Home (band + footer) and in the footer elsewhere, and the footer's contents, order and existence are never specified. A footer whose contact link moves, or that is dropped on some templates, breaches 3.2.6.
- **Fix:** Add a `footer` component/pattern with a **fixed order**: Fale connosco → e-mail → Política de privacidade (and cookie preferences), identical on every template, and note 3.2.6 as the reason. Also make the cookie-preference re-entry point live in that same fixed block.

### F-25 · **MEDIUM** — 1.3.5 Identify Input Purpose (AA) is not committed
- **Location:** EXPERIENCE.md → *Accessibility Floor* (label association only); EXPERIENCE.md → *Component Patterns* → *Formulário*.
- **WCAG:** 1.3.5 Identify Input Purpose AA — inputs collecting information about the user (name, e-mail, telephone are all named in the criterion) must expose their purpose programmatically.
- **What fails:** Name, e-mail and telephone are exactly the three fields 1.3.5 names, and the pair commits only to a visible label. No `autocomplete` tokens. This also degrades the mobile experience the whole product is built around (`autocomplete` is what makes a phone keyboard fill the field), so it is a mobile-first failure as much as a criteria failure.
- **Fix:** Add one line to the form contract: "Campos com `autocomplete` explícito: `name`, `email`, `tel`." Testable in one click.

### F-26 · **MEDIUM** — No page-title or page-language commitment
- **Location:** EXPERIENCE.md → *Information Architecture* (the surface table) — titles are the *UI names* of surfaces, not document titles; EXPERIENCE.md → *Voice and Tone* ("**Português do Brasil** na interface").
- **WCAG:** 2.4.2 Page Titled A — every page needs a descriptive title; 3.1.1 Language of Page A; 2.4.8 Location AA; 3.1.2 Language of Parts AA.
- **What fails:** The pair names every surface and its purpose, and never commits to `<title>`, to the heading outline, or to `lang`. Brazilian Portuguese is a notable risk here: the repository, the design spine and the PRD are all written in *European* Portuguese ("acções", "selector", "ecrã") while the *interface* is pt-BR — exactly the situation where `lang="pt-PT"` ships by accident and every screen reader pronounces the site in the wrong dialect. Mixed-language labels exist too ("Drops", "Editorial", "Notícias", "Acervo", "PIX").
- **Fix:** Add to *Accessibility Floor*: "`<html lang=\"pt-BR\">` em todas as páginas; cada página tem `<title>` descritivo e um `h1` único; termos estrangeiros visíveis marcados com `lang`." Verifiable in a two-line check.

### F-27 · **MEDIUM** — The hero image cannot carry a text alternative
- **Location:** DESIGN.md → *Don'ts* ("Não reintroduzir URL absoluto de ambiente na imagem do hero; vem de `assets/hero-bg.jpg`"); EXPERIENCE.md → *Accessibility Floor* ("Imagens com texto alternativo; imagem decorativa marcada como tal").
- **WCAG:** 1.1.1 Non-text Content A, 1.4.5 Images of Text AA.
- **What fails:** The alt-text rule is committed and that is good — but it applies to `<img>` in content, and the hero is specified as an **asset referenced as a background** (`assets/hero-bg.jpg`), i.e. a CSS background, which is invisible to assistive tech and cannot be given an `alt`. The PRD's §11 tone makes the hero meaningful ("Hero em substância: pesquisa, memória e cultura negra") and FR-12 forbids "cabeçalho esticado" — so the hero may well carry information that is then unreachable, or it may be pure decoration that should be explicitly marked as such. The pair also does not forbid text baked into images for cards/hero, which 1.4.5 restricts.
- **Fix:** State which it is: if decorative, "hero-bg.jpg é decorativo; nada de informação só na imagem"; if informative, it must be an `<img>` with `alt` or be accompanied by equivalent visible text. Add a general rule: no text baked into content images.

### F-28 · **LOW** — Card text is set at 11–13px in versaletes with wide tracking
- **Location:** DESIGN.md typography: `label` 11px/600 with `letterSpacing: 0.06em`, `eyebrow` 12px/600 with `letterSpacing: 0.2em`, `caption` 13px/400 — used for tags, category labels, dates and the button label.
- **WCAG:** 1.4.4 Resize Text AA (200% without loss), 1.4.12 Text Spacing AA, 1.4.3 AA (ratios computed for `terracota`/`text-muted` at these sizes).
- **What fails:** Not a hard failure, but 11px uppercase with 0.2em tracking is the least resizable, most fragile typography in the system, and the pair never commits to 200% zoom/reflow behaviour for it or to surviving the 1.4.12 text-spacing overrides against the fixed `lineHeight` values declared in the front matter (e.g. `eyebrow` lineHeight 1.4 at 0.2em tracking).
- **Fix:** Add to *Responsive & Platform*: "A 200% de zoom e com os ajustes de espaçamento de texto de 1.4.12, nada é cortado nem sobreposto; rótulos e versaletes continuam legíveis." Consider raising `label` from 11px to 12px.

### F-29 · **LOW** — No accessibility statement / conformance record
- **Location:** No mention in either spine; EXPERIENCE.md → *Information Architecture* lists only `Política de privacidade` as the compliance-adjacent page.
- **WCAG:** not a criterion itself (it is the deliverable of the AA claim, and a common public-sector expectation).
- **What fails:** The pair asserts AA for a public institutional site and commits to no page where that is stated and dated. The client gate (UJ-5, FR-17) has no artefact to approve.
- **Fix:** Add an "Acessibilidade" line to the privacy page, or a short institutional page, stating the standard targeted (WCAG 2.2 AA), the date of the last review, and a contact for barriers — which also reinforces 3.2.6.

### Criteria that this product does **not** need (verified N/A)

- **3.3.8 Accessible Authentication (Minimum) AA — satisfied by omission.** EXPERIENCE.md → *Foundation*: "**Sem contas.** Nenhuma superfície desta entrega pede autenticação"; PRD non-goals, and SM-C1 makes zero accounts a counter-metric. With no login and no cognitive-function test, 3.3.8 is not triggered. **Keep it that way**, and note that the *next phase* (Criadora/Aprovador/Administrador, FR-14–16) introduces authenticated surfaces, at which point 3.3.8 and 2.4.11 both come back into scope — the spine should flag that.
- **2.5.7 Dragging Movements AA — satisfied by omission.** EXPERIENCE.md → *Interaction Primitives*: "Tocar para agir. Sem gestos escondidos, **sem swipe para revelar acções**." No slider, drag-to-reorder or drag-to-filter exists. Nothing further required.
- **1.4.2 Audio Control A — not triggered.** No audio or video player is committed; auto-play is banned.
- **3.3.7 Redundant Entry A — met.** Each form asks for its data once, and *Envio falhado* keeps the fields filled ("campos preenchidos mantidos"), which is the retention 3.3.7 requires. Worth one explicit line so it survives refactoring.
- **2.2.2 / 2.3.1 — largely met** via the banned list (no carousel, no opening animation, no counters, no auto-play). See F-23 for the cookie-bar timing gap.
- **4.1.3 Status Messages — partly met by design:** "Filtros e páginas são ligações a sério" means filtering and pagination cause real navigation rather than silent DOM swaps, which removes the largest 4.1.3 risk on this site. Keep this rule; it is doing accessibility work the pair does not take credit for.

---

## Appendix A — Computed contrast table

All values computed from the declared hex values in DESIGN.md front matter (relative-luminance method, sRGB, WCAG 2.x formula).

| Foreground | Background | Ratio | Required | Verdict |
|---|---|---|---|---|
| `chumbo` #2d2418 | `ocre` #c9a86a | **6.75:1** | 4.5 (12px/700 text) | pass |
| `chumbo` | `ocre-hover` #e2b878 | **8.25:1** | 4.5 | pass |
| `base` #ffffff | `navy` #0d176b | **15.53:1** | 4.5 | pass |
| `base` | `ocre` | **2.26:1** | 4.5 | **fail — correctly banned by the spine** |
| `ink` #0f172a | `navy` | **1.15:1** | 4.5 / 3 | **FAIL — F-1 (cookie bar ghost)** |
| `ink` | `ocre` | **7.90:1** | 3 (ring) | pass |
| `ink` | `base` | **17.85:1** | 4.5 | pass |
| `body` #475569 | `base` | **7.58:1** | 4.5 | pass |
| `body` | `subtle` #f8fafc | **7.24:1** | 4.5 | pass |
| `body` | `ocre` | **3.35:1** | 4.5 | **fail — F-5** |
| `body` | `navy` | **2.05:1** | — | (measured to test 1.4.1, see link row) |
| `navy` (link) | `base` | **15.53:1** | 4.5 | pass |
| `navy` vs `body` (link vs neighbouring text) | — | **2.05:1** | 3:1 to skip non-colour cue | **fail — F-7, needs underline** |
| `terracota` #a85a32 | `base` | **5.03:1** | 4.5 (11–12px text) | pass |
| `terracota` | `subtle` | **4.81:1** | 4.5 | pass |
| `terracota` | `navy` | **3.09:1** | 3 (ring) | pass, marginal (F-6) |
| `terracota` | `ocre` | **2.23:1** | 3 (ring) / 4.5 (text) | **FAIL — F-6, F-5** |
| `terracota` | `ocre-hover` | **2.72:1** | 3 | **fail — F-6** |
| `text-muted` #64748b | `base` | **4.76:1** | 4.5 (13px) | pass |
| `text-muted` | `subtle` | **4.55:1** | 4.5 | pass by 0.05 — F-8 |
| `text-muted` | `ocre` | **2.11:1** | 4.5 | **fail — F-5** |
| `muted` #e2e8f0 | `base` | **1.23:1** | 3 (control boundary) | **FAIL — F-3 (inputs)** |
| `muted` | `subtle` | **1.18:1** | 3 | fail (card borders tolerate it; inputs do not) |

**Cookie bar at `navy` over `base` at various alphas** (illustrating F-2 — the ratio is a function of what scrolls behind the bar):

| α | Effective bg | `base` text | `ink` ghost | terracota ring |
|---|---|---|---|---|
| 1.00 | #0d176b | 15.53:1 | 1.15:1 | 3.09:1 |
| 0.90 | #252e7a | 12.00:1 | 1.49:1 | 2.38:1 |
| 0.80 | #3d4589 | 8.71:1 | 2.05:1 | 1.73:1 |
| 0.70 | #565d97 | 6.16:1 | 2.90:1 | 1.22:1 |
| 0.60 | #6e74a6 | 4.46:1 | 4.01:1 | 1.13:1 |

Over the ocre band at α=0.80: base text 11.39:1 but terracota ring **1.86:1**.

---

## Appendix B — Fix candidates, pre-computed

| Problem | Candidate | Ratio |
|---|---|---|
| `text-muted` too tight on subtle (F-8) | `#5a6b80` | 5.46:1 base · 5.21:1 subtle |
| Input border ≥3:1 (F-3) | `#64748b` (reuse `text-muted`) | 4.76:1 base |
| Input border with margin (F-3) | `#556274` | 6.20:1 base · 5.93:1 subtle |
| Focus ring, surface-independent (F-6) | dual: 2px `#ffffff` inner + 2px `#0d176b` outer | navy outer on base **15.53** · subtle **14.85** · ocre **6.87** · ocre-hover **8.40** · muted **12.60**; white inner handles navy surfaces where navy itself is 1.00:1 |
| Any control inside the ocre band (F-5) | `#0d176b` on `#c9a86a` | 6.87:1 |
| Cookie bar third action (F-1) | `#ffffff` text + 1px `#ffffff` underline/border on navy | 15.53:1 |

---

## Appendix C — What would make the AA claim falsifiable

The pair's accessibility content is currently three paragraphs of prose split across two documents. To survive the client gate, it needs to be checkable. Minimum additions, all small:

1. **DESIGN.md:** a contrast matrix (Appendix A) with required minimums per row; `error`/`success` tokens; `checkbox`/`switch`/`radio`, `link`, `focus-ring`, `field-error`, `validation-summary`, `footer` and full `button-pill` outline components; a per-component 44×44 target column; an explicit decorative-vs-informative rule for `hero-bg.jpg`; a documented floor for the cookie bar's opacity.
2. **EXPERIENCE.md:** a bypass-link and landmark commitment; a focus-not-obscured rule for the fixed bar and collapsed header; a form I/O contract (invalid association, error summary, live region, required marking, `autocomplete`); a honeypot construction rule and a non-silent outcome for human-tripped cases; a consistent-help footer order; `lang="pt-BR"` and page-title rules; reduced-motion rewritten to cover every animation and naming `prefers-reduced-motion`; the 320px reflow floor.
3. **One acceptance checklist** attached to UJ-5 (the contractor's review), because FR-17 gates production on a verdict and nothing in the pair gives the reviewer anything to test.

---

*Reviewer note:* the pair is unusually good on the things most spines get wrong — it bans the dark patterns (auto-carousel, autoplay, pop-ups, hidden gestures), it commits to real navigation over hidden state, it gets label association right, and its one explicit contrast rule ("não usar texto claro sobre ocre") is numerically correct. The failures are not in the intent; they are all failures to *state* — a token used on a surface it was never paired with, a claim with no number, a behaviour with no mechanism. Every finding above is closed by a sentence or a hex value, not a redesign.
