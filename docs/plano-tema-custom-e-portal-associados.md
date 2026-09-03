# Plano: Tema Custom do Zero + Portal de Associados — IPCN

> Documento de decisões travadas em 30/08/2026 via grill (8 perguntas). Objetivo: só codar no futuro, sem reabrir premissas. Deploy produção continua manual via hPanel pela contratante.

## 1. Decisões travadas (fonte da verdade)

### Q1 — Tema
**Custom Block Theme (FSE) do zero**, não base free com child. Páginas são simples (header centralizado + menu 9 itens, footer 2 colunas, home 3 sections, grids `rttpg` paginados 9). `theme.json` com `palette #0d176b`, `fontFamily Oswald/Open Sans`, `parts/header.html` e `parts/footer.html` (logo já limitada 180px desktop / 140px mobile). Motivo: você versiona em git e mantém, zero vendor lock-in, sem remendo `ETmodules`.

### Q2/Q3/Q8 — Hierarquia de cargos (6 níveis)
Ordem fixa: `não associado` → `associado` → `publicador` → `coordenador` → `diretoria` → `administrador`.

* `não associado`: quem se cadastra vira isso automaticamente (pendente). Vê só últimos posts se logado, ou nada se deslogado.
* `associado`: liberado manualmente por `diretoria` ou `administrador` após conferência do cadastro. Ganha acesso ao acervo completo.
* `publicador`: cria conteúdo mas **não publica direto** — vai pra `pending` e precisa aprovação de `coordenador` ou `diretoria`. Escopo inicial: só no novo acervo IPCN (não em Notícias/Agenda antigas).
* `coordenador`: pode publicar em tudo, aprova `publicador`. Nesta fase é **geral**, sem vínculo por núcleo/área. "Locais específicos" ficam para v2 com taxonomia `nucleo`.
* `diretoria`: presidência do IPCN para baixo. Vê dados sensíveis de todos os associados, aprova `não associado → associado`, nomeia `publicador` e `coordenador`.
* `administrador`: só você (SRE). Acesso técnico total (gerir cargos, tema, plugins, Hostinger). Diretoria não mexe em `administrator`.

Implementação: `wp_users` + `user_meta` + plugin `Members` ou `User Role Editor` para capabilities. Sem tabela paralela.

### Q4 — Dados de cadastro (LGPD enxuta)
Campos v1: Nome completo, Email, Telefone/WhatsApp, CPF (opcional), Núcleo de interesse (select livre), Como quer contribuir (textarea). **Visível só para `diretoria` e `administrador`**, nunca entre associados. Armazenado em `user_meta`. Sem CPF obrigatório pra evitar dor LGPD no v1.

### Q5 — Acervo IPCN (novo, do zero)
O que existe hoje (Notícias 84, Agenda 54, Destaques 46, Notas 15, etc.) é legado e "velho". O **acervo de pesquisa** que o associado vai acessar é um **CPT novo `acervo_ipcn`** (ou `pesquisa_ipcn`) criado do zero, com taxonomia `tema_acervo`. Não reaproveitar posts antigos como acervo.

Regra de acesso v1 (manual, sem paywall complexo):
* Fluxo: preenche `/associados/cadastro` → cria `user` com `role = não associado` (status pendente) → `diretoria/administrador` ativa manualmente no WP Admin → vira `associado` → email "bem-vindo".
* Sem automação no v1. `wp_mail` via `hSendmail` (`/usr/sbin/hsendmail -t`) já validado (`bool(true)` pro `contato@ipcnbrasil.org`) serve pro aviso.
* Quem é `associado` ou superior vê **acervo completo**. Quem é `não associado` logado vê só **últimos 10 itens** (ou últimos 30 dias) com CTA "Torne-se associado para ver o acervo completo" ao tentar abrir item restrito (via `template_redirect` → 403 + bloco CTA). Deslogado vê CTA pra `/associados/cadastro`.

### Q6 — Fluxo editorial
`publicador` cria `acervo_ipcn` como `pending`. `coordenador` ou `diretoria` revisa e publica. `coordenador` pode publicar direto em qualquer categoria/CPT. `publicador` restrito ao acervo no v1. Cliente ainda sem definição fina — manter simples e evoluir em v2.

## 2. Arquitetura do tema custom

