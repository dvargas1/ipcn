# PRD Quality Review — IPCN — Site público e operação editorial

*Second review. Supersedes `review-rubric.md` where the two differ.*

## Overall verdict

Both high findings from the first review are genuinely closed: the Home is now one canonical five-block structure (FR-2), the institutional pages and the Sections are named (Glossary, §12), search is a non-goal (§5, §12), the Agenda has its own surface (FR-6), and "Candidata" is a defined noun. What is now at risk is the production gate: the decision to leave the Contratante's verdict unrecorded appears nowhere in the PRD, FR-17 still asks for "aprovação registada" and OQ1 still asks "em que forma fica registado?", and §16 does not name what the unrecorded gate costs. Second order, a handful of consequences are still adjectives an engineer cannot fail. The document is decision-ready on everything except the one thing the client gate turns on.

## Decision-readiness — adequate

The thesis and its trade-offs are intact and stronger than in the first draft. The cut is stated as a decision, not a consideration — "Não herda o portal, o paywall nem a escada de seis cargos desse plano" (§0) — and the addendum names what was given up (portal/paywall in the same wave; Criadora restricted to acervo; Divi "3 sections" home; centered header as an FSE requirement). `[NOTE FOR PM]` sits on a real tension ("A contratante pode querer ver 'quem publica' cedo. Isso não puxa o portal", §6.2). The two Open Questions that survived are open, except one.

The softness is the production gate, and it is now worse than soft — it is self-contradictory. The user decided the verdict has **no formal record**, but the PRD never says so: §13 still reads "Contratante: veredicto antes da produção (UJ-5, FR-17)", and OQ1 still asks "Quem, na contratante, dá o veredicto de UJ-5, **e em que forma fica registado?**" — the second half of that question is the half the decision answered, so OQ1 is no longer open. This is exactly the rubric's red flag: a decision that has been taken is presented as an open question. A decision-maker reading §8 and §13 will assume an artifact exists and will look for it.

### Findings

- **high** A decisão "sem registo formal" não está no PRD e OQ1 pergunta o contrário (§8 OQ1, §13, FR-17) — Nothing in §8, §13 or §4.6 states that the verdict is informal; §13's line repeats the old assumption of a verdict to be produced. *Fix:* state the decision where the gate lives ("O veredicto é dado oralmente/por mensagem; não há artefacto formal") and cut OQ1 to the person alone: "Quem, na contratante, dá o veredicto de UJ-5?"
- **high** O custo de um veredicto sem prova não aparece nos Riscos (§16, FR-15) — §16 lists five risks (approval bought with the old visual, phase-2-as-portal, expired QR, discovery without search, the audit re-entering) and none is the gate that was just loosened. The PRD is happy to keep an audit trail for editorial decisions — FR-15: "A decisão fica registada (quem, quando, versão)" — but accepts the same class of decision at production level with no record, and never names what that gives up. *Fix:* add a risk — produção pode mudar por memória; se a contratante negar o go-ahead mais tarde não há prova de quem o deu; mitigação: quem publica deixa uma nota datada (e-mail, mensagem, linha no memlog), sem criar um fluxo formal.

## Substance over theater — strong

Seven entries under §2.1 is more than the rubric's four, but none is furniture: Visitante drives FR-1–FR-7, Candidata drives FR-8, "Quem apoia" drives FR-10 without needing a journey, Criadora/Aprovador/Administrador drive FR-14–FR-16, Contratante drives FR-17 and UJ-5. The phase-2 roles get no fake consumer journey. NFRs carry product bounds rather than boilerplate: no analytics before a choice (FR-11), WCAG 2.2 AA including ocre-with-dark-text failing (FR-12), 320px and no horizontal scroll on UJ-1–UJ-3 (FR-13), no repo pipeline to production (FR-18). The Vision could not be pasted into another institute site — hPanel, Daniel, the unlicensed theme, "Associar-se é um pedido. Apoiar é um gesto público, manual." The Innovation/differentiation claim is earned by the rejected-alternatives list in the addendum, not asserted.

No findings that add information.

## Strategic coherence — strong

The bet is one sentence in §1: the public research-and-memory site ships and is approved before any role split; portal, closed archive and payment are a later product. Prioritisation follows the bet — MVP is FR-1–FR-13 plus the production gate (FR-17, FR-18), and the role split is "a continuação nomeada", not a second product. Success metrics test the thesis rather than activity: SM-1 is a Contratante verdict on reading, navigation and identity; SM-2 is a visitor completing UJ-1 and UJ-2 without an account. Counter-metrics push back on the obvious drift — SM-C1 ("Deve permanecer zero" accounts) and SM-C2 (plugins or themes added to imitate the old site). Scope kind is problem-solving with an experience constraint, and §6 matches it.

