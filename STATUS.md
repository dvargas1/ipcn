# STATUS — IPCN Brasil (ipcnbrasil.org)

> Projeto de manutenção/evolução de site WordPress (Hostinger). Trabalho em staging; publicação em produção é MANUAL pela contratante.

## Estado atual (08/2026)
- **Fase 4 (consolidação visual, NÃO redesign) QUASE pronta no staging** — pendente validação na tela + deploy manual produção.
- Concluído: header original mantido (centralizado+menu embaixo), menu 14→9 itens, grid home reestruturado (4 posts, sem filtros), home enxuta (3 sections), footer reformulado/compactado/centralizado (sem "Saiba Mais", sem "ON1"→"Núcleo de Tecnologia do IPCN"), fonte ETmodules corrigida, páginas vazias consertadas (Editorial/Notícias 81/Agenda IPCN 52 via The Post Grid), Apoia-se (PIX) e Associe-se recriadas (form premium: hero navy #0d176b + card branco 720px com sombra suave, 3 campos input/email/input com foco navy, botão navy uppercase com hover, mensagem de sucesso verde — testado via POST com nonce, wp_mail ok → contato@ipcnbrasil.org). Bug field_type text→input corrigido (textarea virava campo gigante).

## Acesso
- SSH alias `ipcn` (~/.ssh/config) → staging em `~/domains/ipcnbrasil.org/public_html/staging`, banco `u654777386_kQNX2`.
- URL: https://staging.ipcnbrasil.org
- Deploy produção: manual via hPanel (staging→produção), feito pela contratante após aprovação.

## Decisões da contratante (não fuja disso)
- Header **centralizado + menu embaixo** (reverti uma reestruturação minha que ela não pediu).
- Tudo usa email **contato@ipcnbrasil.org**.
- Visual = consolidar com o que tem, NÃO redesign (redesign fica pra reunião futura com a contratante).
- Tema Divi é **nulled/sem licença** — decisão de comprar licença ou migrar pendente (documentado em docs/plano-tema-divi.md).

## Padrão de conserto de páginas vazias
Causa raiz recorrente: shortcode `[smart_post_show id="X"]` (plugin "Smart Post Show" / post type sp_post_carousel) AUSENTE em staging e produção. Conserto: criar grid do The Post Grid (post type `rttpg`) espelhando a config do grid que funciona (ID 2748, layout=isotope1/layout_type=isotope), setar `post__in` com os IDs dos posts da categoria, e substituir o shortcode na página (backup em /tmp antes).

## Fix Associe-se 30/08/2026 — muse spark
- Removida seção fantasma vazia (440px com background 12-slide) que deixava topo feio
- Corrigido field_type: text (textarea) → input (input single-line) — causa do visual quebrado
- Novo layout: hero navy 80px + card #fff 720px/14px radius/sombra rgba(13,23,107,.09), inputs borda #e2e8f0 foco navy, botão #0d176b→#1a2a9e
- Testado funcional: POST com nonce + et_pb_contact_email_fields JSON → mensagem "Recebemos seu cadastro com sucesso!" e wp_mail true (hsendmail)
- CSS premium no mu-plugin (ipcn-form-style) com estados de erro/sucesso e mobile 100% width
- Cache purgado (LiteSpeed + et-cache + HCDN nocache verificado)

## Pendências conhecidas
- Varredura das páginas restantes (Destaques, Diáspora, Colunistas, Notas, Drops Antirracista) — provável mesmo problema smart_post_show.
- QR Code PIX da Apoia-se (se a contratante tiver a imagem).
- Validação na tela (mobile/desktop).
- Deploy produção (manual, pela contratante).

## Referências
- ROADMAP.md e README.md no repo (atualizados até Fase 4).
- mu-plugin: wp-content/mu-plugins/ipcn-optimizations.php (ETmodules fix + estilo de form).
- Docs: docs/fase1-inventario-elementor.md, docs/fase2-*, docs/fase3-seo.md, docs/fase4-*.md, docs/plano-tema-divi.md.