* **Stack:** Block Theme FSE puro (`style.css`, `theme.json`, `templates/*.html`, `parts/*.html`, `patterns/`), sem Divi, sem page builder. CSS vanilla + 1 `style.css` do tema.
* **Templates:** `front-page.html` (hero navy `#0d176b` + 3 sections), `page.html`, `single.html`, `single-acervo_ipcn.html`, `archive-acervo_ipcn.html`, `404.html`.
* **Reuso:** `The Post Grid` continua (shortcode `[the-post-grid id="..."]` funciona sem Divi). Grids já fixados: `5350 Destaques`, `5351 Diáspora`, `5352 Colunistas`, `5353 Notas`, `5338 Editorial`, `5340 Notícias`, `5342 Agenda`.
* **Forms:** `Associe-se` e `Fale Conosco` recriados como bloco nativo ou `Fluent Forms free` → `recipient contato@ipcnbrasil.org`, com `success_message` já testado via POST com nonce.
* **Performance/Segurança:** manter `mu-plugin ipcn-optimizations.php` (já com `et_builder` cache off, `xmlrpc` off, `hideWPVersion`, `ETmodules` fix, `ipcn-form-style`, `ipcn-footer-logo-fix`). Ao migrar, remover o fix `ETmodules` (vira nativo).

## 3. Rotas e capabilities (v1)

* `/associados/cadastro` — form público → cria `não associado`
* `/associados/entrar` — login custom (wp_login_form com redirect)
* `/associados/painel` — dashboard por role (associado vê lista do acervo; diretoria vê lista de pendentes + dados)
* `/associados/acervo` — archive do CPT, com `pre_get_posts` filtrando por role
* Capabilities: `read_acervo_completo` (associado+), `edit_acervo` (publicador cria pending), `publish_acervo` (coordenador/diretoria), `list_users`/`edit_users` restrito (diretoria vê, admin gerencia).

## 4. O que fica fora do v1 (propositalmente)

* Áreas por núcleo/local escopado (coordenador geral por enquanto)
* Paywall automático por tempo/quantidade além dos 10 últimos (fica manual)
* Integração de pagamento recorrente (PIX Apoia-se continua separado em `/apoia-se`)
* Migração do conteúdo legado para o novo acervo

## 5. Checklist futuro (quando for codar)

* [ ] Scaffold Block Theme em `staging-migra` (não no `staging` atual que está com Fase 4 consolidada)
* [ ] Criar roles com `Members` e testar `map_meta_cap`
* [ ] Registrar CPT `acervo_ipcn` + `tema_acervo` + `pre_get_posts` por role
* [ ] Tela de aprovação `não associado → associado` + email
* [ ] Fluxo `publicador → pending → publicado`
* [ ] Teste mobile (logo 140px), `Litespeed purge all` + `?nocache` + `x-hcdn-cache-status`
* [ ] LGPD: página de consentimento no cadastro + `politica-de-privacidade`

## 6. Direção de design — reestrutura (clean com identidade, alternativa A)

> Atualizado em 31/08/2026 — cliente confirmou: site atual muito antigo/feio, quer **clean mas com identidade** (alternativa A). Referências aprovadas: **Museu Afro Brasil** (principal), **Amistad Research Center** e **NYPL Events**. Ambiente futuro: `stagingredesign` (a ser criado no hPanel, separado do `staging` da Fase 4).

### 6.1 Diagnóstico do feio atual (o que dói)

* Header com imagem 1920×700 esticada (`transform scale 1.12`) + menu centralizado embaixo sem hierarquia — pesa no mobile, sem respiro.
* Grids `The Post Grid` sem card consistente (borda/sombra/hover diferentes por página), tipografia `Open Sans` sem escala, navy `#0d176b` usado só em botão.
* Footer com logo `ipcn-sem-fundo.png` 472×787 sem constraint (vira 600px de altura no celular — já corrigido para 180/140px via `ipcn-footer-logo-fix`, mas no tema novo vira nativo).
* Páginas internas (`Quem Somos` 39KB/8 sections) com `background_size: contain` e `min_height:493px` — HTML verboso herdado do Divi (`et_pb_*`).

### 6.2 Referências — o que roubar

