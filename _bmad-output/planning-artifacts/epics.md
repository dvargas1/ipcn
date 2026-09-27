---
stepsCompleted: ["step-01", "step-02", "step-03", "step-04"]
inputDocuments:
  - _bmad-output/specs/spec-ipcn/SPEC.md
  - _bmad-output/specs/spec-ipcn/glossary.md
  - _bmad-output/planning-artifacts/prds/prd-ipcn-2026-09-24/prd.md
  - _bmad-output/planning-artifacts/prds/prd-ipcn-2026-09-24/addendum.md
  - _bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/DESIGN.md
  - _bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/EXPERIENCE.md
  - _bmad-output/planning-artifacts/architecture/architecture-ipcn-2026-09-24/ARCHITECTURE-SPINE.md
---

# IPCN - Epic Breakdown

## Overview

Este documento decompõe os requisitos do SPEC, do PRD, do par de UX e da espinha de arquitectura em épicos e histórias implementáveis. O tema `ipcn-fse` já existe: isto é trabalho sobre código real, não uma construção de raiz.

Notas de estrutura:

- **Não há starter template.** O tema existe, com 619 linhas em `functions.php`; o primeiro passo é arrumação, não scaffold. Nenhuma história cria infraestrutura de raiz.
- **Sobreposição de ficheiros avaliada e aceite.** O Épico 1 e o Épico 3 tocam ambos `inc/forms.php` e `inc/cookie-bar.php`, e o Épico 2 usa o cartão que o Épico 1 cria. A consolidação num só épico foi considerada e rejeitada: o Épico 1 entrega a leitura pública e o Épico 3 entrega as interacções — resultados distintos, verificados separadamente — e juntá-los daria um épico de dezassete histórias impossível de rever como unidade. A partilha de `style.css` é incidental: uma folha de estilo, não o mesmo componente ponta a ponta.
- **Entidades criadas só quando a história precisa.** A única criação de dados é a meta `data_evento`, na história 2.1, onde é usada. Nenhuma história cria tabelas ou tipos de conteúdo por antecipação.

## Requirements Inventory

### Functional Requirements

FR1: O Visitante lê a Home, Notícias, a Agenda, as páginas institucionais e o Acervo sem autenticação.
FR2: A Home apresenta o instituto em cinco blocos — hero, Notícias, Acervo, Agenda e faixa de contacto.
FR3: O Visitante abre uma Notícia em leitura longa e volta à lista.
FR4: O Visitante lista o Acervo, filtra por Tema e abre um Item.
FR5: O Visitante alcança Home, Notícias, Acervo e Agenda no primeiro nível, mais as pílulas `Associe-se` e `Apoia-se`; Secções e páginas institucionais têm endereço próprio.
FR6: O Visitante vê os próximos encontros, com data, nome e lugar quando existe, na Home e numa página de Agenda.
FR7: Sem encontros por vir, o Visitante encontra um estado vazio digno e o canal público do IPCN.
FR8: A Candidata envia nome, e-mail e telefone, e o pedido chega a `contato@ipcnbrasil.org` sem criar conta.
FR9: O Visitante envia nome, e-mail e mensagem pela página Fale conosco, para o mesmo destino.
FR10: A página Apoia-se mostra como contribuir por PIX, sem checkout e sem conta.
FR11: O Visitante aceita, recusa o não-essencial ou gere preferências antes de analítica ou marketing dispararem.
FR12: O site lê-se como instituto e cumpre WCAG 2.2 AA.
FR13: As superfícies funcionam no telemóvel e no computador, sem app.
FR14: A Criadora cria e submete Notícia e Item de Acervo, que ficam por aprovar. *(fase seguinte)*
FR15: O Aprovador publica ou devolve uma Notícia ou um Item, com a decisão registada. *(fase seguinte)*
FR16: O Administrador mantém o site e as contas sem ser, por definição, quem aprova texto. *(fase seguinte)*
FR17: O site público não substitui a produção sem o veredicto da Contratante.
FR18: A publicação em produção é feita pelo Daniel no hPanel, e ninguém a automatiza.

### NonFunctional Requirements

