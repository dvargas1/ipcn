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

## 6. Histórico de correções já entregues (base para o tema novo)

* `Associe-se` premium (hero navy 80px + card 720px/14px/sombra, inputs `input` single-line, botão navy hover, teste POST com nonce `Recebemos seu cadastro...` e `wp_mail true`)
* 4 páginas vazias corrigidas de `smart_post_show` para `rttpg` (Destaques 46, Diáspora 1, Colunistas 7, Notas 15) + remoção de sticky `5289` que contaminava todos os grids
* Footer logo limitado (180px/140px) via `ipcn-footer-logo-fix`
* Staging `staging.ipcnbrasil.org` — Fase 4 quase fechada, pendente validação visual e deploy manual hPanel
* Tema Divi segue nulled 4.20.2 — decisão travada: não licenciar, migrar para custom (este doc)

---
*Gerado em 30/08/2026 — grill 8 perguntas, fronteira vazia, pronto pra codar quando você mandar.*