* **Museu Afro Brasil (museuafrobrasil.org.br):** branco dominante, respiro grande, hero tipográfico (título serif grande + foto recortada, não esticada), cards de exposição com borda 1px + metadata discreta, navegação por tipo (Longa duração / Temporária) — traduz para IPCN como **nav por Eixo (Destaques/Diáspora/Colunistas/Notas)** + acervo em grid limpo. Paleta: branco + preto + detalhe dourado/ocre — daria para trazer um **ocre #c9a86a** como acento ao lado do navy.
* **Amistad (amistadresearchcenter.org):** narrativa "Where Heritage Meets Vision", blocos alternados imagem-texto com muito whitespace, archive como protagonista (Amistad traz `American Missionary Association archives` com peso). Para IPCN: **página /acervo como landing protagonista**, não subpágina escondida; cards com data + tag de núcleo.
* **NYPL Events (nypl.org/events):** sistema de filtros visíveis (tipo, data, local) + cards com imagem 16:9 + badge. Para IPCN: filtros do `rttpg`/archive por `tema_acervo` e por data, sem esconder paginação.

### 6.3 Princípios travados (para o tema custom)

1. **Clean institucional, não editorial vibrante** — alternativa A confirmada.
2. **Tipografia como identidade:** `Oswald` para títulos (já em uso, manter), `Inter` ou `Open Sans` para corpo com escala 16/18/24/32/48 — nada de 12 variações.
3. **Paleta:** base `branco #fff` + `navy #0d176b` (primária) + `ink #0f172a` (texto) + `muted #e2e8f0` (borda) + acento `ocre #c9a86a` opcional para hover/badges (referência Museu Afro).
4. **Cards:** branco, `radius 14px`, `shadow 0 6px 20px rgba(13,23,107,.08)`, `border 1px #e2e8f0`, hover `translateY(-2px)` + sombra maior — mesmo padrão já validado no `Associe-se`.
5. **Header novo:** logo 160px + menu 9 itens em linha única (sem foto gigante), sticky leve com `backdrop-blur` no FSE.
6. **Hero tipográfico:** sem imagem esticada — título 48px + subtítulo 18px `#475569` + CTA navy (padrão hero do `Associe-se` já aprovado `80px` padding).
7. **Mobile-first (375px base):** logo 140px, grid 1 coluna, header com hambúrguer `☰` (checkbox hack sem JS) que desdobra 9 itens em lista vertical com `padding 12px` e `border-bottom subtle`; hero `80px → 48px` no mobile, `font 18→16px`, botões `100% width` empilhados; cards `1 coluna + gap 16px + padding 16px`; filtros em drawer (v2). Tudo desenhado mobile-first, validado 375/768/1100 — desktop só expande colunas (4→2→1).

### 6.4 Estrutura de páginas (mapa para `stagingredesign`)

* `/` — hero tipográfico + 3 blocos (Destaques 4 posts, Acervo em destaque 6, Agenda 3) + CTA Apoia-se/Associe-se.
* `/acervo` (CPT) — archive protagonista com filtros (tema, ano), paywall: `associado` vê tudo, `não associado` logado vê últimos 10 + CTA, deslogado vê CTA para `/associados/cadastro`.
* Páginas legado (`/destaques`, `/diaspora`, etc.) — manter como redirects ou índices leves para o novo `tema_acervo`; conteúdo velho não migra para o acervo.
* `/associados/*` — conforme seção 3 (cadastro/entrar/painel).

### 6.5 Tokens para `theme.json` (pronto pra codar)

```json
{
  "settings": {
    "color": {
      "palette": [
        {"slug": "navy", "color": "#0d176b", "name": "Navy IPCN"},
        {"slug": "ink", "color": "#0f172a", "name": "Ink"},
        {"slug": "muted", "color": "#e2e8f0", "name": "Muted"},
        {"slug": "ocre", "color": "#c9a86a", "name": "Ocre Afro"},
        {"slug": "base", "color": "#ffffff", "name": "Base"}
      ]
    },
    "typography": {
      "fontFamilies": [
        {"fontFamily": "\"Oswald\", sans-serif", "slug": "oswald", "name": "Oswald"},
        {"fontFamily": "\"Inter\", \"Open Sans\", sans-serif", "slug": "body", "name": "Body"}
      ]
    },
    "spacing": {"units": ["px", "rem"], "blockGap": "1.5rem"}
  }
}
```

> Quando `stagingredesign` estiver no ar, subo um `sketch` (2 variações: 1) Museu Afro puro branco/ocre, 2) Amistad com bloco alternado) para você escolher antes de codar.