NFR1: WCAG 2.2 AA em texto, controlos, foco, reflow a 320px e alvos de toque de 44px.
NFR2: Erro de formulário identificado por texto além da cor, ligado ao campo, com resumo no topo.
NFR3: Ajuda consistente — contacto e política de privacidade alcançáveis de qualquer página, no rodapé.
NFR4: Lighthouse ≥ 90 em mobile nas superfícies da primeira entrega.
NFR5: LGPD no que a entrega colhe: nome, e-mail e telefone para contacto humano; nada de dados sensíveis; consentimento de cookies separado do pedido de associação.
NFR6: Sem pipeline para produção. Segredos, `wp-config` e cópias de segurança fora do repositório. Não se pede nem se guarda password de produção.
NFR7: Formulários sem nonce, porque o serviço de cache entrega HTML antigo; protecção por honeypot mais verificação de origem, e um cabeçalho ausente não bloqueia pessoas reais.
NFR8: Os endereços existentes mantêm-se; o plugin de SEO instalado mantém-se.
NFR9: Sem build step, sem suite de testes e sem CI; a verificação é `php -l` sobre o PHP alterado e observação no browser, depois de purgar a cache.
NFR10: Não se acrescenta tema ou plugin pago para cumprir o que os blocos nativos do WordPress já cumprem.
NFR11: Web, telemóvel primeiro, piso de 320px; sem app e sem modo escuro nesta entrega.
NFR12: PHP >= 8.1 e WordPress >= 6.4 declarados no tema; sem dependências externas além das fontes do Google.

### Additional Requirements

Da espinha de arquitectura. Não há starter template: o tema existe e o trabalho ratifica as suas convenções.

- AR1 (AD-1): O markup é block markup escrito em ficheiros; PHP nunca escreve comentários de bloco como literais. Duas excepções: passar o markup de um pattern registado ao interpretador de blocos, e um `render_callback` que imprime HTML.
- AR2 (AD-2): Listar conteúdo é `core/query`. Não se cria `WP_Query` novo em shortcodes; a única excepção é a agenda.
- AR3 (AD-3): O cartão é um pattern registado (`ipcn/card` e `ipcn/card-feature`), com as casas fixadas — imagem, marcador de ausência, etiqueta por superfície, título e data. O marcador é um `span` com a classe `ipcn-card-noimg`. `ipcn-card-v2` está retirada.
- AR4 (AD-4): O comportamento vive em `inc/` — `setup.php`, `content-model.php`, `listings.php`, `forms.php`, `cookie-bar.php`, `agenda-block.php`. `functions.php` é um carregador e não acumula lógica.
- AR5 (AD-5): O CSS do tema vive só em `style.css`, com a única excepção da propriedade `--ipcn-hero-bg`. Nenhum PHP do tema emite `<style>`.
- AR6 (AD-6): O mu-plugin é congelado, com a excepção autorizada: os três blocos só-Divi passam a ser emitidos apenas quando o tema activo é o Divi.
- AR7 (AD-7): A selecção de conteúdo é por slug, nunca por `term_id`. A referência da navegação e os ids de páginas são conteúdo na base de dados, não código.
- AR8 (AD-8): Secções são categorias filhas de `noticias`, com o texto de apresentação na descrição do termo. `agenda-ipcn` é categoria de topo. `acervo_ipcn` e `tema_acervo` (`/temas/`) são separados de `post`. `/temas/<slug>` é servido pelo template do archive.
- AR9 (AD-9): A Agenda é o bloco dinâmico `ipcn/agenda`, com um atributo `limite` inteiro (por omissão 3; a página da Agenda usa -1). A data de um encontro é só `data_evento`, sem alternativa por `post_date`.
- AR10 (AD-10): `data_evento` é registada como meta de `post`, com caixa de meta própria e `input type="date"`. Guardada em `Y-m-d`, validada ao guardar.
- AR11 (AD-11): O contrato dos formulários — acções `ipcn_assoc` e `ipcn_contact`; campos `ipcn_nome`, `ipcn_email`, `ipcn_tel`, `ipcn_msg`, `ipcn_hp`; redirect com `cadastro` e `contato`, valores `ok` e `erro`; destino `contato@ipcnbrasil.org`. Retenção dos valores num transient indexado por token no URL.
- AR12 (AD-12): O deploy é manual e só o ambiente de revisão é destino. O mu-plugin só se deploya quando ele próprio muda, e nunca para o staging Divi.
- AR13 (AD-13): Um script no repositório corre `php -l` em todo o PHP alterado e falha se algum não compilar.
- AR14 (AD-14): O cartão tem um caminho único de render — dentro de `core/post-template` nas listagens, por `render_block()` com o `postId` no contexto fora de um loop.
- AR15 (AD-15): A vitrine do Acervo na Home são dois `core/query` irmãos: um com `perPage: 1` para o destaque, outro com `perPage: 2` e `offset: 1` para os compactos.
- AR16: Envelope operacional — Hostinger, LiteSpeed com HCDN, artefactos entregues por tar e scp com `--strip-components=3`, purga com `litespeed-purge all`. A produção é publicada à mão no hPanel.
- AR17: O par de UX é vinculativo para o desenho e o comportamento; as duas espinhas vencem sobre qualquer mock.

