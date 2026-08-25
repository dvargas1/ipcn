# Fase 1 — Inventário Elementor (para migração ao Divi)

> Levantado em 25/08/2026 via WP-CLI, antes da migração. Produção: `ipcnbrasil.org`.

## Páginas/publicados construídos em Elementor

| ID | Tipo | Título | Status | Widgets usados | Observação |
|---|---|---|---|---|---|
| 2045 | page | Quem Somos | publish | (conteúdo simples, sem widgets) | Migração fácil |
| 2957 | page | Entrar | publish | `eael-login-register`, `text-editor` | **Login — widget quebrado** (EAEL ausente) |
| 3027 | page | Painel de Associados do IPCN | publish | `eael-post-grid` | **Painel de membros — widget quebrado** |
| 5186 | post | Merendas e Afetos | publish | `image-carousel`, `text-editor` | Migração fácil |
| 3166 | elementor_library | login-page | publish | `eael-login-register` | Template de login (redundante) |
| 2842 | elementor_library | Default Kit | publish | — | Kit global do Elementor |

## Rascunhos (não publicados — podem ser ignorados/excluídos)

- `Home` (4145), `Semana Jurídica IPCN` (4147), `a-tercas` (2724, 4149, 4150, 4151), `Elementor #5273` (5273).

## Descoberta crítica

Os widgets `eael-*` são do plugin **Essential Addons for Elementor (EAEL)**, que **NÃO está instalado** no site. Consequência:

- A página **Entrar** (login) e o template **login-page** usam `eael-login-register` → **não renderizam o formulário de login**.
- A página **Painel de Associados do IPCN** usa `eael-post-grid` → **não renderiza a listagem**.

Ou seja, a área de login/painel de associados já estava quebrada antes da migração.

## Decisões pendentes

1. A área de associados ainda é usada? Existe um app separado em `associa/` (Yii, 1.9GB) que pode ser o portal real — ou a versão WP (`Painel de Associados` + CPT `exclusivo_associados`) é a pretendida.
2. Como recriar o login/painel no Divi (formulário de login + listagem de posts)?

## Escopo da migração

- **Fácil:** `Quem Somos` (2045) e `Merendas e Afetos` (5186).
- **Requer decisão:** `Entrar` (2957) e `Painel de Associados` (3027).
