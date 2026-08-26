# IPCN Brasil — Manutenção e evolução do site

Repositório do projeto de estabilização e manutenção do site do **Instituto de Pesquisas das Culturas Negras** (`ipcnbrasil.org`), WordPress hospedado na Hostinger.

> Documento principal: [ROADMAP.md](./ROADMAP.md)

## Onde estamos

- ✅ **Fase 0 — Estabilização**: backup, remoção de 6GB, malware, cron, `Commands out of sync`.
- ✅ **Fase 1 — Consolidar Divi**: Elementor removido, 18 plugins limpos, deploy staging.
- ✅ **Menus corrigidos** (bug de permalinks 404 do Really Simple SSL).
- 🔄 **Fase 2 — Cargos + PublishPress**: limpeza + PublishPress no staging. Falta lista de usuários reais e publicação.
- ✅ **Fase 3 — SEO + Performance + Segurança** (staging): Yoast/robots/sitemap, LiteSpeed Cache (TTFB 2.2s→16ms), Wordfence (XML-RPC 403, 2FA admin/editor). Falta publicar + purgar CDN.
- 🔄 **Fase 4 — Consolidação visual** (no staging, sem redesign):
  - Header mantido original (centralizado + menu embaixo) a pedido da contratante.
  - Menu reduzido 14 → 9 itens; grid da home reestruturado (sem filtros, 4 posts); home enxuta (3 sections).
  - Footer reformulado/compactado/centralizado (sem "Saiba Mais" nem "ON1" → "Núcleo de Tecnologia do IPCN").
  - **Páginas vazias consertadas** (plugin `smart_post_show` ausente → The Post Grid): Editorial, Notícias (81, paginado), Agenda IPCN (52, paginado).
  - **Apoia-se** (PIX `contato@ipcnbrasil.org`) e **Associe-se** (form Divi estilizado Nome/E-mail/Telefone → `contato@ipcnbrasil.org`) recriadas.
  - Fonte de ícones do Divi (ETmodules) corrigida.
  - **Pendente:** varredura das demais páginas (Destaques, Diáspora, Colunistas, Notas, Drops) + validação na tela + publicação em produção (manual, pela contratante, após aprovação).
- 📋 **Plano de tema** criado (`docs/plano-tema-divi.md`) para a contratante decidir antes de redesign.

## Decisão sobre o Divi (nulled)

O tema **Divi instalado é pirata (nulled, sem licença)** — foi usado pelo desenvolvedor anterior.

**Decisão atual:** manter o nulled por enquanto, trabalhar com o que temos, e levar à contratante a decisão de **comprar a licença** (~US$ 89/ano) ou **migrar pra tema gratuito**.

⚠️ **Risco:** temas nulled não atualizam e são o vetor mais comum de malware (foi provavelmente assim que o `filter.php` entrou). Mitigação até resolver: Wordfence + rotacionar senhas.

## Repo (privado)

- `ROADMAP.md` — plano e status por fase.
- `docs/` — diagnóstico, inventários e decisões.
- `wp-content/mu-plugins/` — código customizado (ex.: `ipcn-optimizations.php`).

**Nunca** versionar `wp-config.php`, chaves de API, mídia ou backups (ver `.gitignore`).