### UX Design Requirements

Do par `DESIGN.md` + `EXPERIENCE.md`.

UX-DR1: Tokens de cor em `theme.json` — navy, ink, body, muted, subtle, base, ocre, ocre-hover, terracota, chumbo, text-muted, error, success — com a razão de contraste declarada por par usado.
UX-DR2: Escala tipográfica de onze papéis (display, headline, card-title, wordmark, reading, body, body-small, eyebrow, label, caption, button) em três famílias, carregadas do Google por PHP.
UX-DR3: Tokens de raio (`sm` 6px, `md` 12px, `lg` 14px, `full` 9999px) e de espaçamento (margem 20px, secção 96/48, hero 120/72, rodapé 56, medida 720, largura 1100).
UX-DR4: Componentes visuais especificados: `button-primary`, `button-secondary`, `button-pill`, `button-pill-outline`, `cookie-action`, `link`, `card`, `card-feature`, `card-agenda`, `contact-band`, `eyebrow`, `tag`, `theme-filter`, `pagination`, `accordion`, `input`, `error-text`, `success-text`, `header`, `footer`, `cookie-bar`, `focus-ring`.
UX-DR5: A barra de cookies passa a **opaca**, e a terceira acção deixa de ser um fantasma sobre navy — usa `cookie-action`, ocre com contorno base.
UX-DR6: A borda dos campos passa a `text-muted` (4.76:1); `muted` fica só para cartões.
UX-DR7: O foco passa a **anel duplo** — `base` interior de 2px e `navy` exterior de 2px, com 2px de afastamento — visível sobre base, subtle, ocre, navy, terracota e rodapé.
UX-DR8: O rodapé ganha a ligação à Política de privacidade, hoje ausente, para cumprir a ajuda consistente.
UX-DR9: Composição da Home: cinco blocos por ordem, com a vitrine do Acervo a usar `card-feature` e a grelha compacta a usar `card`. Nunca duas grelhas iguais seguidas.
UX-DR10: O título do hero perde o número de anos e não volta a contê-lo.
UX-DR11: Navegação de primeiro nível curta (Home, Notícias, Acervo, Agenda mais as pílulas), com as Secções dentro de Notícias e as institucionais no rodapé.
UX-DR12: Sem campo de busca nesta entrega.
UX-DR13: Estados vazios nomeados: Home sem Notícias, Notícias vazias, Acervo vazio, Tema sem itens, Agenda sem próximos.
UX-DR14: Estado de ausência de imagem: marcador tipográfico `IPCN`, sem encolher o cartão em relação aos vizinhos.
UX-DR15: Estados de formulário: em envio, aceite, falhado com campos mantidos e resumo no topo, e honeypot com o mesmo erro — nunca silêncio.
UX-DR16: Estado do QR de PIX vencido ou em falta: diz que o código está em actualização em vez de mostrar um código morto.
UX-DR17: Escolha de cookies pendente: nada de analítica ou marketing dispara antes dela, e as três acções têm igual proeminência.
UX-DR18: Estado de foco sob a barra de cookies: afastamento de scroll reservado, o conteúdo focado nunca fica escondido.
UX-DR19: 404 com uma frase e um caminho de volta ao início.
UX-DR20: Piso de acessibilidade: skip link como primeiro elemento focável, `autocomplete` nos campos, `aria-invalid` e `aria-describedby` nos erros, anúncio do resultado do envio, texto alternativo, movimento reduzido, ordem de foco sem armadilhas, reflow a 320px.
UX-DR21: Primitivas de interação: tocar para agir, filtros e paginação como ligações reais, navegação mobile que não sequestra o ecrã.
UX-DR22: Superfícies banidas: carrossel automático, animação na abertura, contador de artigos, pop-up a pedir subscrição, auto-play de vídeo.
UX-DR23: Microcopy em português do Brasil, com a tabela de voz: "A agenda está sendo montada", "Recebemos seu pedido", "Não foi possível enviar agora", sem entusiasmo de marketing.
UX-DR24: Pontos de viragem: 320px piso, 375px revisão, 782px viragem, 1440px computador; grelhas de três colunas no computador e uma no telemóvel.
UX-DR25: As cinco jornadas UJ-1 a UJ-5 como narrativas com protagonista nomeado e batida de clímax, cada uma com o seu caminho de falha.

### FR Coverage Map