## 7. Plano exaustivo por tasks — com gates de validação e pivôs

> Ambiente: tudo no `stagingredesign` (Hostinger, subdomínio a criar no hPanel). `staging` da Fase 4 fica congelado como backup. Deploy prod continua manual via hPanel por você.

### Como ler

* **GATE:** momento em que você precisa ver e dizer "segue" ou "pivota". Sem gate não avanço.
* **PIVÔ:** alternativa se você não curtir o gate — o que muda sem jogar fora.
* **DON:** critério de pronto (provável com `curl ?nocache + x-hcdn-cache-status: MISS` e teste mobile 375px).

---

### FASE 0 — Fundação (sem risco)

**T0.1 — Clonar produção para stagingredesign**
Trabalho: duplicar `domains/ipcnbrasil.org/public_html` → `domains/ipcnbrasil.org/public_html/stagingredesign` via hPanel, criar subdomínio `stagingredesign.ipcnbrasil.org`, apontar DB separado, copiar `mu-plugin`.
DON: `stagingredesign.ipcnbrasil.org/?t=xxx` responde com `x-litespeed-cache: miss` e admin loga.
PIVÔ: se clonagem ficar pesada, fazer fresh install + importar `wp_posts` via `wp export/import`.

**T0.2 — Repo e CI do tema**
Trabalho: `themes/ipcn-fse/` no git (`/home/ubuntu/ipcn` já é repo), `main` protegido, `theme.json` vazio commitado.
DON: `git log --oneline -1` mostra `feat(fse): scaffold vazio`.
GATE 0: você confirma que `stagingredesign` está no ar (me manda URL). Sem isso não codamos.

### FASE 1 — Design System (tokens antes de pixel)

**T1.1 — theme.json + style.css base**
Trabalho: paleta `navy/ink/muted/ocre/base` da §6.5, fonts `Oswald` + `Inter`, `blockGap 1.5rem`, `radius 14px`, sombra `0 6px 20px rgba(13,23,107,.08)`.
DON: `wp-admin → Aparência → Editor` mostra cores e fontes.

**T1.2 — Sketch 1: Museu Afro puro (branco + ocre)**
Trabalho: `sketch` HTML estático (sem WP) com home tipográfica + cards 3 colunas + footer 180px — 2 breakpoints (1440/375).
DON: arquivo `docs/sketches/museu-afro.html` abre no navegador, LCP <1.5s local.
**GATE 1A:** você no celular (375px) aprova ou pede ajuste.
PIVÔ 1A: se achar frio demais, pivota para Amistad sem refazer tokens — só troca `ocre` por `terracota #a85a32` e adiciona blocos alternados.

**T1.3 — Sketch 2: Amistad narrativo (blocos alternados imagem-texto)**
Trabalho: mesmo conteúdo do T1.2 mas com seção "Herança → Visão" e cards com metadata.
DON: `docs/sketches/amistad.html`.
**GATE 1B:** você escolhe **um** dos dois sketches. O não escolhido vira `archive-acervo` alternativo (não é lixo).
PIVÔ 1B: se nenhum agradar, pivota para híbrido (hero Museu + blocos Amistad) — custo +1 dia, sem refazer CPT.

### FASE 2 — Shell do tema (header/footer/templates vazios) — ✅ FECHADA 03/09/2026

**GATE 2 e GATE 3 assinados pelo Daniel em 03/09/2026 ("MUITO bom, absurdamente melhor").**

