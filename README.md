# IPCN Brasil — Redesign do site

Repositório do redesign do site do **Instituto de Pesquisas das Culturas Negras** (`ipcnbrasil.org`), WordPress na Hostinger. Duas linhas de trabalho:

| Linha | Ambiente | Tema | Estado |
|---|---|---|---|
| **staging (Divi)** | `staging.ipcnbrasil.org` | Divi nulled 4.20.2 | Consolidado (Fase 4), entregue pra cliente |
| **redesign (FSE)** ⭕ foco | `stagingredesign.ipcnbrasil.org` | `ipcn-fse` (block theme própria) | Home v2 no ar, refinamentos de r=r |

> Documento principal do plano: [docs/plano-tema-custom-e-portal-associados.md](./docs/plano-tema-custom-e-portal-associados.md) · Status operacional: [STATUS.md](./STATUS.md) · roadmap por fase no [ROADMAP.md](./ROADMAP.md)

---

## Changelog / Status Atual (v2 — Home redesign)

### 1. Lógica de dados (queries sem duplicação)

A home (`templates/front-page.html`) usa **fonts de dados distintos por seção**:

- **Últimas Notícias** — `wp:query` com `postType:"post"` + `taxQuery.category:[1]` (term_id real de `noticias` no DB do redesign; **não é o ID do staging Divi**) → 3 posts mais recentes.
- **Vozes do IPCN** (acervo) — `wp:query` com **`postType:"acervo_ipcn"`, `taxonomy:"tema_acervo"`** (CPT + taxonomia registradas em `functions.php`). Zero chance de sobrepor a notícias, pois são post types diferentes.
- **Agenda (Próximos encontros)** — shortcode `ipcn_home_agenda` (`functions.php`): query dupla — `meta_query data_evento >= hoje` (respeitando metas que a cliente vai preencher) com **fallback `date_query after:hoje`**. Sem eventos futuros = estado vazio elegante (card "A agenda está sendo montada" + CTA Ver no Instagram) validado no browser.

Espera-se que o editor de posts use `data_evento` como meta key de evento (documentado no código).

### 2. Responsividade mobile-first

- **Hero**: `padding: 72px` mobile → **120px** desktop (`@media min-width 782px`), verificado via computed style no browser real (375px/1440px).
- **Seções**: 48px/40px mobile → 96px/64px desktop.
- **Solução do inline-style do WP**: templates FSE imprimem `style="padding-top:96px..."` inline que vence media queries comuns. O `style.css` usa **seletor de atributo** (`[style*="padding-top:96px"]`) com `!important` deliberado + comentário explicando que é seletivo só ao front-page. Se um dia essa lista de padding mudar no template, adicionar o valor novo na regra.

### 3. Acessibilidade (WCAG AA)

Contraste **real** (calculado com luminância WCAG):

| Combinação | Ratio | Status |
|---|---|---|
| Ocre `#c9a86a` + branco (antes, FAIL) | 2.26:1 | FAIL |
| **Ocre + chumbo `#2d2418`** | **6.75:1** | PASS AA |
| Ocre hover `#e2b878` + chumbo | 8.25:1 | PASS AA |
| Terracota `#a85a32` + branco | 5.03:1 | PASS AA |
| Terracota eyebrow/cards sobre claro | 4.79:1 | PASS AA |

Regime estético travado: **ocre é fundo de botão primário com texto chumbo escuro**, nunca texto claro | Terracota é a acento de eyebrow/tag/hover/link hover. `focus-visible` terracota com outline-offset 2px em CTAs e botões de cookie.

### 4. Arquitetura de cookies (sem CSS hack)

- **Banner legado do CookieYes DESLIGADO na opção nativa do plugin**: option `CookieLawInfo-0.9` → `is_on=false` + `showagain_tab=false`. Confirmado no HTML renderizado: `div#cookie-law-info-bar` nem é emitido pelo servidor.
- **Barra própria** `#ipcn-cookie-bar` (bottom-bar fixa, navy com `backdrop-filter`): texto curto + `Aceitar Todos` (ocre sólido) + `Gerenciar Preferências` (outline) + `Só os necessários` (ghost).
- **Painel** `#ipcn-cookie-panel` com toggles reais (Necessários sempre-on, Analytics, Marketing) mostrado apenas ao clicar em Gerenciar; técnicos mantidos pelo plugin.
- **Persistência**: `localStorage['ipcn_cookie_consent_v1'] = {ts, necessary, analytics, marketing, all}`; banner só reaparece se consent não existe.
- Fallback defensivo no CSS: se alguém religar o banner no hPanel, o CSS mantém o modal deles com max-height e esconde o header duplicado. Essa regra **não é a solução**, apenas proteção.

---

## Estrutura do repo

- `wp-content/themes/ipcn-fse/` — o tema `ipcn-fse`: `theme.json` (paleta navy/ocre/terracota/ink/muted + Oswald/Playfair/Inter), `templates/*.html`, `parts/header.html` + `footer.html`, `functions.php` (fonts, forms nativos, Home v2 shortcode agenda, cookie bar, CPT acervo).
- `wp-content/mu-plugins/` — plugins obrigatórios: `ipcn-optimizations.php` (ETmodules/form-style), `ipcn-mail-from.php` (From contato@ipcnbrasil.org — sem ele o Gmail rejeita os e-mails de form).
- `docs/` — diagnósticos, planos, gate docs.
- Deploy do tema: `tar -czf` + `scp` no server → `tar -xzf --strip-components=3` no tema do redesign + `litespeed-purge all` (HCDN é teimoso). Commit + **push sempre**.

## Ambiente

- SSH alias `ipcn` (~/.ssh/config) no Hostinger, staging DB `u654777386_DbbDB` (usuário `HsB7C`).
- Staging: https://stagingredesign.ipcnbrasil.org · staging Divi em https://staging.ipcnbrasil.org (congelado).
- Produção: https://ipcnbrasil.org — deploy **manual pelo Daniel** no hPanel (nunca via agent).