FR1: Epic 1 — cobertura estrutural. Nenhuma história introduz autenticação; a ausência de conta verifica-se na história 4.3, que percorre as superfícies no ambiente de revisão.
FR2: Epic 1 — história 1.5: a Home em cinco blocos, com a vitrine do Acervo.
FR3: Epic 1 — cobertura por desenho: o template de leitura já existe e funciona; o que faltava, a medida de leitura e o reflow, está na história 1.9.
FR4: Epic 1 — Acervo, filtro por Tema e Item.
FR5: Epic 1 — menu de primeiro nível, Secções e páginas institucionais.
FR6: Epic 2 — próximos encontros, na Home e na página da Agenda.
FR7: Epic 2 — estado vazio da Agenda com o canal público.
FR8: Epic 3 — pedido de associação.
FR9: Epic 3 — Fale conosco.
FR10: Epic 3 — Apoia-se com o QR honesto.
FR11: Epic 3 — consentimento de cookies.
FR12: Epic 1 — identidade visual e acessibilidade.
FR13: Epic 1 — telemóvel até 320px.
FR14: fase seguinte — fora deste lote, por decisão do SPEC.
FR15: fase seguinte — fora deste lote.
FR16: fase seguinte — fora deste lote.
FR17: Epic 4 — veredicto antes da produção.
FR18: Epic 4 — produção manual, fora do alcance deste repositório.

## Epic List

### Epic 1: O site público do IPCN, legível e acessível

O Visitante percorre a Home, uma Notícia, as Secções, o Acervo e um Tema no telemóvel, sem conta, com a identidade do instituto e sem barreiras de leitura. Inclui a arrumação que o torna estável — um cartão só, o markup em patterns, o comportamento em `inc/`, o CSS num ficheiro — e as correcções de contraste e de foco.
**FRs covered:** FR1, FR2, FR3, FR4, FR5, FR12, FR13
**NFRs:** NFR1, NFR3, NFR4, NFR8, NFR9, NFR11, NFR12
**ARs:** AR1 – AR8, AR14, AR15, AR17

### Epic 2: A Agenda, do editor ao Visitante

A equipa marca um encontro com uma data, num campo próprio, e o Visitante vê os próximos encontros na Home e na página da Agenda. Sem encontros por vir, o vazio explica-se e aponta o canal público.
**FRs covered:** FR6, FR7
**NFRs:** NFR2, NFR3, NFR9
**ARs:** AR2, AR3, AR8, AR9, AR10, AR14

### Epic 3: Falar com o instituto, e decidir sobre os próprios dados

A Candidata pede associação e sabe que o pedido chegou; o Visitante escreve uma mensagem; quem quer apoiar vê como. E o Visitante decide sobre cookies antes de algo disparar.
**FRs covered:** FR8, FR9, FR10, FR11
**NFRs:** NFR2, NFR3, NFR5, NFR7, NFR9
**ARs:** AR1, AR11, AR17

### Epic 4: Aprovar e publicar sem surpresas

A Contratante revê o site no ambiente de revisão e diz que pode ir para produção; o Daniel publica à mão no hPanel. Nenhum artefacto deste repositório toca em produção, e o tempo de revisão não é julgado por HTML em cache.
**FRs covered:** FR17, FR18
**NFRs:** NFR4, NFR6, NFR8, NFR9
**ARs:** AR12, AR13, AR16

---

## Epic 1: O site público do IPCN, legível e acessível

O Visitante lê o IPCN sem conta, no telemóvel, com identidade consistente e sem barreiras. Cada história deixa uma superfície completa e verificável no ambiente de revisão.

### Story 1.1: Verificação de sintaxe antes de mexer no tema

Como quem faz deploy,
quero verificar a sintaxe de todo o PHP alterado antes de enviar,
para não partir o ambiente de revisão com um erro que só se vê no browser.

**Acceptance Criteria:**

**Dado** um conjunto de ficheiros PHP alterados no tema ou nos mu-plugins,
**Quando** corro o script de verificação,
**Então** o script corre `php -l` em cada um e falha com o nome do ficheiro e a linha se algum não compilar.
**E** o script não altera ficheiros nem faz deploy.
**E** as instruções de uso estão escritas no próprio repositório.

### Story 1.2: O comportamento sai de `functions.php` para `inc/`

Como quem mantém o site,
quero o comportamento repartido por ficheiros de preocupação,
para acrescentar uma secção sem tocar num ficheiro de 619 linhas.

**Acceptance Criteria:**

**Dado** o tema como está, com setup, formulários, consultas, modelo de conteúdo e cookies num só ficheiro,
**Quando** o comportamento é repartido,
**Então** existem `inc/setup.php`, `inc/content-model.php`, `inc/listings.php`, `inc/forms.php` e `inc/cookie-bar.php`, e `functions.php` só os carrega e faz o setup do tema.
**E** nenhum ficheiro de `inc/` regista o mesmo hook que outro.
**E** nenhum PHP do tema emite `<style>`; o CSS vive em `style.css`, e a única inline é a propriedade `--ipcn-hero-bg`.
**E** o site em revisão continua a comportar-se exactamente como antes: mesma home, mesmos formulários, mesmos cookies.