O que fechou a fase (commits `4d7c3b2`, `524a405` + fix de fontes):
* `functions.php` agora enfileira o `style.css` (antes vazio = nenhum fix visual carregava) e as fontes Oswald+Inter via Google Fonts (antes só declaradas no theme.json, nunca baixadas — site inteiro no fallback sans-serif).
* Header novo: logo 56px + wordmark "IPCN" ao lado (padrão sketch aprovado), menu à direita, overlay mobile estilizado.
* Footer novo: fundo navy com logo RETRO-AZUL 48px (resolve "preto sobre azul escuro" — o logo `ipcn-sem-fundo.png` é preto e sumia no fundo ink).
* Root padding 20px (`useRootPaddingAwareAlignments`) + `alignfull` no hero/footer — conteúdo nunca cola na borda no mobile.
* Páginas internas refeitas em blocos nativos (T6.1 antecipada): Quem Somos (texto real extraído do Divi), Projetos, Editorial, Notícias, Agenda, Drops, Associe-se, Apoia-se (PIX), Fale Conosco — shell `ipcn_page_shell` (hero navy kicker + título) + shortcode `[ipcn_query_posts category="..." per_page="N"]` com cards no padrão da home e paginação `/page/N/`.
* Forms nativos sem Divi: Associe-se e Fale Conosco (`admin_post` + honeypot, sem nonce por causa do LiteSpeed) — testados com wp_mail real pro `contato@ipcnbrasil.org` (log confirma).
* CTA da home `/acervo` (404) → `/noticias` (provisório até a Fase 3 criar o acervo de verdade).
* `custom_logo` segue 573 (preto) no header sobre fundo branco — visível. Footer usa attachment 4459 (RETRO-AZUL).
* **Lição registrada (pitfall FSE):** block theme não carrega `style.css` sozinho no frontend; sem `wp_enqueue_style` no functions.php, todo CSS do tema é morto. Foi isso que fez a pausa de 01/09 parecer insolúvel ("logo gigante mesmo com 28px").

**Pendente visual menor:** logo do footer (4459) é vertical 1200x1600 — se o Daniel achar estranho no celular, substituir por asset horizontal/SVG (cliente ia fornecer).

### FASE 3 — Acervo (CPT novo, do zero)

**T3.1 — Registrar CPT acervo_ipcn + tax tema_acervo**
Trabalho: `register_post_type acervo_ipcn` (supports title/editor/thumbnail), `register_taxonomy tema_acervo`, flush permalinks.
DON: `WP Admin → Acervo` aparece, criar 3 itens fake funciona.

**T3.2 — archive-acervo_ipcn.html + single-acervo_ipcn.html**
Trabalho: archive com filtros visíveis (tema + ano, inspiração NYPL), grid 3/2/1 colunas, card 14px; single com metadata + CTA de associado.
DON: `stagingredesign/acervo/?nocache` lista 3 fakes com filtros operacionais.
**GATE 4:** você aprova layout do acervo vazio (sem conteúdo real).
PIVÔ 4: se filtros ficarem complexos, pivota para filtro mínimo (só `tema_acervo` dropdown) e deixa data para v2.

### FASE 4 — Portal de Associados (6 roles, fluxo manual v1)

**T4.1 — Roles + capabilities**
Trabalho: `Members` plugin (free), criar `nao_associado`, `associado`, `publicador`, `coordenador`, `diretoria` (manter `administrator` = você), `map_meta_cap` para `read_acervo_completo / edit_acervo / publish_acervo`.
DON: `wp user list --role=nao_associado` retorna teste criado.
**GATE 5:** você e a diretoria validam nomes dos cargos (pode renomear sem quebrar cap).

**T4.2 — /associados/cadastro (cria não associado)**
Trabalho: form público (6 campos da Q4: Nome/Email/Telefone/CPF opcional/Núcleo/Como contribuir) → `wp_create_user` com `role nao_associado` + `user_meta`, email para `contato@ipcnbrasil.org` via `wp_mail/hSendmail` já validado `bool(true)`.
DON: cadastro fake cria usuário e log em `wp_mail` não quebra; diretoria recebe email.
PIVÔ 4.2: se LGPD apertar, pivota para CPF removido e consent checkbox obrigatório.

**T4.3 — /associados/entrar + /associados/painel**
Trabalho: `wp_login_form` custom em `/entrar`, redirect por role; `/painel` com lista: associado vê "Meu acervo", diretoria vê "Pendentes (nao_associado)".
DON: logar como `nao_associado` mostra painel restrito; `associado` mostra acervo.
**GATE 6:** você testa login no celular e logout.

**T4.4 — Ativação manual nao_associado → associado**
Trabalho: no `WP Admin → Usuários`, diretoria muda role + dispara `wp_mail` "bem-vindo" (sem automação extra).
DON: mudar role libera acesso ao acervo completo (T4.5).
PIVÔ 4.4: se diretoria achar confuso, pivota para tela dedicada `/associados/aprovar` com botão "Aprovar" (1 dia extra).

