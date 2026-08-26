# Roadmap — IPCN Brasil (ipcnbrasil.org)

> Plano de estabilização, manutenção e evolução do site WordPress hospedado na Hostinger.

## Status atual (resumo)

- ✅ **Fase 0 concluída**: backup interno, remoção de 6GB de backups velhos, remoção de malware (`filter.php` + `monarx-analyzer.php`), correção do erro `Commands out of sync` + cron real.
- ✅ **Fase 1 concluída**: Elementor removido, conteúdo migrado para Divi, 18 plugins inativos removidos, deploy staging → produção feito.
- ✅ **Menus corrigidos (grande vitória)**: bug pré-existente de permalinks 404 causado pelo Really Simple SSL no `.htaccess` — resolvido removendo os blocos de redirect e desativando o plugin.
- 🔄 **Fase 2 em andamento (no staging)**: contas-fake de "seções" removidas (20 posts reatribuídos), cargos órfãos WooCommerce/Give limpos, PublishPress (Planner + Statuses) instalado. Falta definir usuários reais e publicar → ver `docs/fase2-cargos-publishpress.md`.
- ⏸️ **Divi**: mantido **nulled (pirata, sem licença)** por enquanto — decisão de comprar licença ou migrar será levada à contratante. Risco de segurança documentado abaixo.

## Contexto

- **Cliente:** Instituto de Pesquisas das Culturas Negras (IPCN) — `ipcnbrasil.org`
- **Hospedagem:** Hostinger compartilhado (server952, Brasil), LiteSpeed + HCDN
- **Staging:** `https://staging.ipcnbrasil.org` — install separado (`public_html/staging`, banco próprio). Trabalho feito no staging; **publicação em produção é manual via hPanel** (feita pela contratante quando aprovado).
- **Stack:** WordPress 7.1 · PHP 8.2.31 · WP-CLI 2.12 · SSH disponível (porta 65002)
- **Objetivo do cliente:** publicar notícias/artigos com facilidade, cargos de quem posta e quem aprova, identidade visual melhorada e site fácil de manter.

## Decisões tomadas

| Tema | Decisão |
|---|---|
| Construtor de página | **Manter Divi**, remover Elementor |
| Fluxo de aprovação | **PublishPress** (status + calendário editorial) |
| Ordem de execução | **Estabilizar primeiro**, depois visual e cargos |
| Forma de trabalho | Staging + Git (SSH), não editar produção direto |
| Manutenção pós-entrega | **Misto**: cliente posta conteúdo, nós cuidamos da técnica |
| Tema (Divi) | **Manter o Divi nulled por enquanto** (sem licença). Decisão de comprar licença ou migrar será levada à contratante. |

---

## Estratégia de versionamento (GitHub)

**Decisão:** criar um repositório **privado** no GitHub já no início do trabalho.

### Por quê (opinião)
- **Rollback seguro:** qualquer mudança errada no tema/plugin pode ser revertida com um `git checkout`.
- **Histórico e colaboração:** registra tudo o que foi feito e permite trabalhar em paralelo.
- **Casa para o projeto:** guarda este roadmap, docs e o código customizado num só lugar.
- **Segurança:** como o site hoje usa `FS_METHOD=direct` (sem git), não há nenhuma proteção contra erro humano — o git resolve isso.

### Regras de ouro
1. **Repo privado** (contém código do site).
2. **NUNCA versionar segredos:** `wp-config.php` (credenciais do banco), `.env`, chaves de API ficam fora do git (ver `.gitignore`).
3. **NÃO versionar mídia/cache:** `wp-content/uploads` (2.3GB), `ai1wm-backups` (6GB), caches.
4. Versionar: **tema customizado (child theme)**, **mu-plugins**, **snippets** e, opcionalmente, plugins/temas de terceiros para reproduzibilidade.
5. **Deploy** via `git pull` no servidor (SSH) ou staging → produção.

### Estrutura sugerida do repo
```
/                     # raiz = public_html
├── ROADMAP.md
├── docs/             # diagnóstico, decisões, treinamento
├── wp-content/
│   ├── themes/       # child theme (versionado)
│   ├── mu-plugins/   # código customizado (versionado)
│   └── plugins/      # opcional: pin de versões
└── .gitignore
```

---

## Diagnóstico resumido (levantado via SSH + análise remota)