### Story 1.3: Um cartão só, com as casas fixadas

Como Visitante,
quero que os cartões se leiam da mesma maneira em toda a parte,
para distinguir notícia de peça de acervo sem me perder.

**Acceptance Criteria:**

**Dado** o tema com três implementações de cartão,
**Quando** o cartão passa a pattern registado,
**Então** existem `patterns/ipcn-card.php` e `patterns/ipcn-card-feature.php`, registados com `Inserter: false`.
**E** as casas de cada um são as da tabela do `DESIGN.md`: imagem 16:9 no compacto e 4:3 no destaque, etiqueta de `category` em Notícias e Secção, etiqueta de `tema_acervo` no Acervo e no Tema.
**E** sem imagem de destaque, o cartão mostra um `span` com a classe `ipcn-card-noimg` e a palavra `IPCN`, e mantém a altura dos vizinhos.
**E** nenhum markup de cartão é escrito à mão em PHP, e as listagens usam o cartão dentro de `core/post-template`.

### Story 1.4: A identidade e o foco

Como Visitante,
quero ler e navegar o site com contraste suficiente e foco sempre visível,
para o usar no telemóvel à luz do dia e com o teclado.

**Acceptance Criteria:**

**Dado** o `theme.json` e o `style.css` actuais,
**Quando** os tokens e o foco são aplicados,
**Então** os treze tokens de cor estão declarados e cada par que carrega texto tem a razão de contraste declarada no `DESIGN.md`.
**E** o foco de todos os elementos interactivos é um anel duplo — `base` de 2px por dentro e `navy` de 2px por fora, com 2px de afastamento — visível sobre base, subtle, ocre, navy, terracota e o rodapé.
**E** nenhum `outline: none` existe sem este substituto.
**E** o texto claro sobre ocre não aparece em lado nenhum.

### Story 1.5: A Home em cinco blocos, com a vitrine do Acervo

Como Visitante,
quero que a página de entrada me diga o que o IPCN é e o que tem,
para decidir em segundos se fico.

**Acceptance Criteria:**

**Dado** a Home actual, com duas grelhas de três iguais e um hero datado,
**Quando** a composição é aplicada,
**Então** a Home tem cinco blocos por esta ordem: hero, Notícias, Acervo, Agenda e faixa de contacto.
**E** o título do hero não contém número de anos.
**E** com as imagens bloqueadas, o primeiro ecrã continua a dizer o que o IPCN é.
**E** a vitrine do Acervo são dois `core/query` irmãos — um com `perPage: 1` para o `ipcn-card-feature`, outro com `perPage: 2` e `offset: 1` para dois `ipcn-card` — e não repete as Notícias.
**E** a faixa de contacto mostra endereço, telefone e e-mail como texto, não como imagem.

### Story 1.6: Notícias puras e o hub das Secções

Como Visitante,
quero que a secção de Notícias mostre notícias e me deixe entrar nas secções,
para não encontrar uma pauta de agenda onde esperava uma notícia.

**Acceptance Criteria:**

**Dado** que a secção de Notícias da Home tem hoje conteúdo que não é notícia,
**Quando** a listagem é corrigida,
**Então** a secção mostra só publicações da categoria `noticias`, filtrada por slug e nunca por `term_id`.
**E** a página Notícias funciona como hub e dá acesso a cada Secção.
**E** cada Secção é categoria filha de `noticias`, tem endereço próprio, responde, e o seu título e descrição vêm da descrição do termo.
**E** uma Secção sem conteúdo explica-se em vez de mostrar uma grelha partida.

### Story 1.7: O Acervo e os Temas, públicos e filtráveis

Como Visitante,
quero folhear o Acervo e filtrar por Tema,
para encontrar uma peça de pesquisa sem criar conta.

**Acceptance Criteria:**

**Dado** o Acervo com poucos itens,
**Quando** a listagem é aplicada,
**Então** o Acervo é público por inteiro: sem "últimos N" e sem 403.
**E** filtrar por Tema muda o endereço, o botão de voltar funciona, e `/temas/<slug>` é servido pelo template do archive com o hero a ler a descrição do termo.
**E** um Tema sem itens explica-se e oferece o Acervo inteiro.
**E** um Item de Acervo distingue-se de uma Notícia pela etiqueta de Tema; sem Tema atribuído, mostra a ausência.
**E** a paginação só aparece com mais de uma página.

### Story 1.8: Menu de primeiro nível curto e rodapé completo