No findings that add information.

## Done-ness clarity — adequate

The first delivery is now mostly fail-able. FR-1 (no auth), FR-2 (Home is five blocks, in order; Notícias section filters by slug `noticias`, never `term_id`; showcase ≠ news grid; missing image keeps card height), FR-4 (no "últimos N", no 403, Tema filter changes the address), FR-6 (date ≥ today, chronological, missing place does not get invented), FR-8 (failed send keeps fields, error summary at top, "Nunca confirma o que não aconteceu"), FR-10 (no card or recurrence button; stale QR says so), FR-11 (no analytics before a choice), FR-18 (no pipeline from this repo) are all conditions an engineer can fail. The phase-2 FRs are testable on the access axis.

Two gaps remain. The first is FR-17, the gate the client approval story hangs on: its body is a constraint, not a requirement, and its consequences test the environment and a record that no longer exists, so there is nothing to observe. The second is a family of adjective consequences that will be invented by story creation: the first screen ("o instituto percebe-se sem depender de imagem carregada", FR-2), reading ("O texto é legível numa coluna, no telemóvel", FR-3), the empty Tema ("Um Tema sem itens explica-se", FR-4), the empty Agenda ("um estado vazio digno", FR-7), the cookie bar ("têm igual proeminência", FR-11) and SM-1 ("sem lista aberta de bloqueios de leitura, navegação ou identidade").

### Findings

- **high** FR-17 não tem consequência testável (§4.6 FR-17) — "O site público não substitui a produção sem um veredicto da Contratante" is followed by "Há um ambiente de revisão distinto da produção" (tests the environment, not the gate) and "Sem aprovação registada, a produção não é alterada por este trabalho" (tests a record the decision just removed — with no artifact there is nothing to check). "Por este trabalho" is also loose: FR-18 says the site does change production, by hand. As written the gate is an honour system with no falsifiable consequence. *Fix:* either admit it as a process constraint in §14 instead of an FR, or write the observable — production changes only after the go-ahead is given to whoever publishes (Daniel), and nothing else changes it.
- **medium** Consequências que são adjectivos, não condições (§4.1 FR-2, FR-3, FR-4; §4.2 FR-7; §4.4 FR-11; §7 SM-1) — "percebe-se", "legível numa coluna", "explica-se", "digno", "igual proeminência", "lista aberta de bloqueios" are the acceptance tests an engineer or reviewer would have to invent. *Fix:* name a bound for each — a font size and line length for FR-3, the required copy plus a link for FR-4/FR-7 empty states, the three cookie actions' rendering for FR-11 — or delete them and let the remaining consequences carry the FR.

## Scope honesty — strong

Omissions are the document's point, not an inference: §5 names portal, closed archive, "últimos 10", fees, app, Divi redesign, migrating legacy news into the Acervo, roles by núcleo, and a finding-aid product; §6.2 de-scopes FR-14–FR-16 in public and says reopening portal/paywall/payment needs "decisão nova". Assumptions are tagged inline and indexed, and the roundtrip now closes. Open-item density is low for a client-gated launch: 3 Open Questions, 7 assumptions, 2 `[NOTE FOR PM]` — not a green-light blocker.

The one honesty miss is the unrecorded gate: a decision that removes a safeguard was taken and neither stated in the decisions nor priced in §16. It is scored under Decision-readiness rather than counted twice here.

No findings that add information.

## Downstream usability — strong

The extraction surface is now much cleaner than in the first draft. Glossary nouns are used as defined across FRs and UJs (Notícia is never an Item, Tema never classifies news, Associe-se is a message, Apoia-se is not checkout, Produção is a URL plus a person). IDs are contiguous and the cross-references resolve. Sections stand alone, and the dependency on `addendum.md` is declared in §0. The IA closes: §12 gives a first level (Home, Notícias, Acervo, Agenda + the `Associe-se`/`Apoia-se` pills), a named Section set, a named institutional-page set, and "Sem busca" — and it agrees with `EXPERIENCE.md` (lines 45–59), which is the pair the PRD hands to UX. Each UJ has a named protagonist with inline context.

Three prose drifts remain, none of which breaks the sitemap.

### Findings

