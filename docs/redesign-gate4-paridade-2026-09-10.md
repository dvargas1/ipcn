# Redesign FSE — 10/09/2026: Gate 4 validado + paridade de paginas internas

## Gate 4 (Acervo) — VALIDADO por análise no stagingredesign
- `/acervo/` 200, hero navy, term-list, 3 itens fake listados com cards (radius 14 + shadow), sem paginacao (so 3 itens, ok)
- Filtros por tema: /temas/documentos, /temas/fotografia, /temas/memoria-oral todos 200 filtrando correto
- Single: /acervo/carta-de-fundacao-do-ipcn-1975 200 com titulo em hero navy h2 + CTA /associe-se
- Pendencia menor: taxonomy-tema_acervo.html e single sem links raio — ok para o gate
- **Gate 4: APROVADO (tecnico).** Falta so o "ok" formal do Daniel conferindo no navegador.

## Varredura das paginas internas (feat: paridade)
Diagnostico via sweep PHP no WP: paginas com shortcodes Divi crus ou sem hero navy:
FIXED (reescritas com hero navy padrao + grid de cards `[ipcn_query_posts]`):
- colunistas (2016) — hero + grid categoria colunistas (7 posts, cards 35 contados)
- notas (2625) — hero + grid notas (15 posts, query block nativo herdado do archive)
- destaques-3 (2117) — hero + grid destaques (46)
- diaspora (2619) — hero + grid diaspora
- memorias (94) — hero + grid memorias (2)
- conteudos-restritos (2965) — hero + CTA "Torne-se associado" (vira portal na Fase 4)
- politica-de-privacidade (3) — hero + texto extraido do et_pb (15KB, wp:html)

## Archive de categorias (novo)
- `templates/archive.html` reescrito: usarHERO navy (eyebrow "IPCN · <cat>" + h1 + desc) via shortcode `ipcn_archive_hero` (le a queried category) + query padrao 12 posts + paginacao + term-list removida (era archive de CPT ou de categorias com mesmo legibilidade errada)
- `functions.php` + `ipcn_archive_hero` no fim do arquivo (hero de arquivo reutiliza across categorias)
- Deploy: tar com strip-components=3 para o servidor e `functions.php` com duplicidade de `<?php` resolvida (php -l ok)

## Paridade QR PIX no redesign
- Baganco: attachment 5358 tinha sido importado em staging, mirror no redesign vazio
- Criado attachment 5372 no redesign (2026/09/qrcode-pix.jpg adicionado via scp no uploads)
- `/apoia-se/` (2382 no redesign): substituido "QR Code PIX: em breve." bpor bloco imagem 220px + "Escaneie para apoiar" centralizado
- Validado: qrcode-pix.jpg 200 no redesign e imagem em destaque no markup

## Design aprovado no Divi staging (10/09) nao foi portado pro FSE: mantivemos o design model da Fase 2 (hero tipografico, footer utilitario, cards 14px)
- Footer FSE ja e o modelo sem logotipo (paridade automantida com aprovado: sem logo, col Instituto/Contato/Redes, mailto/tel clicaveis)

## Pendencias pro proximo contato
- Substituir QR dinamico PagSeguro (venceu 08/09) por estatico quando cliente mandar
- Daniel validar visualmente o archive hero nas 4 categorias (curta "aprovado" pro Gate 5)
