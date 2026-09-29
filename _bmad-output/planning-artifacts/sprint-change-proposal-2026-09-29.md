# Proposta de Mudança de Sprint — um só cabeçalho de página

> Data: 2026-09-29 · Repositório `ipcn` · Base: `2274d7e` · Fluxo: `bmad-correct-course`
> Modo de trabalho: **Incremental** (recomendado). Os checkpoints de aprovar/editar/descartar foram resolvidos por conta própria e ficam aqui para contestação.

## 1. Resumo do problema

As cinco superfícies institucionais **`/quem-somos/`, `/projetos/`, `/editorial/`, `/associe-se/` e `/apoia-se/` servem dois `<h1>` com o mesmo texto**, empilhados: o do template e o que vive dentro do conteúdo da própria página. `/noticias/` tem um só e um cabeçalho desenhado, porque tem template próprio.

**Descoberta:** revisão de UX (`bmad-ux`, modo Update) a 2026-09-29, a partir do que a Contratante viu no ambiente de revisão ("dois títulos, um embaixo do outro").

**Evidência medida no ar** (HTML servido, `?nocache=1` depois da purga):

| URL | `<h1>` | De onde vêm |
|---|---|---|
| `/quem-somos/` | 2 — "Quem Somos", "Quem Somos" | `page.html` + hero no conteúdo |
| `/projetos/` | 2 — "Projetos", "Projetos" | idem |
| `/editorial/` | 2 — "Editorial", "Editorial" | idem |
| `/associe-se/` | 2 — "Associe-se", "Associe-se" | idem |
| `/apoia-se/` | 2 — "Apoia-se", "Apoie o IPCN" | idem |
| `/noticias/` | **1** — "Notícias do IPCN" | `page-noticias.html` |

Todas as cinco são **páginas** (`post_type=page`), servidas por `templates/page.html` — três delas têm também uma categoria de igual slug, mas a página vence a rota. O defeito é, portanto, **de uma só superfície: `page.html`**.

**Causa raiz, com rasto:** a história 1.10 ("Os estados vazios e a página 404") encontrou o `wp:post-title` do `page.html` **escondido** por `className:"ipcn-hero-alone"` e pela regra `.wp-block-post-title.ipcn-hero-alone{display:none}`. Subiu-o a `"level":1` e apagou a regra, para "dar `h1` às páginas institucionais". Passou a haver dois: o do template e o hero que o editor já tinha escrito no conteúdo. Antes disso a página tinha **exactamente um** `h1`. A premissa da spec — "as páginas institucionais não têm `h1`" — era falsa: foi lida dos templates, não das páginas servidas, e o AC de verificação ("as nove templates … um `h1` visível") é satisfeito por um template enquanto a página renderiza dois.

**O requisito existe em três sítios e não tem critério em nenhum:**

1. `docs/auditoria-redesign-fse-2026-09-17.md`, achado #8 — "`page.html` sem identidade visual — 7 linhas, sem hero, sem estrutura" (17/09, nunca virou história).
2. `review-accessibility.md`, achado **F-17 (HIGH)** — pede "um `h1` por página" na `Accessibility Floor`.
3. `spec-1-10-…md` — implementou-o, com a regra certa escrita ("subindo um cabeçalho já existente, nunca inventando um") e a leitura errada ("existente" = o título do template).

Nenhum `UX-DR` do `epics.md` nomeia `h1`, e `epics.md` não contém a palavra. Um achado HIGH de acessibilidade foi fechado sem AC.

## 2. Avaliação de impacto

### Épicos

- **Epic 1** (contém o gatilho) — continua realizável como planeado, mas **não está completo como se declarou**: `UX-DR4` não lista o componente de cabeçalho de página e `UX-DR20` omite a regra do `h1` único, apesar de o épico ter fechado com a verificação do F-17 aberta (`deferred-work.md`, "o `h1` das institucionais e do Acervo"). Alterações necessárias: emendar os dois UX-DR, acrescentar **um** UX-DR para o cabeçalho de página e **uma** história (1.13). Sem épico novo.
- **Epic 2** — âmbito intacto. Nota de dependência: as histórias **2.2/2.3** já são donas de reconstruir a agenda com o bloco `ipcn/agenda` e o cartão do pattern (AR9, AD-14); o remendo da agenda entregue hoje em `3fb670a` fica **substituído** por elas e não deve ser reimplementado sem ler esta nota.
- **Epic 3 / Epic 4** — sem impacto.

### Histórias

