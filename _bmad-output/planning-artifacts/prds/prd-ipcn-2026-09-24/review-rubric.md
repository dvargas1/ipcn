# PRD Quality Review — IPCN — Site público e operação editorial

## Overall verdict

This is a decision-ready PRD with a real thesis: the public institute site ships first, roles come next, and the 30/08 portal/paywall/six-role ladder is explicitly not inherited. Trade-offs, non-goals, counter-metrics and the addendum earn that cut. What is at risk is done-ness for the surfaces the Contratante will actually approve — Home content, which institutional pages exist, and what "aprova" means as a recorded verdict — plus a few consequences that an engineer cannot test as written (thumb-sized controls, "erro visível", "pedido objectivo").

## Decision-readiness — strong

A decision-maker can act on this. The thesis is stated as a decision, not a consideration: "Não herda o portal, o paywall nem a escada de seis cargos desse plano" (§0), and the addendum names what was given up (portal and paywall in the same wave; Criadora restricted to acervo; Divi "3 sections" home; centered header inherited as an FSE requirement). Pushback has somewhere to land. The 17/09 audit is demoted in one sentence — "A auditoria de 17/09 não é fonte: onde contradiz este PRD, este PRD manda" (§0) — and Risk 4 says where that audit is wrong (treating the redesign header as a deviation from the Divi rule). Open Questions 1–5 are actually open; none is answered in the next sentence. The one `[NOTE FOR PM]` in scope is a real tension, not a checkpoint: "A contratante pode querer ver 'quem publica' cedo. Isso não puxa o portal" (§6.2).

The remaining softness is who records the production gate, and that is already OQ3 rather than a buried hedge. §13 still lets architecture and stories proceed on the review environment without that verdict ("Sem o veredicto da Contratante, arquitectura e histórias podem avançar no ambiente de revisão. Produção, não."), which is the right split.

### Findings

- **medium** O veredicto de produção não tem forma (§8 OQ3, §13, FR-17) — FR-17 exige "aprovação registada" e UJ-5 resolve com "aprova, ou devolve uma lista do que falta", mas quem assina e em que artefacto fica aberto. Sem isso, "sem aprovação, produção não muda" é uma regra sem prova. *Fix:* fechar OQ3 antes do gate: nome da pessoa e o artefacto (e-mail, checklist assinado) que conta como aprovação registada. Não bloqueia desenho no ambiente de revisão.

## Substance over theater — strong

Five jobs, not a persona gallery, and four of them move a requirement: Visitante (FR-1–FR-7), Candidata (FR-8), Criadora/Aprovador/Administrador (FR-14–FR-16), Contratante (FR-17, UJ-5). "Quem apoia" is the thin one — it only needs a page, and FR-10 carries it without a journey, which is proportionate. The differentiation is earned by the rejected-alternatives table, not by a novelty claim. NFRs have product bounds: no analytics before a choice (FR-11), WCAG 2.2 AA including ocre with dark text (FR-12), 375px and no horizontal scroll on UJ-1–UJ-3 (FR-13), no production pipeline (FR-18). The Vision could not be swapped into another institute site: hPanel, Daniel, the unlicensed third-party theme, and "Associar-se é um pedido. Apoiar é um gesto público, manual."

### Findings

- **low** "Botões e links cabem no polegar" (FR-13) — the only NFR consequence that is an adjective sitting next to a real bound (375px, no horizontal scroll). *Fix:* drop it, or name a minimum target size. Do not let it become the acceptance test for mobile.

## Strategic coherence — strong

The bet is one sentence in §1: a public research-and-memory site, approved by the Contratante, before any role split; portal, closed archive and payment are a later product. Prioritization follows that bet. MVP is FR-1–FR-13 plus the production gate (FR-17, FR-18); the role split is "a continuação nomeada", not a second product and not "what's easy first". Success metrics test the thesis: SM-1 is a Contratante verdict on reading, navigation and identity; SM-2 is UJ-1 and UJ-2 completed without an account. Counter-metrics are doing work — SM-C1 (accounts created in the first delivery must stay zero) pushes back on "já meter o login"; SM-C2 (third-party plugins or themes added to imitate the old site) pushes back on buying approval with patches. Scope kind is problem-solving (replace the accumulated WordPress surface) with an experience constraint (mobile-first, no account), and §6 matches that.

No findings that add information.

## Done-ness clarity — adequate

