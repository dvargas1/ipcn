# Roadmap — IPCN Brasil (ipcnbrasil.org)

> Plano de estabilização, manutenção e evolução do site WordPress hospedado na Hostinger.

## Contexto

- **Cliente:** Instituto de Pesquisas das Culturas Negras (IPCN) — `ipcnbrasil.org`
- **Hospedagem:** Hostinger compartilhado (server952, Brasil), LiteSpeed + HCDN
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
- [ ] Criar **staging** no hPanel (clone da produção).
- [ ] Versionar o child theme / mu-plugins no GitHub.

### Fase 1 — Consolidar Divi (remover Elementor)
- [x] Inventariar páginas construídas em Elementor (`_elementor_edit_mode` + `elementor_library`) → ver `docs/fase1-inventario-elementor.md`.
- [x] Reconstruir/adaptar no Divi: `Merendas e Afetos` (virou post com galeria nativa), `Quem Somos` (já era Divi, só limpou flag).
- [x] Desativar e remover Elementor + Elementor Pro + dados órfãos (options, CPTs).
- [x] Despublicar placeholders quebrados (`Entrar`, `Painel de Associados`) e apagar rascunhos antigos.
- [x] Remover 18 plugins inativos (WooCommerce/Give e sobras). Mantidos: Yoast, All-in-One WP Migration, Better Search Replace.
- [ ] Atualizar Divi 4.20.2 → versão atual (exige chave Elegant Themes).
- [ ] Validar no staging antes de publicar (deploy para produção).

### Fase 2 — Cargos e fluxo editorial (PublishPress)
- [ ] Remover contas de teste/e-mails falsos e reatribuir posts.
- [ ] Remover cargos órfãos (`customer`, `give_donor`).
- [ ] Definir cargos: Redator (escreve) → Revisor/Editor (aprova) → Admin.
- [ ] Instalar e configurar **PublishPress** (status customizados + calendário editorial).
- [ ] Criar/ajustar usuários reais com os cargos corretos.

### Fase 3 — SEO, performance e segurança
- [ ] Reativar **Yoast SEO** → gerar `robots.txt` e sitemap corretos.
- [ ] Instalar e configurar **LiteSpeed Cache** + HCDN.
- [ ] Instalar **Wordfence**, bloquear XML-RPC, esconder versão, ativar **2FA**.
- [ ] (Opcional) trocar URL de login.

### Fase 4 — Identidade visual (polimento no staging)
- [ ] Ajustar cores, tipografia, logo e espaçamentos.
- [ ] Garantir consistência entre páginas.
- [ ] Documentar mini-guia de marca para a equipe.

### Fase 5 — Treinamento e documentação
- [ ] Guia curto "como postar" por cargo + fluxo de aprovação.
- [ ] Rotina de manutenção (atualizar no staging → produção).
- [ ] Documentar como fazer backup e restaurar.

### Backlog (fases futuras)
- [ ] **Painel de Associados** (feature novo): login + área restrita de membros, do zero (não existe hoje).
- [ ] Decidir destino de `OLD/`, `associa/` e `sistema/` (apps legados).

---

## Pré-requisitos do cliente

- [ ] **Licença do Divi (Elegant Themes):** login/chave para atualizar o tema.
- [ ] **Rotacionar senha SSH/FTP** após o trabalho (foi compartilhada em texto).
- [ ] Confirmar quem serão os usuários (nomes + e-mails reais) e seus cargos.

## Riscos e cuidados

- **Remover Elementor pode quebrar páginas** → inventariar antes (Fase 1, tarefa 1).
- **Atualizar Divi pode mudar layout** → fazer no staging e validar.
- **Nunca** commitar `wp-config.php`, chaves de API ou backups.
- **Site já foi comprometido** (malware `filter.php` de set/2024, já removido) → **rotacionar todas as senhas** (WP, banco, SSH/FTP, e-mails).
- `OLD/`, `associa/` e `sistema/` **não foram varridos** (apps separados, mantidos a pedido do cliente).