- **1.10** — implementou o requisito sem o ter em AC, e introduziu a duplicação. Não se reescreve história fechada; fica registado aqui e o AC que falta passa para a 1.13, agora escrito **por página renderizada**.
- Nova **1.13** — o único trabalho de tema + conteúdo que fecha isto.

### Conflitos de artefactos

| Artefacto | Conflito | Acção |
|---|---|---|
| **PRD** | nenhum. A `FR-12` já exige "WCAG 2.2 AA verificado"; o `h1` é consequência (WCAG 1.3.1, 2.4.6) | **Sem alteração.** Uma linha nas *Consequences* da `FR-12` é a única adição legítima, e é opcional |
| **Arquitectura** | nenhum. A solução **cumpre** o AD-1 ("o markup é escrito em `templates/*.html` ou `patterns/*.php`") e o AD-5 (CSS só em `style.css`, excepção `--ipcn-hero-bg`) | **Sem alteração.** O AD-13 já manda verificar no browser: a contagem de `h1` é dessa família |
| **UX** | resolvido nesta passagem | `DESIGN.md` ganhou `page-hero` e `typography.page-title`; `EXPERIENCE.md` ganhou o componente, a regra do `h1` único, o estado da faixa sem linha de apoio e o eyebrow na tabela de voz (`2274d7e`) |
| **Épicos** | dois UX-DR incompletos, história em falta | emenda abaixo |
| **Tracking** | `sprint-status.yaml` diz `epic-1: in-progress` com **1-7 a 1-12 em `backlog`**, e os commits dessas histórias estão feitos | reconciliar |
| **`deferred-work.md`** | duas entradas fecham-se aqui (a do `h1` das institucionais e a dos archives sem `h1` fica parcial) | reconciliar |
| **`AGENTS.md`** | o bloco gerido não conhece o template part novo, e a data do selo está velha | refresh do `bmad-project-context` |
| **Auditoria de 17/09** | achado #8 passa a fechado | histórico; anotar |
| **Conteúdo (BD)** | o hero vive **dentro do conteúdo** das páginas; sem o remover, o `h1` duplicado fica mesmo com o template corrigido | dentro do âmbito da 1.13 |

### Impacto técnico

- Tema: `parts/page-hero.html` (template part), `templates/page.html`, `templates/page-noticias.html`, `style.css` (generalizar `.ipcn-hero`, overlay ≥ `0.88`, eyebrow `base`).
- Contingente: `templates/archive.html` + `inc/listings.php` (`[ipcn_archive_hero]`) podem passar a consumir o mesmo part — é o que o AD-1 e a espinha de arquitectura já exigem ("remoção … dos shortcodes de markup … faz parte das histórias"). **Não faz parte do defeito**: esses archives renderizam um só `h1`. Fica como candidato, não como requisito desta mudança.
- Conteúdo: remover o bloco de hero do conteúdo de `/quem-somos/`, `/projetos/`, `/associe-se/`, `/apoia-se/` e `/editorial/`.
- Verificação: só no browser, depois da purga. Nenhuma verificação do repositório mede `h1`.

## 3. Caminhos avaliados

| Opção | Veredicto | Esforço | Risco |
|---|---|---|---|
| **1 — Ajuste directo** (emendar 2 UX-DR + 1 história) | **Viável — escolhida** | Médio | Baixo |
| 2 — Rollback (reverter o `level:1` da 1.10) | Não viável como solução: repõe um `h1` mas volta a **esconder** o título das institucionais, que é exactamente o que o achado #8 e o pedido da Contratante querem resolver | Baixo | Médio |
| 3 — Revisão do MVP | Não aplicável: nada do MVP está em causa e nada é diferido | — | — |

**Escolhida: Opção 1.** É a única que entrega o que foi pedido (um cabeçalho de página desenhado, uma vez por página), cumpre o AD-1 em vez de o contornar, e não reescreve história fechada. O rollback trataría o sintoma e perderia o desenho.

## 4. Alterações propostas

### `epics.md`

**UX-DR4 — acrescentar o componente**

```
OLD: … `success-text`, `header`, `footer`, `cookie-bar`, `focus-ring`.
NEW: … `success-text`, `header`, `page-hero`, `footer`, `cookie-bar`, `focus-ring`.
```

**UX-DR20 — acrescentar a regra ao piso**

```
OLD: UX-DR20: Piso de acessibilidade: skip link como primeiro elemento focável, `autocomplete` nos campos, … reflow a 320px.
NEW: UX-DR20: … reflow a 320px, e um só `h1` por página, verificado no HTML servido e não no template.
```

**UX-DR26 — novo (o cabeçalho de página)**