Most first-delivery FRs have a verifiable consequence an engineer can fail: no auth on those surfaces (FR-1), archive showcase does not repeat news (FR-2), no 403 and no "últimos N" (FR-4), date ≥ today and chronological order (FR-6), honeypot does not create a request (FR-8), no card or recurrence button (FR-10), analytics does not fire before a choice (FR-11), no repo pipeline to production (FR-18). The phase-2 FRs are equally testable on the access-control axis (direct publish fails; visitor sees only the published version; an Administrador who does not publish news is possible).

The gap is the surfaces the Contratante will walk in UJ-5. FR-2 specifies a Home structure and then pins the canonical one to an assumption that is also Open Question 1, so "done" for the first screen is currently two designs. Institutional pages are a menu destination (FR-5) whose membership is Open Question 2 — Editorial and Drops appear there and are also "fora do primeiro nível" in §12. Several consequences name a signal without a condition: "Erro visível" (FR-8, FR-9), "pedido objectivo" (FR-15), "cabem no polegar" (FR-13). Story creation will invent those.

### Findings

- **high** A Home canónica está em dois sítios ao mesmo tempo (§4.1 FR-2, §8 OQ1) — FR-2's testable consequences (no stretched header, showcase ≠ news) are real, but the content of the first screen is an assumption the PRD itself flags as blocking UX: "a Home canónica é a que já está no ar no redesign… e não o catálogo de nove secções do sketch Museu Afro." UJ-5 asks the Contratante to approve that Home. *Fix:* either promote the redesign-on-air as the decision (OQ1 becomes a confirmation, not a fork) or hold FR-2's content list as provisional and say which blocks are invariant either way. Do not leave both as current.

- **high** Páginas institucionais não têm conjunto fechado (§8 OQ2, §12, FR-5) — FR-5 says the visitor reaches "as Páginas institucionais" from the menu; OQ2 asks which ones, "pelo nome (Quem somos, Projetos, Editorial, Drops, Fale connosco, Política de privacidade)". §12 then puts Editorial and Drops outside the first level "até decisão em contrário". An engineer cannot know if Editorial is in the menu, in the archive, or both. *Fix:* ship a named minimum (Quem somos, Contacto, Política de privacidade) and mark the rest `[NON-GOAL for MVP]` until OQ2 closes.

- **medium** "Erro visível" e "pedido objectivo" não são condições (FR-8, FR-9, FR-15) — "Erro visível quando o envio falha, com possibilidade de repetir" does not say what the visitor can do next or what must not happen to the data. "Devolver inclui um pedido objectivo, visível a quem submeteu" will be implemented as a comment box unless the PRD says the return is blocked without a written note, and that the note is visible to the Criadora on that item. *Fix:* one sentence each — failed send keeps the fields and does not claim the message arrived; devolver requires a non-empty note stored with the decision record.

- **low** "Botões e links cabem no polegar" (FR-13) — see Substance. Same phrase, same fix.

## Scope honesty — strong

Omissions are the point of the document, not an inference. §5 names portal, closed archive, "últimos 10", fees, app, Divi redesign, migrating old news into the archive, roles by núcleo, and a finding-aid product. §6.2 de-scopes FR-14–FR-16 in public rather than by silence, and says reopening portal/paywall/payment needs "decisão nova". Assumptions are tagged inline and indexed. Open-item density is low for a public launch with a client gate: 5 open questions, 10 assumptions, 2 `[NOTE FOR PM]`. That is not a green-light blocker. The unclassified assumption is the only honesty miss.

### Findings

- **medium** A assunção de custo não está no índice (§14, §9) — "não acrescentar tema ou plugin pago para cumprir um FR que o site público já sabe cumprir sem isso. `[ASSUMPTION]`" is the only assumption with no section pin and no Assumptions Index row. It is also the assumption that will be tested the first time a form or a grid is easier to buy. *Fix:* give it an owner and an index line, or promote it to a constraint and drop the tag. Name what "já sabe cumprir" refers to (native blocks, not a new paid plugin).

- **low** Fale connosco é requisito sem jornada nem menção no MVP narrativo (FR-9, §6.1) — it is in scope by the FR-1–FR-13 sweep, but §6.1's prose list ("site público completo… Associe-se por mensagem, Apoia-se manual") never says so, and no UJ carries it. A reader skimming scope can drop it. *Fix:* one clause in §6.1. Not a de-scope.

## Downstream usability — adequate