**T4.5 — Paywall do acervo (últimos 10)**
Trabalho: `pre_get_posts` no archive + `template_redirect` no single: `associado+` vê tudo; `nao_associado` logado vê últimos 10 (ou 30 dias) + CTA "Torne-se associado"; deslogado vê CTA para `/cadastro`.
DON: teste com 15 itens fake: `nao_associado` abre 11º → `403 + CTA` (validado com `curl -b cookie`).
**GATE 7:** você decide se "últimos 10" está bom ou quer 20/30 dias — ajuste é 1 linha em `pre_get_posts`.

### FASE 5 — Editorial (publicador/coordenador)

**T5.1 — Fluxo pending → publicado**
Trabalho: `publicador` cria `acervo_ipcn` como `pending`; `coordenador/diretoria` vê em `WP Admin → Acervo → Pendentes` e publica. `coordenador` publica direto em tudo; `publicador` só no acervo.
DON: 1 rascunho de `publicador` aparece como pendente para `coordenador`.
GATE 8: cliente confirma que `publicador só no acervo` está ok (se quiser liberar depois, é só cap).

### FASE 6 — Conteúdo, SEO e performance

**T6.1 — Migração leve (sem arrastar Divi)**
Trabalho: copiar texto de páginas legado (ex: `Quem Somos` 8 sections) para blocos nativos — sem importer `et_pb_*`.
DON: validator `grep -r et_pb_ stagingredesign/wp-content/themes/ipcn-fse` retorna 0.

**T6.2 — SEO/performance**
Trabalho: `Yoast` continua, `Litespeed` purge, imagens `webp` 180w/472w como `ipcn-sem-fundo`, `lighthouse ≥90 mobile`.
DON: `curl -I .../acervo/?nocache | grep x-litespeed-cache: miss → hit` após reload.
GATE 9: você valida `lighthouse` e teste real no celular (footer, hero, grid).

**T6.3 — LGPD**
Trabalho: `Politica de Privacidade` + checkbox no cadastro + `user_meta` só visível para `diretoria/admin`.
DON: criar usuário fake com CPF vazio não quebra; admin vê, associado não.

### FASE 7 — Validação final e deploy manual (seu hPanel)

**T7.1 — Checklist de aceite (você assina)**
Trabalho: rodar todos os GATES 0–9 em `stagingredesign` com `?nocache` + mobile 375/768/1440.
DON: planilha `docs/aceite-stagingredesign.md` com screenshots.

**T7.2 — Handoff para produção**
Trabalho: export do tema `ipcn-fse.zip` + instruções hPanel (você faz o deploy, como combinado "pare de perguntar" — documentado, não automatizado).
DON: produção em `ipcnbrasil.org` roda tema novo, `staging` antigo mantido 30 dias como rollback.

### Cronograma e pivôs globais

* **Ordem travada:** Fase 1 (tokens) → Gate 1B → Fase 2 (shell) → Fase 3 (acervo) → Fase 4 (portal) — não inverta, pois `roles` dependem de saber o que é acervo.
* **Pivôs rápidos (≤1 dia):** paleta `ocre → terracota`, hero tipográfico → com foto, filtros completos → mínimo, `Members → User Role Editor`.
* **Pivôs caros (pausa e regrill):** mudar para `GeneratePress child` no meio da Fase 2 (joga fora FSE), escopo `coordenador por núcleo` no meio da Fase 4 (cria taxonomia `nucleo` e `pre_get_posts` escopado).
* **Sem sprint fechado:** você valida cada GATE no Telegram (foto do celular já serve) e eu só avanço após "segue".

## 8. Histórico de correções já entregues (base para o tema novo)

* `Associe-se` premium (hero navy 80px + card 720px/14px/sombra, inputs `input` single-line, botão navy hover, teste POST com nonce `Recebemos seu cadastro...` e `wp_mail true`)
* 4 páginas vazias corrigidas de `smart_post_show` para `rttpg` (Destaques 46, Diáspora 1, Colunistas 7, Notas 15) + remoção de sticky `5289` que contaminava todos os grids
* Footer logo limitado (180px/140px) via `ipcn-footer-logo-fix`
* Staging `staging.ipcnbrasil.org` — Fase 4 quase fechada, pendente validação visual e deploy manual hPanel
* Tema Divi segue nulled 4.20.2 — decisão travada: não licenciar, migrar para custom (este doc)

---
*Gerado em 30/08/2026 — grill 8 perguntas, fronteira vazia, pronto pra codar quando você mandar.*
