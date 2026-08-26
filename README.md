# IPCN Brasil — Manutenção e evolução do site

Repositório do projeto de estabilização e manutenção do site do **Instituto de Pesquisas das Culturas Negras** (`ipcnbrasil.org`), WordPress hospedado na Hostinger.

> Documento principal: [ROADMAP.md](./ROADMAP.md)

## Onde estamos

- ✅ **Fase 0 — Estabilização**: backup, remoção de 6GB de backups velhos, remoção de malware, correção de cron e do erro `Commands out of sync`.
- ✅ **Fase 1 — Consolidar Divi**: Elementor removido, conteúdo migrado para Divi, limpeza de 18 plugins inativos, deploy staging → produção.
- ✅ **Menus corrigidos** (bug pré-existente de permalinks 404 causado pelo Really Simple SSL).
- ⏳ **Próximo**: Fase 2 (cargos + PublishPress) e Fase 3 (SEO/cache/segurança).

## Decisão sobre o Divi (nulled)

O tema **Divi instalado é pirata (nulled, sem licença)** — foi usado pelo desenvolvedor anterior.

**Decisão atual:** manter o nulled por enquanto, trabalhar com o que temos, e levar à contratante a decisão de **comprar a licença** (~US$ 89/ano) ou **migrar pra tema gratuito**.

⚠️ **Risco:** temas nulled não atualizam e são o vetor mais comum de malware (foi provavelmente assim que o `filter.php` entrou). Mitigação até resolver: Wordfence + rotacionar senhas.

## Repo (privado)

- `ROADMAP.md` — plano e status por fase.
- `docs/` — diagnóstico, inventários e decisões.
- `wp-content/mu-plugins/` — código customizado (ex.: `ipcn-optimizations.php`).

**Nunca** versionar `wp-config.php`, chaves de API, mídia ou backups (ver `.gitignore`).
