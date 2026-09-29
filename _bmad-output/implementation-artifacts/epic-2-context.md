# Epic 2 Context: A Agenda, do editor ao Visitante

<!-- Generated from planning artifacts. Regenerate with compile-epic-context if planning docs change. -->

## Goal

Dar à equipa uma forma de marcar um encontro com uma data própria — não uma chave de metadados escrita à mão — e dar ao Visitante os próximos encontros, na Home e numa página de Agenda completa. A Agenda é a única superfície do tema que corre a sua própria consulta, porque a janela temporal ("a partir de hoje, por ordem crescente") não se exprime com as listagens nativas. Sem encontros por vir, o vazio explica-se e encaminha para o canal público do instituto, nunca mostrando encontros passados no lugar dos próximos.

## Stories

- Story 2.1: O campo de data do encontro no editor
- Story 2.2: O bloco `ipcn/agenda`
- Story 2.3: A Agenda na Home e na sua página, com o vazio digno
- Story 2.4: O archive de `agenda-ipcn` deixa de listar encontros

## Requirements & Constraints

- **Próximos encontros (FR6):** data, nome e, quando existir, lugar. Superfície própria (Agenda) com a lista completa; a Home mostra só os próximos. Só entram encontros com data igual ou posterior a hoje, por ordem cronológica crescente.
- **Sem lugar, o encontro aparece mesmo assim** — o lugar não se inventa.
- **Um encontro sem data não conta como próximo**; se estiver listado, aparece sem data, nunca com uma data inventada.
- **Agenda vazia (FR7):** estado vazio digno e ligação ao canal público do IPCN. Nunca encontros passados a fingir de próximos.
- **Acessibilidade e alcance:** WCAG 2.2 AA; alvos de toque de 44px; reflow a 320px sem scroll horizontal; um só `h1` por página, medido no HTML servido (na Agenda, o `h1` é o da `page-hero`).
- **Ajuda consistente:** contacto e Política de privacidade alcançáveis de qualquer página, no rodapé.
- **Verificação:** sem build step, sem suite de testes e sem CI. Verifica-se com `php -l` sobre o PHP alterado e observação no browser, no ambiente de revisão, depois de purgar a cache.

## Technical Decisions

- **Bloco dinâmico registado no servidor:** `ipcn/agenda`, com um único atributo inteiro `limite` (por omissão `3`) e um `render_callback`. É o único lugar em que o tema corre a própria consulta; uma listagem normal é sempre `core/query` ou o archive herdado. O bloco é usado em template e **não** aparece no inseridor nesta entrega.
- **A consulta:** publicações da categoria `agenda-ipcn` com `data_evento` igual ou posterior a hoje, por ordem crescente. Home usa `limite: 3`; página da Agenda usa `limite: -1`. Selecção por slug, nunca por `term_id`.
- **`data_evento` é a única data do encontro:** meta de `post`, com caixa de meta própria e `input type="date"` (não o painel de campos cru). Guardada em `Y-m-d`, validada ao guardar; outro formato é rejeitado sem apagar um valor anterior válido. Campo vazio é permitido. Não há alternativa por `post_date`. Registar a meta não cria o campo no editor — a caixa de meta é que o cria.
- **Render pelo cartão, sem markup à mão:** cada encontro renderiza pelo pattern do cartão compacto (`ipcn-card`), fora de um loop, via `render_block()` com o `postId` no contexto. Nenhum caminho escreve markup de cartão à mão; um render sem o contexto do post mostra o título da própria página e falha em silêncio.
- **Contrato de classes do cartão:** `.ipcn-card`, `.ipcn-card-media`, `.ipcn-card-noimg`, `.ipcn-card-title`, `.ipcn-card-date`. Sem imagem, o marcador é um `span` com a classe `ipcn-card-noimg` e a palavra `IPCN`, mantendo a altura dos vizinhos. No cartão da agenda, a data mostrada é a `data_evento`.
- **Modelo de conteúdo:** `agenda-ipcn` é categoria de topo, separada de `noticias`. A página da Agenda tem como conteúdo um único bloco `ipcn/agenda`. O archive de `agenda-ipcn` redirecciona para essa página: não é superfície pública nem indexável.
- **Código:** o bloco vive em `inc/agenda-block.php`; a meta e a caixa em `inc/content-model.php`. `functions.php` só carrega.
- **Formatos e stack:** datas de meta em `Y-m-d`, mostradas em `j \d\e M \d\e Y`. PHP >= 8.1 e WordPress >= 6.4, sem dependências externas além das fontes do Google, sem build step.
- **Nota de dependência (correct-course 2026-09-29):** as histórias 2.2 e 2.3 são donas de reconstruir a agenda com o bloco e o cartão. O remendo actual da agenda fica **substituído** por elas e não deve ser reimplementado sem ler esta nota.

## UX & Interaction Patterns

- **Cartão de agenda (`card-agenda`):** o dia é o elemento de maior peso tipográfico; seguem-se nome e lugar.
- **Na Home**, a secção Agenda mostra até três encontros por vir.
- **Vazio (Agenda e Home):** a copy é "A agenda está sendo montada", com ligação ao canal público do IPCN.
- **Cabeçalho da Agenda:** a página abre com a `page-hero` — que carrega o `h1` — e o conteúdo retoma no `h2`, sem repetir o título.
- **Interacção:** tocar para agir; filtros e páginas são ligações reais (o botão de voltar funciona); navegação mobile não sequestra o ecrã. Banidos: carrossel automático, animação na abertura, pop-up de subscrição, auto-play.
- **Foco:** anel duplo (base interior 2px, navy exterior 2px, 2px de afastamento), visível sobre base, subtle, ocre, navy, terracota e rodapé; o conteúdo focado nunca fica escondido pela barra de cookies.
- **Voz:** microcopy em português do Brasil, frases completas, sem entusiasmo de marketing. Rótulo "Próximos encontros".

## Cross-Story Dependencies

- **2.1 → 2.2:** o bloco depende da meta `data_evento` e da sua entrada no editor.
- **2.2 → 2.3:** as duas superfícies (Home e página da Agenda) montam o bloco.
- **2.3 → 2.4:** o redirect do archive aponta para a página criada em 2.3.
- **Épico 1 (entrada):** o cartão (`ipcn-card`) e a Home em cinco blocos, incluindo a secção Agenda, são entregues pelo Épico 1. O Épico 2 consome o cartão e a `page-hero`, não os recria.
- **Épico 4 (a jusante):** a publicação é manual e depende do veredicto da Contratante; nenhum artefacto deste épico publica sozinho.