Como Visitante,
quero um menu que não me obrigue a ler dezasseis destinos e um rodapé com o essencial,
para navegar e sair informado.

**Acceptance Criteria:**

**Dado** o menu actual, guardado na base de dados, e um rodapé sem ligação à política,
**Quando** a navegação é ajustada,
**Então** o primeiro nível tem Home, Notícias, Acervo e Agenda, mais as pílulas `Associe-se` e `Apoia-se`, e nada mais.
**E** as Secções alcançam-se a partir de Notícias e as páginas institucionais a partir do rodapé.
**E** o rodapé inclui a ligação à Política de privacidade, que hoje não existe.
**E** o contacto e a política estão alcançáveis a partir de qualquer página.

### Story 1.9: Leitura no telemóvel até 320px

Como Visitante num telemóvel pequeno,
quero ler sem arrastar o ecrã para o lado,
para não desistir a meio de uma notícia.

**Acceptance Criteria:**

**Dado** as superfícies do Épico 1,
**Quando** são vistas a 320px de largura,
**Então** não há scroll horizontal em nenhuma delas.
**E** todo o controlo tem alvo de toque de pelo menos 44px, incluindo paginação, filtros e o cabeçalho do acordeão.
**E** a leitura longa mantém a medida definida no `DESIGN.md` e não obriga a scroll horizontal a 375px.
**E** nenhuma das superfícies banidas aparece: sem carrossel, sem pop-up, sem auto-play.

### Story 1.10: Os estados vazios e a página 404

Como Visitante,
quero que a ausência de conteúdo se explique,
para não pensar que o site está avariado.

**Acceptance Criteria:**

**Dado** uma superfície sem conteúdo,
**Quando** a página carrega,
**Então** a Home sem Notícias diz "Em breve, novidades por aqui." e mantém a secção.
**E** Notícias ou Secção vazias explicam e oferecem as restantes Secções.
**E** o Acervo vazio diz "Em breve, novos itens do acervo.".
**E** a página 404 diz uma frase e dá um caminho de volta ao início.

### Story 1.11: A copy acentuada em português do Brasil

Como Visitante,
quero ler português correcto,
para confiar no instituto que o escreveu.

**Acceptance Criteria:**

**Dado** o texto de interface actual, com palavras sem acento,
**Quando** a revisão de copy é feita,
**Então** todo o texto de interface está em português do Brasil e acentuado correctamente, incluindo os estados vazios e os rótulos.
**E** a microcopy segue a tabela de voz do `EXPERIENCE.md`, sem entusiasmo de marketing.

### Story 1.12: O mu-plugin deixa de servir o site FSE

Como quem mantém o site,
quero que o mu-plugin só emita os blocos Divi quando o tema Divi está activo,
para o site FSE não receber CSS que não usa nem uma fonte que não existe.

**Acceptance Criteria:**

**Dado** que o mu-plugin emite três blocos `<style>` no `wp_head` — `#ipcn-etmodules-fix`, `#ipcn-form-style` e `#ipcn-footer-logo-fix` — e serve as duas instalações,
**Quando** o guard é aplicado,
**Então** os três blocos são emitidos apenas quando o tema activo é o Divi.
**E** nada mais no ficheiro muda: a remoção do `generator`, o `xmlrpc_enabled` e a remoção do `X-Pingback` continuam a valer para todas as instalações.
**E** o HTML do site FSE deixa de conter as três etiquetas, e o pedido ao ficheiro de fonte Divi desaparece.
**E** o ficheiro é deployado no `stagingredesign` e **não** no staging Divi, onde o comportamento se mantém idêntico sem ele.

---

## Epic 2: A Agenda, do editor ao Visitante

A equipa marca um encontro com data e o Visitante vê-o. A Agenda é o único lugar do tema que corre a sua própria consulta, e fá-lo por um bloco próprio.

### Story 2.1: O campo de data do encontro no editor

Como quem publica no IPCN,
quero marcar a data de um encontro num campo próprio,
para não ter de escrever uma chave de metadados à mão nem acertar no formato.

**Acceptance Criteria:**

**Dado** que hoje a data do encontro só existe como meta lida pelo tema, sem entrada no editor,
**Quando** o campo é registado,
**Então** `data_evento` está registada como meta de `post` e aparece no editor como uma caixa de meta com `input type="date"`.
**E** o valor é guardado em `Y-m-d`.
**E** um valor noutro formato é rejeitado e não é gravado, sem apagar um valor válido anterior.
**E** deixar o campo vazio é permitido.

### Story 2.2: O bloco `ipcn/agenda`

Como Visitante,
quero ver os próximos encontros,
para saber se há algo esta semana.

**Acceptance Criteria:**

