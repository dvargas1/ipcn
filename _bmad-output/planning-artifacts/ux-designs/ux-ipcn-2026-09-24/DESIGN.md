---
name: IPCN
description: Identidade visual do site público do Instituto de Pesquisas das Culturas Negras — instituto de pesquisa e memória negra, moderno e sóbrio.
status: final
sources:
  - {planning_artifacts}/prds/prd-ipcn-2026-09-24/prd.md
  - wp-content/themes/ipcn-fse/theme.json
  - wp-content/themes/ipcn-fse/style.css
created: 2026-09-24
updated: 2026-09-24
colors:
  navy: '#0d176b'
  ink: '#0f172a'
  body: '#475569'
  muted: '#e2e8f0'
  subtle: '#f8fafc'
  base: '#ffffff'
  ocre: '#c9a86a'
  ocre-hover: '#e2b878'
  terracota: '#a85a32'
  chumbo: '#2d2418'
  text-muted: '#64748b'
  error: '#b3261e'
  success: '#1b5e20'
typography:
  display:
    fontFamily: Oswald
    fontSize: clamp(32px, 6vw, 56px)
    fontWeight: '700'
    lineHeight: '1.08'
  headline:
    fontFamily: Oswald
    fontSize: clamp(26px, 3vw, 34px)
    fontWeight: '700'
    lineHeight: '1.15'
  card-title:
    fontFamily: Oswald
    fontSize: 19px
    fontWeight: '600'
    lineHeight: '1.35'
  wordmark:
    fontFamily: Oswald
    fontSize: 22px
    fontWeight: '700'
    letterSpacing: 0.02em
  reading:
    fontFamily: Playfair Display
    fontSize: 19px
    fontWeight: '400'
    lineHeight: '1.75'
  body:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-small:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.8'
  eyebrow:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1.4'
    letterSpacing: 0.2em
  label:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '600'
    lineHeight: '1.4'
    letterSpacing: 0.06em
  caption:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: '1.4'
  button:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    letterSpacing: 0.08em
rounded:
  sm: 6px
  md: 12px
  lg: 14px
  full: 9999px
spacing:
  margin-mobile: 20px
  section: 96px
  section-mobile: 48px
  hero: 120px
  hero-mobile: 72px
  footer: 56px
  blockGap: 1.5rem
  content: 720px
  wide: 1100px
