# Fase 2 — Cargos e fluxo editorial (PublishPress)

> Executado em **26/08/2026** no **staging** (`https://staging.ipcnbrasil.org`), via SSH + WP-CLI.
> Publicação em produção: manual, pela contratante (hPanel), quando aprovado.

## Ambiente

- Staging: `~/domains/ipcnbrasil.org/public_html/staging/` — install WordPress separado.
- Banco do staging: `u654777386_kQNX2` (prefixo `wordpress_`).
- Backup pré-mudança: `~/backups/staging-ipcn-pre-fase2.sql` (22.6MB, mysqldump).

## O que foi feito

### 1. Reatribuição de posts das contas-fake
As "seções" eram mantidas **em dobro**: como conta de usuário **e** como categoria (que já existiam com os mesmos nomes). As contas foram consideradas redundantes — a organização por seção fica nas **categorias**, e o autor passa a ser uma pessoa real.

| Conta | Posts | Categoria equivalente |
|---|---|---|
| Agenda do IPCN (120) | 4 | `Agenda IPCN` (50 posts) |
| Destaques (121) | 11 | `Destaques` (46 posts) |
| Editorial (122) | 3 | `Editorial` (6 posts) |
| Drops Antirracistas (123) | 2 | `Drops Antirracista` (2 posts) |
| Presenca (125) | 0 | — |

**20 posts reatribuídos para `admin`** (default neutro e reversível — distribuir para autores reais quando a lista estiver definida).

### 2. Contas removidas (8)
- 5 contas-fake de seção: `Agenda do IPCN`, `Destaques`, `Editorial`, `Drops Antirracistas`, `Presenca`.
- 2 órfãs WooCommerce (`customer`): `Cristina`, `Flavia Oliveira`.
- 1 órfã GiveWP (`give_donor`): `rasfabio@gmail.com`.

Nenhuma tinha comentários ou mídia vinculada (verificado antes de apagar).

### 3. Cargos órfãos removidos (6)
`customer`, `shop_manager` (WooCommerce) + `give_manager`, `give_accountant`, `give_worker`, `give_donor` (GiveWP) — plugins de origem já removidos na Fase 1.

Também removido o addon leftover **`give-receipt-attachments`** (ativo, mas o GiveWP core não existe mais).

### 4. PublishPress instalado
- **PublishPress Planner 4.8.0** — calendário editorial + conteúdo (Kanban/lista).
- **PublishPress Statuses 1.3.4** — status customizados do fluxo editorial (defaults: *Pitch → Assigned → In Progress → Approved → Needs Work*).

> Nota: os status são "virtuais" (definidos no código) e materializam no banco só quando customizados pela interface. O fluxo default já está ativo; a tradução PT-BR e o ajuste fino ficam para a tela de configuração do plugin.

## Estado final (staging)

**Usuários (5):**
| Login | Cargo | Papel no fluxo |
|---|---|---|
| admin | administrator | Admin (aprova/publica) |
| margarethferreiraadv | editor | Revisor/Editor |
| mpascoalnascimento | editor | Revisor/Editor |
| fatimamoura | author | Redatora |
| marcelodias | author | Redator |

**Cargos:** só os padrões do WP (`administrator`, `editor`, `author`, `contributor`, `subscriber`) + 3 custom preservados (ver pendências).

## Pendências (precisam de decisão da contratante)

1. **Lista de usuários reais** (nomes + e-mails + cargo) para criar/ajustar as contas.
2. **Cargos de assinatura** `pms_subscription_plan_3025` ("Pedido de Associação ao IPCN") e `associado_ipcn` ("Associado(a) IPCN") — sem plugin de origem, mas ligados ao futuro **Painel de Associados**. Manter ou remover?
3. **Cargo `translator`** — órfão (sem plugin de tradução). Remover?
4. **Tradução PT-BR dos status** e ajuste fino do fluxo (feito na tela do PublishPress no wp-admin).
5. **Distribuir os 20 posts** reatribuídos ao `admin` para os autores reais (opcional).