### 🔴 Críticos
1. **6GB de backups velhos (2022–2023)** dentro de `wp-content/ai1wm-backups`.
2. **Erro recorrente no banco** `Commands out of sync` (cache do Divi + wp-cron), desde mar/2026.
3. **Cron quebrado** (`rsssl_every_five_minutes`, `tcmp_weekly` → `could_not_set`).
4. **Dois construtores ativos**: Divi 4.20.2 + Elementor Pro 3.7.0 (ambos desatualizados).
5. **SEO quebrado**: Yoast inativo → `robots.txt` e sitemap retornam 404.

### 🟠 Médios
6. **Sem cache** ativo (W3TC e LiteSpeed Cache inativos/ausentes). TTFB ~0.9s, HTML 214KB.
7. **Sem segurança**: XML-RPC ligado, `readme.html` expõe versão, login padrão, sem 2FA.
8. **39 plugins** (20 ativos, ~19 sobras: WooCommerce, Give, Monarch, Loco, W3TC…).
9. **Cargos bagunçados**: autores usados como "seções" com e-mails falsos; cargos órfãos (`customer`, `give_donor`).

### ✅ Positivos
- PHP/WP atualizados, HTTPS válido, LiteSpeed/HCDN prontos.
- SSH + WP-CLI disponíveis.
- Já existem: Custom Post Type UI, `new-user-approve`, CPTs `project` e `exclusivo_associados`.
- Conteúdo: 152 posts (notícias ativas até out/2025), 57 páginas, 13 usuários.

---

## Roadmap por fases

### Fase 0 — Versionamento + Estabilização (começar aqui)
- [x] Criar repositório **privado** no GitHub e clonar localmente.
- [x] Adicionar `.gitignore` (WordPress) e commit inicial (roadmap + docs).
- [x] Backup completo via SSH (interno, em `~/backups/ipcn-2026-08-25/`): `mysqldump` (23MB) + tar dos arquivos (6.2GB).
- [x] Baixar e **remover os 6GB** de `wp-content/ai1wm-backups` (wp-content: 9GB → 3GB).
- [x] Remover **malware** (`filter.php`) + `monarx-analyzer.php` e varredura completa (core/plugins/uploads/snippets/HFCM limpos).
- [x] Corrigir erro `Commands out of sync` e cron: mu-plugin desativa cache de features do Divi + `DISABLE_WP_CRON` + cron real no hPanel (`/usr/bin/php .../wp-cron.php` a cada 10min).
- [x] **Corrigir bug de permalinks 404 (pré-existente):** os blocos de redirect do Really Simple SSL no `.htaccess` quebravam o rewrite do WordPress na origem (IPv4). Removidos os blocos + plugin desativado (HTTPS já é forçado pela Hostinger).
- [ ] Criar **staging** no hPanel (clone da produção). → ✅ criado pela contratante (`staging.ipcnbrasil.org`)
- [ ] Versionar o child theme / mu-plugins no GitHub. → ✅ mu-plugins versionado (`ipcn-optimizations.php`); **child theme não existe** (Divi usado direto)

### Fase 1 — Consolidar Divi (remover Elementor)
- [x] Inventariar páginas construídas em Elementor (`_elementor_edit_mode` + `elementor_library`) → ver `docs/fase1-inventario-elementor.md`.
- [x] Reconstruir/adaptar no Divi: `Merendas e Afetos` (virou post com galeria nativa), `Quem Somos` (já era Divi, só limpou flag).
- [x] Desativar e remover Elementor + Elementor Pro + dados órfãos (options, CPTs).
- [x] Despublicar placeholders quebrados (`Entrar`, `Painel de Associados`) e apagar rascunhos antigos.
- [x] Remover 18 plugins inativos (WooCommerce/Give e sobras). Mantidos: Yoast, All-in-One WP Migration, Better Search Replace.
- [ ] Atualizar Divi 4.20.2 → versão atual (**BLOQUEADO**: tema é nulled/sem licença — pendente decisão da contratante).
- [ ] Validar no staging antes de publicar (deploy para produção).

### Fase 2 — Cargos e fluxo editorial (PublishPress)
- [x] Remover contas de teste/e-mails falsos e reatribuir posts. *(staging: 5 contas-fake removidas, 20 posts → `admin`)*
- [x] Remover cargos órfãos (`customer`, `give_donor`). *(staging: + `shop_manager`, `give_*`; addon Give removido)*
- [x] Definir cargos: Redator (escreve) → Revisor/Editor (aprova) → Admin. *(cargos nativos limpos: 2 autores, 2 editores, 1 admin)*
- [x] Instalar e configurar **PublishPress** (status customizados + calendário editorial). *(Planner 4.8.0 + Statuses 1.3.4 instalados; tradução/ajuste fino pendente)*
- [ ] Criar/ajustar usuários reais com os cargos corretos. *(pendente: lista de nomes/e-mails da contratante)*