components:
  header:
    backgroundColor: '{colors.base}'
    textColor: '{colors.navy}'
    controlRounded: '{rounded.full}'
    paddingInline: '{spacing.margin-mobile}'
  footer:
    backgroundColor: '{colors.navy}'
    textColor: '{colors.base}'
    labelColor: 'rgba(255,255,255,0.5)'
    bodyColor: 'rgba(255,255,255,0.85)'
    dividerColor: 'rgba(255,255,255,0.15)'
    typography: '{typography.caption}'
    labelTypography: '{typography.label}'
    paddingBlock: '{spacing.footer} 24px'
    contrastRatio: '4.74:1 (rótulos) · 11.4:1 (texto e ligações)'
  button-primary:
    backgroundColor: '{colors.ocre}'
    textColor: '{colors.chumbo}'
    typography: '{typography.button}'
    rounded: '{rounded.sm}'
    hoverBackgroundColor: '{colors.ocre-hover}'
    contrastRatio: '6.75:1'
  button-secondary:
    backgroundColor: '{colors.navy}'
    textColor: '{colors.base}'
    typography: '{typography.button}'
    rounded: '{rounded.sm}'
    contrastRatio: '15.6:1'
  button-pill:
    backgroundColor: '{colors.ocre}'
    textColor: '{colors.chumbo}'
    typography: '{typography.label}'
    rounded: '{rounded.full}'
    contrastRatio: '6.75:1'
  button-pill-outline:
    backgroundColor: 'transparent'
    textColor: '{colors.navy}'
    borderColor: '{colors.navy}'
    typography: '{typography.label}'
    rounded: '{rounded.full}'
    contrastRatio: '15.6:1'
  cookie-action:
    backgroundColor: '{colors.ocre}'
    textColor: '{colors.chumbo}'
    borderColor: '{colors.base}'
    typography: '{typography.label}'
    rounded: '{rounded.sm}'
    contrastRatio: '6.75:1'
  link:
    textColor: '{colors.navy}'
    textDecoration: underline
    contrastRatio: '15.6:1'
  card:
    backgroundColor: '{colors.base}'
    borderColor: '{colors.muted}'
    borderWidth: 1px
    rounded: '{rounded.md}'
    imageAspectRatio: '16/9'
  card-agenda:
    backgroundColor: '{colors.base}'
    borderColor: '{colors.muted}'
    borderWidth: 1px
    rounded: '{rounded.md}'
  card-feature:
    backgroundColor: '{colors.base}'
    borderColor: '{colors.muted}'
    borderWidth: 1px
    rounded: '{rounded.md}'
    imageAspectRatio: '4/3'
    titleTypography: '{typography.headline}'
  contact-band:
    backgroundColor: '{colors.ocre}'
    textColor: '{colors.chumbo}'
    contentWidth: '{spacing.content}'
    paddingBlock: '{spacing.section-mobile}'
    contrastRatio: '6.75:1'
  eyebrow:
    textColor: '{colors.terracota}'
    typography: '{typography.eyebrow}'
    contrastRatio: '5.03:1'
  tag:
    textColor: '{colors.terracota}'
    typography: '{typography.label}'
    contrastRatio: '5.03:1'
  theme-filter:
    textColor: '{colors.navy}'
    borderColor: '{colors.muted}'
    borderWidth: 1px
    rounded: '{rounded.full}'
    typography: '{typography.label}'
    selectedBackgroundColor: '{colors.navy}'
    selectedTextColor: '{colors.base}'
  pagination:
    textColor: '{colors.navy}'
    currentTextColor: '{colors.ink}'
    typography: '{typography.label}'
  accordion:
    textColor: '{colors.ink}'
    borderColor: '{colors.muted}'
    typography: '{typography.caption}'
  input:
    backgroundColor: '{colors.base}'
    borderColor: '{colors.text-muted}'
    textColor: '{colors.ink}'
    focusBorderColor: '{colors.navy}'
    rounded: '{rounded.sm}'
    contrastRatio: '4.76:1'
  error-text:
    textColor: '{colors.error}'
    typography: '{typography.caption}'
    contrastRatio: '5.94:1'
  success-text:
    textColor: '{colors.success}'
    typography: '{typography.caption}'
    contrastRatio: '6.9:1'
  cookie-bar:
    backgroundColor: '{colors.navy}'
    textColor: '{colors.base}'
    typography: '{typography.caption}'
    contrastRatio: '15.6:1'
  focus-ring:
    innerColor: '{colors.base}'
    innerWidth: 2px
    outerColor: '{colors.navy}'
    outerWidth: 2px
    offset: 2px
---

# IPCN — Design Spine

## Brand & Style

Instituto de pesquisa e memória, apresentado com o cuidado de uma casa de cultura — não como portal de notícias e não como revista. A postura é sóbria e institucional: muito branco, hierarquia clara, rótulos discretos. O peso visual vem da tipografia condensada e do navy, não de efeitos.

Referências de tom: Museu Afro Brasil (principal), Amistad Research Center, NYPL Events. Anti-referência: o site acumulado em construtor de página, com cabeçalho esticado, grelhas inconsistentes e logótipo a ocupar o ecrã do telemóvel.

O desenho não inventa uma identidade nova. Codifica o que já está decidido em `theme.json` e no `style.css`, com duas correcções de acessibilidade que o código actual não garante: a barra de cookies passa a opaca e o contorno de foco passa a anel duplo.

## Colors

Cada par que carrega texto mantém **4.5:1** (texto normal), **3:1** (texto grande e limites de controlo). Os pares abaixo estão verificados contra os hex declarados.