Glossary terms are used as defined across FRs and UJs: Notícia is never an Item, Tema never classifies news, Associe-se is a message, Apoia-se is not checkout, Produção is a URL plus a person. IDs are contiguous (FR-1–FR-18, UJ-1–UJ-5, SM-1–SM-4, SM-C1–SM-C2) and the cross-references that exist resolve (SM → FR, UJ → the feature that claims it). Every UJ has a named protagonist and inline context. Sections mostly stand alone; the dependency on `addendum.md` is declared in §0 and the addendum itself says it is not requirements.

Two extraction hazards will tax story creation. "Candidata a associada" (§2.1) is never a glossary noun, while FR-8 switches to "A Candidata". And the first-delivery information architecture is not closed: Destaques, Diáspora, Colunistas, Notas, Editorial and Drops are archive-if-they-have-content (FR-5 assumption), out of the first level (§12), and two of them are also candidates for institutional pages (OQ2). A UX workflow cannot source-extract a sitemap from this without guessing. This PRD is chain-top (UX, architecture, stories — §0), so that gap counts.

### Findings

- **medium** "Candidata" não é termo de glossário (§2.1, FR-8, Glossário) — the job is "Candidata a associada"; the requirement says "A Candidata pode enviar nome, e-mail e telefone"; the glossary only has Visitante. Story extraction will either invent a role or fold her into Visitante and lose the confirmation requirement. *Fix:* one glossary line — Candidata is a Visitante who submits Associe-se; she does not become a user.

- **medium** O mapa do primeiro nível não fecha (§12, FR-5, OQ2) — "Primeiro nível, nesta vaga: Home, Notícias, Acervo, Agenda, Associe-se, Apoia-se, mais as Páginas institucionais que a Open Question 2 fechar." The archive list and the institutional-page list overlap on Editorial and Drops. UX cannot pull a single IA. *Fix:* same as the done-ness finding — a named minimum, everything else marked out until OQ2.

- **low** UJ-5 não diz quem carrega o veredicto (UJ-5, §13) — "a contratante" is a role, and §13 / OQ3 admit the person is unnamed. Acceptable until OQ3 closes; do not let stories invent a sign-off workflow in the meantime.

## Shape fit — strong

Agreed stakes, from the workspace memlog, are a public launch with a client gate, not a hobby and not an internal tool. The shape matches: UJs with named protagonists are load-bearing for a multi-stakeholder public site (visitor, candidate, client reviewer), and the phase-2 role split correctly does not get a fake consumer journey beyond UJ-4. Brownfield citations that this review checked are accurate. The 30/08 plan does lock six roles (`não associado` → `administrador`), a publicador restricted to the new archive, "últimos 10" plus a 403, and manual hPanel deploy; the addendum's disposition table matches. The 31/08 design note does name Museu Afro Brasil, Amistad and NYPL Events. The audit-as-non-source rule is a brownfield decision, stated, not implied. Regulatory traceability is present at the level this wave collects (LGPD on cookies and minimum form data, WCAG 2.2 AA) and the addendum correctly refuses to pretend a future member file has a legal basis.

No findings that add information. The open Home and page-set questions are shape-correct (they are product decisions, not missing template sections); they are scored under done-ness.

## Mechanical notes

- Assumptions Index roundtrip fails once. Ten indexed assumptions match ten inline tags in FR-2, FR-4, FR-5, FR-7, FR-8, FR-10, FR-12, FR-14, FR-15, FR-16. The eleventh, §14 "Custo", is inline and unindexed. No index row lacks an inline tag.
- ID continuity is clean: FR-1–FR-18, UJ-1–UJ-5, SM-1–SM-4, SM-C1–SM-C2, no gaps or duplicates. Feature → UJ claims resolve. SM → FR claims resolve.
- Glossary drift is small. "Candidata" is used and undefined (see Downstream). "Páginas institucionais" (FR-1, FR-5, §12) vs glossary "Página institucional" is plural only. "fase seguinte" is consistently the role phase; "fase posterior" is consistently portal/payment. Portuguese (pt-PT narration, pt-BR content voice) is a declared choice in §11, not drift.
- UJ protagonists are named and carry context inline, except the Contratante in UJ-5, whose name is an open question rather than an omission.
- Required sections for these stakes are present: vision, jobs, non-users, journeys, glossary, FRs with consequences, non-goals, MVP in/out, metrics with counters, open questions, assumptions index, platform, tone, IA, stakeholders, constraints, compliance, risks. Addendum is separated and labeled non-requirements.
- Working title is still marked unconfirmed ("*Título de trabalho. Confirmar na revisão.*"). Fine at draft; do not let it ship into UX as the product name without a pass.
