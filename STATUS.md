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

## Pausa redesign 01/09/2026 — onde paramos (mobile)
- **RESOLVIDO 03/09/2026 (commit 4d7c3b2):** diagnóstico da quebra: (1) functions.php vazio → style.css nunca carregava (nenhum fix visual ativo); (2) logo PNG preto (ipcn-sem-fundo 472x787) em footer ink = preto sobre azul escuro; (3) título longo estourava header com 9 itens de menu; (4) páginas internas com shortcodes Divi crus. Corrigido: functions.php enfileira style.css; header = logo 56px + wordmark "IPCN" (padrão sketch aprovado); footer = fundo navy com LOGO-ORIGINAL-TEXTURIZADA-RETRO-AZUL.png 48px; páginas (Quem Somos, Projetos, Editorial, Notícias, Agenda, Drops, Associe-se, Apoia-se, Fale Conosco) refeitas em blocos nativos FSE com grid de cards por categoria (shortcode ipcn_query_posts, paginação /page/N/); forms nativos Associe-se e Fale Conosco (admin-post.php + honeypot, sem nonce por causa do cache) testados com wp_mail real pro contato@ipcnbrasil.org; CTA /acervo (404) → /noticias; root padding 20px + alignfull no hero/footer. Backups: tema em /tmp/ipcn-fse-backup-20260903-2029.tar.gz e páginas em /tmp/ipcn-pages-backup-20260903_205457.json (servidor).
- **Pendente de validação:** visual no celular (375px) — logo do footer é vertical (1200x1600), se ficar estranho pedir asset horizontal/SVG pra substituir. Menu mobile usa overlay padrão do WP (estilizado no style.css).
- **Histórico:** Stagingredesign clonado do `staging` da Fase 4 (152 posts, The Post Grid, Yoast, LiteSpeed) para `u654777386_DbbDB` — tema `ipcn-fse` FSE ativo. Sketches validados: `docs/sketches/museu-afro.html` (branco/ocre) e `amistad.html`. Usuário gostou das duas ideias (híbrido).

## Referências
- ROADMAP.md e README.md no repo (atualizados até Fase 4).
- mu-plugin: wp-content/mu-plugins/ipcn-optimizations.php (ETmodules fix + estilo de form).
- Docs: docs/fase1-inventario-elementor.md, docs/fase2-*, docs/fase3-seo.md, docs/fase4-*.md, docs/plano-tema-divi.md.