- **low** "Fale conosco" tem duas casas (§4.3 FR-9; Glossário; §12) — the Glossary and §12 classify it as a Página institucional, but FR-9 sits under §4.3 "Associe-se e Apoia-se", whose Description covers only associating and supporting ("Associar-se é pedido. Apoiar é informação."). A story extractor gets two homes for one requirement. *Fix:* move FR-9 under §4.1 or name it in §4.3's Description.
- **low** FR-5 sugere que as Páginas institucionais se alcançam a partir de Notícias (§4.1 FR-5 vs §12) — "As Secções editoriais e as Páginas institucionais ... alcançam-se a partir de Notícias e do rodapé" binds both surfaces to both entry points; §12 and `EXPERIENCE.md` put institutional pages in the rodapé (Fale conosco also from the Home), and only Sections inside Notícias. *Fix:* split the sentence — Sections from Notícias, institutional pages from the rodapé.
- **low** A Description de §4.1 inventaria uma Home que não é a de FR-2 (§4.1 Description vs FR-2) — "mais convites a Apoiar e a Associar-se" against the closed five blocks (hero, Notícias, Acervo, Agenda, faixa de contacto). Apoia-se is a header pill (FR-5, §12), not a sixth Home block. *Fix:* align the Description with FR-2, or say the two pills live in the header.
- **low** "Daniel" nunca se torna um papel (Glossário "Produção", §13, FR-18) — the only named individual in a document where every other actor is a role, and he is embedded inside the Produção entry and §13 rather than defined. *Fix:* one glossary line — operador de produção / quem publica no hPanel.

## Shape fit — strong

Agreed stakes (workspace memlog) are a public launch with a client gate — not a hobby, not an internal tool — and the shape matches. UJs with named protagonists are load-bearing for a multi-stakeholder public site; the phase-2 role split correctly gets no fake consumer journey beyond UJ-4; single-operator concerns (manual hPanel deploy) are handled as constraints, not as journeys. Brownfield references remain accurate: the 30/08 plan does lock six roles, a publicador restricted to the archive, "últimos 10" and a 403; the addendum's disposition table matches; the 31/08 tone references are real; the audit-as-non-source rule is stated, not implied. Regulatory traceability is present at the level this wave collects (LGPD on cookies and minimum form data, WCAG 2.2 AA) and the addendum refuses to pretend a future member file has a legal basis. Chain-top pressure (UX → architecture → stories, §0) is met by the now-closed IA.

No findings that add information.

## Mechanical notes

- **Assumptions Index roundtrip now passes.** Seven inline tags (FR-7, FR-8, FR-10, FR-12, FR-14, FR-15, FR-16) match seven index rows; no index row lacks an inline tag. The previous §14 "Custo" `[ASSUMPTION]` is now an untagged constraint — the fix the first review asked for, done correctly.
- **ID continuity is clean.** FR-1–FR-18, UJ-1–UJ-5, SM-1–SM-4, SM-C1–SM-C2: no gaps or duplicates; SM → FR and UJ → FR claims resolve.
- **Glossary drift is small.** "Página institucional" / "Páginas institucionais" is plural-only. "fase seguinte" (roles) vs "fase posterior" (portal/payment) is used consistently. "Candidata" is now a defined noun — the first review's gap is closed. "Fale conosco" is spelled consistently in pt-BR.
- **Leftover from the previous version:** FR-17's "Sem aprovação **registada**" and OQ1's "e em que forma fica **registado**?" both assume a record the new decision removed. These are the only sentences that did not survive the update intact (see Decision-readiness and Done-ness).
- **Review-1 highs verified closed.** Home: one canonical five-block structure in FR-2 and §12, agreeing with `EXPERIENCE.md` 47–53 (hero, Notícias, Acervo, Agenda, faixa de contacto; title without years; "Uma pauta de Agenda aqui é um defeito"). Institutional pages: closed set of four (Glossary, §12). Sections: seven, named, own addresses, none in the first level. Search: non-goal in §5, §12 and Risk 4. Agenda: own surface in FR-6. "Candidata a associada" is gone from §2.1.
- **Also closed from review 1:** the mobile bound is now consistent at 320px across FR-13, §10 and §15 (was 375px in one place); "Erro visível" and "pedido objectivo" are now testable ("Nunca confirma o que não aconteceu", FR-8; "Devolver exige uma nota escrita; sem nota, a devolução não acontece", FR-15); "Fale conosco" now appears in the §6.1 scope list.
- **Open-item count is effectively 2, not 3.** OQ1's second clause is answered by the "sem registo formal" decision, so it is a leftover rather than an open question.
- **Working title unconfirmed** ("*Título de trabalho.*", line under the H1) and the front-matter `updated:` was not bumped at this revision. Harmless at draft; do not let the working title into UX or stories unconfirmed.