```
UX-DR26: Cabeçalho de página uniforme: as superfícies de entrada abrem com a `page-hero` — navy, `assets/hero-bg.jpg` por trás, eyebrow, título e linha de apoio quando existe — e é ela que carrega o `h1`; o conteúdo retoma no `h2` e nunca repete o título. As leituras (Notícia, Item de Acervo) mantêm o título editorial sobre base.
```

**Story 1.13 — nova, no fim do Epic 1**

```
### Story 1.13: Um só cabeçalho de página

Como Visitante,
quero que cada página abra com um só título, no mesmo tratamento,
para não ler o nome da mesma página duas vezes.

Acceptance Criteria:
Dado qualquer superfície de entrada — Notícias, Secção, Tema, Acervo, Agenda, as cinco Páginas institucionais e a 404 —
Quando a página é servida no ambiente de revisão, depois da purga,
Então o HTML servido tem exactamente um `h1`, e é o da `page-hero`.
E o hero que hoje vive dentro do conteúdo das páginas institucionais é removido, não substituído por outro.
E o eyebrow nomeia a superfície e a linha de apoio vem da descrição do termo quando existe.
E o eyebrow assenta num navy com alfa >= 0.88 e usa `base`: o ocre a 0.68 dá 2.57:1 e falha os 4.5:1 de `typography.eyebrow`.
E as leituras mantêm o título editorial sobre base, sem tarja.
E a contagem de `h1` é medida na página renderizada, não no template.
```

### `sprint-status.yaml`

```
1-7 … 1-12:  backlog → review   (implementadas e commitadas; a passagem no browser continua pendente)
+ 1-13-um-so-cabecalho-de-pagina: backlog
last_updated: 2026-09-29
```

### Sem alteração

`prd.md`, `ARCHITECTURE-SPINE.md`, `DESIGN.md` e `EXPERIENCE.md` (já emendados em `2274d7e`).

## 5. Handoff

**Âmbito: Moderado** — acrescenta uma história a um épico e emenda dois UX-DR; exige coordenação entre o plano e o desenvolvimento.

| Destinatário | Responsabilidade |
|---|---|
| `bmad-create-epics-and-stories` (PO) | aplicar as emendas aos UX-DR e a história 1.13, como acima |
| `bmad-build` (Dev) | implementar a 1.13 — template part, três templates, CSS e a limpeza do conteúdo |
| `bmad-sprint-planning` (status) | reconciliar o resto do `sprint-status.yaml` com os commits |
| `bmad-project-context` (refresh) | pôr o template part no bloco gerido e actualizar o selo |

**Critérios de sucesso:** o HTML servido de cada superfície de entrada, depois da purga, tem um só `h1`; `/noticias/` mantém a sua copy; as leituras ficam intocadas; `check-php.sh` não ganha problemas novos.

## Checklist (situação)

| Secção | Itens | Situação |
|---|---|---|
| 1 Gatilho e contexto | 1.1, 1.2, 1.3 | `[x]` — história 1.10, defeito de render, evidência medida |
| 2 Impacto em épicos | 2.1–2.5 | `[x]` — Epic 1 emendado; Epic 2 com nota de dependência; 3 e 4 limpos; ordem mantida |
| 3 Conflitos de artefactos | 3.1–3.4 | `[x]` — PRD e arquitectura sem alteração; UX já emendado; tracking, ledger e auditoria a reconciliar |
| 4 Caminho a seguir | 4.1–4.4 | `[x]` — Opção 1 |
| 5 Componentes da proposta | 5.1–5.5 | `[x]` |
| 6 Revisão e handoff | 6.1, 6.2, 6.5 | `[x]` |
| 6 Revisão e handoff | 6.3 aprovação explícita | `[!]` — apresentada; as decisões resolvidas por conta própria ficam para contestação |
| 6 Revisão e handoff | 6.4 actualizar o `sprint-status.yaml` | `[x]` — reconciliado |

## Achados que ficam fora desta mudança

- **`/editorial/` sombreia a categoria `editorial`** (6 posts): a Secção não tem superfície pública alcançável, porque a página vence a rota. O mesmo se passa com `associe-se` e `apoia-se`, que não são Secções. Merece decisão própria.
- **`[ipcn_archive_hero]`** continua a fabricar markup em PHP — contra o AD-1, que a própria espinha de arquitectura já manda remover "nas histórias". Os archives renderizam bem, por isso não é urgente.
- **Os 5 problemas do `check-php.sh`** vivem no mesmo hero do `listings.php` e fecham-se com o item anterior.