- **Navy (`{colors.navy}`)** — fundo do hero, links, botão secundário, barra de cookies, rodapé. Com texto base dá **15.6:1**.
- **Ocre (`{colors.ocre}`)** — fundo de botão primário e da faixa de contacto. O texto sobre ocre é sempre chumbo (`{colors.chumbo}`), **6.75:1**. Nunca claro.
- **Ink (`{colors.ink}`)** — texto principal, **18:1** sobre base.
- **Body (`{colors.body}`)** — texto corrido, **7.6:1** sobre base.
- **Base (`{colors.base}`)** — fundo de página, do cabeçalho e dos cartões.
- **Subtle (`{colors.subtle}`)** — fundo de secção alternado.
- **Muted (`{colors.muted}`)** — **só** bordas de cartão, onde dá **1.23:1** e é decorativo. **Nunca** em campos de formulário nem em texto.
- **Terracota (`{colors.terracota}`)** — acento de rótulo, tag e hover, **5.03:1** sobre base. Nunca fundo. Não é slug de `theme.json`.
- **Chumbo (`{colors.chumbo}`)** — o texto sobre ocre. Não é slug de `theme.json`.
- **Text-muted (`{colors.text-muted}`)** — data e metadado, **4.76:1** sobre base. É também a borda de campos, onde cumpre os 3:1 de limite de controlo.
- **Error (`{colors.error}`)** e **Success (`{colors.success}`)** — estados de formulário, **5.94:1** e **6.9:1** sobre base.

Terracota, chumbo, ocre-hover, text-muted, error, success e os valores de alfa do rodapé não são tokens de `theme.json`. Não os promover a slug sem decisão.

Navy e terracota são as únicas cores de link e de acento. Um link nunca depende só da cor: leva sublinhado.

## Typography

- **Oswald** (`{typography.display}`, `{typography.headline}`, `{typography.card-title}`, `{typography.wordmark}`) — títulos, wordmark e nomes de cartão. Carregada do Google por `functions.php`, nunca só declarada em `theme.json`.
- **Playfair Display** (`{typography.reading}`) — leitura longa de uma Notícia ou de um Item de Acervo.
- **Inter** (`{typography.body}`, `{typography.body-small}`, `{typography.eyebrow}`, `{typography.label}`, `{typography.caption}`, `{typography.button}`) — corpo, rótulos e controlos.

Rótulos e eyebrows usam maiúsculas com tracking largo. O texto de interface não desce abaixo de `{typography.caption}`; a excepção são os rótulos em `{typography.label}` **11px**, só em maiúsculas com tracking. O corpo assenta em `{typography.body}` a `1.6`; a leitura longa em Playfair a `1.75`.

## Layout & Spacing

Uma coluna, centrada. `{spacing.content}` para texto corrido e leitura; `{spacing.wide}` para grelhas de cartões.

Margem lateral de `{spacing.margin-mobile}` em todos os ecrãs. Secções ocupam `{spacing.hero-mobile}` a `{spacing.hero}` no hero e `{spacing.section-mobile}` a `{spacing.section}` nas restantes. O rodapé usa `{spacing.footer}`.

Grelhas de cartões: três colunas no computador, uma no telemóvel. Espaçamento entre blocos: `{spacing.blockGap}`.

Reflow a 320px sem scroll horizontal; 375px é a largura confortável de revisão.

O hero e as faixas da home recebem padding inline dos templates FSE, que vence media queries normais; o `style.css` ajusta-os por selector de atributo. Os templates não são a superfície desta espinha — quem muda o valor inline tem de actualizar esse selector.

## Elevation & Depth

- Cartões: fundo `{colors.base}`, borda `1px` em `{colors.muted}`, `{rounded.md}`.
- Repouso: sombra `0 6px 20px rgba(13,23,107,.08)`.
- Hover: sombra `0 12px 32px rgba(13,23,107,.14)`, com transição curta.
- Faixas de secção alternam `{colors.base}` e `{colors.subtle}` em vez de ganhar bordas.

Sem sombras coloridas, sem vidro, sem gradientes decorativos.

## Shapes

Cantos discretos: `{rounded.sm}` em botões de conteúdo e campos, `{rounded.md}` em cartões e imagens, `{rounded.full}` nos controlos do cabeçalho. O `theme.json` declara `{rounded.lg}` como raio por omissão.