### Fase 3 — SEO, performance e segurança
- [x] **SEO (Yoast)**: reativado, `robots.txt` + sitemap corrigidos (estavam 404), templates de title/metadesc/Open Graph, breadcrumbs. *Falta publicar + purgar cache do CDN.*
- [x] **Typo corrigido**: "Culturas Negra" → "Culturas Negras" (staging).
- [x] **Performance (LiteSpeed Cache)**: instalado, cache de página + CDN (HCDN) ligados. TTFB ~2.2s → **16ms** (hit). Object cache off (sem Redis na Hostinger).
- [x] **Segurança (Wordfence)**: instalado; firewall ligado (learning mode), XML-RPC bloqueado (403), versão do WP escondida, live traffic off.
- [x] **2FA**: obrigatório para `administrator` e `editor` (cada usuário ativa no próprio perfil com app TOTP).
- [x] **Really Simple SSL desativado** no staging (redundante; Hostinger já força HTTPS — foi a causa do bug de permalinks 404 na Fase 0).
- [ ] (Opcional) trocar URL de login.

### Fase 4 — Consolidação visual (NÃO é redesign)
- [x] **Páginas consertadas (smart_post_show ausente)**: Editorial (grid 5338, 6), Notícias (grid 5340, 81, paginado), Agenda IPCN (grid 5342, 52, paginado). Todas usavam `[smart_post_show]` (plugin ausente) → substituído por grid TPG via `post__in`. Backups em `/tmp/*_backup_*.txt`.
- [x] **Correção da fonte de ícones (ETmodules)**: o tema Divi 4.20.2 veio **sem os arquivos de fonte** → menu mobile virava "A" e setas do slider viravam "4"/"5". Restaurados os arquivos `ETmodules.*` + injetado `@font-face` via mu-plugin. (Ver `docs/fase4-etmodules-fix.md`.)
- [ ] **Validar na tela real** (mobile/devtools) que hambúrguer e setas renderizam como ícones.
- [ ] **Decisão de tema** (licenciar Divi vs migrar) — ver `docs/plano-tema-divi.md`. Travar antes de redesign profundo.
- [ ] Auditar consistência (cor navy `0d176b`, fontes, logo 3161) entre páginas.
- [ ] Acertar páginas com problemas conhecidos (placeholders quebrados: Entrar, Painel de Associados).
- [ ] (Futuro, pós-contratante) Redesign: novas cores/tipografia/layout.

### Fase 5 — Treinamento e documentação
- [ ] Guia curto "como postar" por cargo + fluxo de aprovação.
- [ ] Rotina de manutenção (atualizar no staging → produção).
- [ ] Documentar como fazer backup e restaurar.

### Backlog (fases futuras)
- [ ] **Painel de Associados** (feature novo): login + área restrita de membros, do zero (não existe hoje).
- [ ] Decidir destino de `OLD/`, `associa/` e `sistema/` (apps legados).

---

## Pré-requisitos do cliente

- [ ] **Licença do Divi (Elegant Themes):** o tema atual é **nulled (pirata)**. Pendente decisão da contratante: comprar licença (~US$ 89/ano) ou migrar pra tema gratuito.
- [ ] **Rotacionar senha SSH/FTP** após o trabalho (foi compartilhada em texto).
- [ ] Confirmar quem serão os usuários (nomes + e-mails reais) e seus cargos.

## Riscos e cuidados

- **Divi nulled (pirata)**: não atualiza e é o vetor provável do malware já encontrado (`filter.php`). Pode conter backdoors. **Prioridade alta** resolver (licença ou migração). Mitigação até lá: Wordfence (Fase 3) + rotacionar senhas.
- **Remover Elementor pode quebrar páginas** → inventariar antes (Fase 1, tarefa 1).
- **Atualizar Divi pode mudar layout** → fazer no staging e validar.
- **Nunca** commitar `wp-config.php`, chaves de API ou backups.
- **Site já foi comprometido** (malware `filter.php` de set/2024, já removido) → **rotacionar todas as senhas** (WP, banco, SSH/FTP, e-mails).
- `OLD/`, `associa/` e `sistema/` **não foram varridos** (apps separados, mantidos a pedido do cliente).