**Dado** que o `core/query` não sabe comparar datas de metadados,
**Quando** o bloco da agenda é registado,
**Então** existe um bloco dinâmico com o nome `ipcn/agenda`, registado no servidor com um `render_callback`, e um único atributo `limite`, inteiro, por omissão `3`.
**E** a consulta devolve apenas publicações da categoria `agenda-ipcn` com `data_evento` igual ou posterior a hoje, por ordem crescente de data.
**E** um encontro sem `data_evento` preenchido não aparece.
**E** cada encontro é renderizado pelo pattern do cartão, com o `postId` no contexto — nenhum markup de cartão é escrito à mão, e nenhum título de página aparece por engano no lugar do título do encontro.

### Story 2.3: A Agenda na Home e na sua página, com o vazio digno

Como Visitante,
quero ver os próximos encontros na entrada e a lista completa numa página,
para não ter de percorrer uma página inteira na home.

**Acceptance Criteria:**

**Dado** o bloco `ipcn/agenda` disponível,
**Quando** as duas superfícies são montadas,
**Então** a Home mostra o bloco com `limite: 3` dentro da secção Agenda.
**E** existe uma página de Agenda cujo conteúdo é um único bloco `ipcn/agenda` com `limite: -1`.
**E** sem encontros por vir, ambas mostram "A agenda está sendo montada", a ligação ao canal público do IPCN, e nenhum encontro passado.
**E** um encontro sem lugar aparece com dia e nome, sem lugar inventado.

### Story 2.4: O archive de `agenda-ipcn` deixa de listar encontros

Como Visitante,
quero que o arquivo de agenda não me mostre encontros passados como se fossem próximos,
para não me deslocar a um evento que já aconteceu.

**Acceptance Criteria:**

**Dado** que `/category/agenda-ipcn/` responde hoje com o template do archive,
**Quando** a regra é aplicada,
**Então** o archive da categoria `agenda-ipcn` redirecciona para a página da Agenda.
**E** não é indexável nem é uma superfície pública.
**E** nenhuma outra listagem pública mostra encontros passados como próximos.

---

## Epic 3: Falar com o instituto, e decidir sobre os próprios dados

Os dois formulários, o apoio manual e o consentimento. As peças do contrato dos formulários estão fixadas, e o redirect deixa de perder o que a pessoa escreveu.

### Story 3.1: Associe-se, com o contrato fixado

Como Candidata,
quero pedir associação com os meus dados mínimos,
para fazer parte sem criar conta.

**Acceptance Criteria:**

**Dado** que o formulário existe e o contrato das suas peças não está fixado,
**Quando** o contrato é aplicado,
**Então** a acção é `ipcn_assoc`, os campos são `ipcn_nome`, `ipcn_email`, `ipcn_tel` e `ipcn_hp`, e o redirect vai para `/associe-se/` com o argumento `cadastro`.
**E** o pedido é enviado para `contato@ipcnbrasil.org`.
**E** a borda dos campos é `text-muted` e não `muted`, e cada campo tem etiqueta visível e associada.
**E** não nasce utilizador nenhum.

### Story 3.2: O envio falhado não perde o que a pessoa escreveu

Como Candidata,
quero poder corrigir o que faltou sem reescrever tudo,
para não desistir a meio.

**Acceptance Criteria:**

**Dado** um envio inválido,
**Quando** o handler rejeita,
**Então** guarda os valores submetidos num transient de vida curta, indexado por um token que vai no URL do redirect.
**E** o formulário lê o token, re-preenche os campos, escreve um resumo de erro no topo e liga cada erro ao campo por `aria-invalid` e `aria-describedby`.
**E** o transient é apagado depois de usado.
**E** um envio bem-sucedido confirma na página e não confirma nada quando falha.

### Story 3.3: A origem é verificada nos formulários

Como quem mantém o site,
quero que a protecção declarada no código exista de facto,
para não confiar numa defesa que não está lá.

**Acceptance Criteria:**

**Dado** que o comentário do handler promete verificação de referer e o código não a tem,
**Quando** a verificação é implementada,
**Então** um `Origin` ou `Referer` presente que não corresponda ao host do site faz a submissão ser rejeitada com o estado de erro normal.
**E** com ambos os cabeçalhos ausentes, a submissão é aceite e fica protegida só pelo honeypot.
**E** um honeypot preenchido produz o mesmo estado de erro, nunca silêncio.
**E** o comentário do handler descreve o que o código faz.

### Story 3.4: Fale conosco

Como Visitante,
quero escrever uma mensagem ao instituto,
para perguntar uma coisa que não encontrei no site.

**Acceptance Criteria:**