Imagens seguem o raio do contentor. Nada de cantos mistos no mesmo cartão.

## Components

Especificação visual. O comportamento de cada um vive em `EXPERIENCE.md` → *Component Patterns*, sob o mesmo nome.

- **Cabeçalho** (`header`) — fundo base, marca em navy, controlos em pílula.
- **Rodapé** (`footer`) — navy, três colunas (Instituto · Contato · Redes sociais) com rótulos a 50% e texto a 85% de base, separador a 15%, e uma linha final com copyright e crédito. `{typography.caption}`.
- **Botão primário** (`button-primary`) — ocre com texto chumbo.
- **Botão secundário** (`button-secondary`) — navy com texto base.
- **Pílula** (`button-pill`, `button-pill-outline`) — ocre com texto chumbo, ou contorno navy a transparente. Versaletes.
- **Acção de cookies** (`cookie-action`) — ocre com texto chumbo e contorno base, sobre a barra navy. As três acções usam este tratamento.
- **Link** (`link`) — navy, sublinhado.
- **Cartão** (`card`) — imagem 16:9 recortada, tag de categoria em terracota, título em `{typography.card-title}`, data em `{typography.caption}` e `{colors.text-muted}`.
- **Cartão de agenda** (`card-agenda`) — o dia é o elemento de maior peso tipográfico.
- **Cartão de destaque** (`card-feature`) — vitrina do Acervo na Home: imagem 4:3, título em `{typography.headline}` e Tema visível. Aparece uma vez, ao lado de dois cartões compactos.
- **Faixa de contacto** (`contact-band`) — fundo ocre, texto chumbo, `{spacing.content}` de largura, centrada.
- **Eyebrow** (`eyebrow`) — versalete terracota, acima do título de secção.
- **Tag / termo** (`tag`) — categoria da notícia ou Tema do acervo, no acento terracota.
- **Filtro de Tema** (`theme-filter`) — chip em contorno; o Tema escolhido fica preenchido em navy.
- **Paginação** (`pagination`) — número actual em ink, restantes como links navy.
- **Acordeão** (`accordion`) — metadado histórico do Item, com contorno muted.
- **Campo de formulário** (`input`) — fundo base, borda `{colors.text-muted}` (**não** muted), foco navy.
- **Texto de erro** (`error-text`) e **de sucesso** (`success-text`) — a cor acompanha sempre uma palavra.
- **Barra de cookies** (`cookie-bar`) — navy **opaco**, texto base, acções em `cookie-action`.
- **Anel de foco** (`focus-ring`) — anel duplo de `{colors.base}` e `{colors.navy}`, com 2px de afastamento. Funciona sobre base, subtle, ocre, navy, terracota e rodapé.

## Do's and Don'ts

**Do**
- Verificar contraste antes de introduzir um par novo e declarar a razão no componente.
- Usar ocre como fundo com texto chumbo; usar terracota só como acento.
- Deixar o branco fazer a separação antes de acrescentar linhas.
- Dar 44px de alvo a todo o controlo: paginação, filtros, acções de cookies e cabeçalho do acordeão.
- Recortar imagens em 16:9 dentro do raio do cartão.
- Manter a barra de cookies opaca e o anel duplo de foco.
- Usar maiúsculas com tracking para rótulos, não `font-variant: small-caps`.

**Don't**
- Não usar texto claro sobre ocre.
- Não usar `{colors.muted}` em bordas de campos nem em texto — só em cartões.
- Não usar fundo translúcido na barra de cookies nem em nada que contenha controlos.
- Não depender só da cor para links ou estados.
- Não inventar slugs de `theme.json` para terracota, chumbo ou ocre-hover.
- Não reintroduzir URL absoluto de ambiente na imagem do hero; vem de `assets/hero-bg.jpg`.
- Não usar Playfair em títulos de interface nem Oswald em leitura longa.
- Não acrescentar tema ou plugin pago para cumprir o que os blocos nativos já fazem.