**Dado** o contrato fixado em Story 3.1,
**Quando** o segundo formulário é alinhado,
**Então** a acção é `ipcn_contact`, os campos são `ipcn_nome`, `ipcn_email`, `ipcn_msg` e `ipcn_hp`, e o redirect vai para `/fale-conosco/` com o argumento `contato`.
**E** o assunto da mensagem não se confunde com o de um pedido de associação.
**E** o destino é o mesmo: `contato@ipcnbrasil.org`.
**E** a página está alcançável a partir de qualquer outra.

### Story 3.5: Apoia-se, com o QR honesto

Como quem quer apoiar,
quero saber como contribuir e não encontrar um código morto,
para não desconfiar do instituto.

**Acceptance Criteria:**

**Dado** que o QR pode estar vencido ou em falta,
**Quando** a página é apresentada,
**Então** mostra como contribuir por PIX, sem checkout e sem conta.
**E** sem botão que cobre cartão ou recorrência.
**E** com o QR vencido ou ausente, diz que o código está em actualização e oferece o contacto, em vez de mostrar um código morto.

### Story 3.6: Cookies com escolha real

Como Visitante,
quero decidir sobre cookies antes de algo disparar,
para não ser rastreado sem o saber.

**Acceptance Criteria:**

**Dado** a barra de cookies actual, com a terceira acção em fantasma sobre navy,
**Quando** a correcção é aplicada,
**Então** a barra tem fundo navy **opaco** e as três acções usam o mesmo tratamento `cookie-action`, com igual proeminência e nenhuma pré-seleccionada.
**E** sem escolha registada, analítica e marketing não disparam.
**E** a escolha persiste no browser e pode ser revista.
**E** a barra não obscurece o conteúdo nem o foco do teclado: há afastamento de scroll reservado, e a barra cede o passo ao foco.

---

## Epic 4: Aprovar e publicar sem surpresas

O fecho da entrega: o que chega ao ambiente de revisão, como se verifica, e a regra de que produção só muda depois do veredicto.

### Story 4.1: O procedimento de deploy escrito e repetível

Como quem faz deploy,
quero o procedimento escrito com os comandos exactos,
para não depender da memória nem inventar o caminho remoto.

**Acceptance Criteria:**

**Dado** que o procedimento existe só em prosa no `README`,
**Quando** é escrito,
**Então** os comandos estão registados no repositório: criar o pacote do tema, enviá-lo, extrair com `--strip-components=3` no tema do redesign, e purgar com `litespeed-purge all`.
**E** o caminho remoto está confirmado e escrito.
**E** o procedimento diz que o mu-plugin só se deploya quando ele próprio muda, e nunca para o staging Divi.
**E** o procedimento diz que o HTML só é prova depois da purga.

### Story 4.2: A produção está fora do alcance deste repositório

Como Contratante,
quero ter a garantia de que nada deste trabalho publica sozinho,
para não acordar com o site mudado.

**Acceptance Criteria:**

**Dado** que a produção é publicada à mão no hPanel pelo Daniel,
**Quando** o repositório é revisto,
**Então** não existe pipeline, script nem hook que publique em produção.
**E** não há credenciais, `wp-config` nem cópias de segurança versionadas.
**E** as superfícies versionadas são apenas o tema e os mu-plugins.
**E** o veredicto da Contratante é condição da publicação, e está escrito que ele é falado e não deixa artefacto.

### Story 4.3: A checklist de revisão da Contratante

Como Contratante,
quero saber o que percorrer e o que conta como bloqueio,
para dar o veredicto sem depender de quem construiu.

**Acceptance Criteria:**

**Dado** que o veredicto é falado e não tem artefacto formal,
**Quando** a checklist é escrita,
**Então** lista as superfícies a percorrer — Home, uma Notícia, a Agenda, o Acervo, Associe-se e Apoia-se — no telemóvel e no computador.
**E** diz que um bloqueio de leitura no telemóvel é, por si, motivo de devolução.
**E** inclui a verificação de desempenho: Lighthouse ≥ 90 em mobile, medido no ambiente de revisão depois da purga.
**E** confere que nenhum endereço existente mudou: `/`, `/noticias/`, `/acervo/`, `/temas/<slug>/`, `/apoia-se/`, `/associe-se/`, `/fale-conosco/` e `/politica-de-privacidade/` respondem como antes.

---

<!--
Épicos e histórias completos.

Cobertura: FR1–FR5, FR12, FR13 no Épico 1; FR6 e FR7 no Épico 2; FR8–FR11 no Épico 3; FR17 e FR18 no Épico 4.
FR14 a FR16 ficam para a fase seguinte, por decisão registada no SPEC.
Todas as 25 UX-DRs estão cobertas por pelo menos uma história.
-->
