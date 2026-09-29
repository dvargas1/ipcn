---
name: IPCN
status: final
sources:
  - {planning_artifacts}/prds/prd-ipcn-2026-09-24/prd.md
  - DESIGN.md
created: 2026-09-24
updated: 2026-09-29
---

# IPCN — Experience Spine

> Site público do Instituto de Pesquisas das Culturas Negras. Sem login. Telemóvel primeiro. Par de `DESIGN.md` (identidade visual). Onde este documento e o código discordarem, este documento manda; a identidade visual é `DESIGN.md`. Não há mocks nesta entrega — as tabelas e `DESIGN.md` são a especificação.

## Foundation

Web, mobile-first, sem app. Um ambiente de revisão distinto da produção; o público não sabe disso e não deve notar.

Não há sistema de UI nomeado além dos blocos nativos do WordPress e dos tokens de `theme.json`. `DESIGN.md` é a referência de identidade visual; este documento é o comportamento.

**Sem contas.** Nenhuma superfície desta entrega pede autenticação. O Acervo é público por inteiro. `Associe-se` é uma mensagem, não um registo.

A navegação principal **não** vive no tema. Os itens residem no navigation post do WordPress (referência `5358`, na base de dados do redesign). Editar `parts/header.html` muda o aspecto do cabeçalho, não os itens. Quem implementar a arquitectura abaixo edita a navegação, não o HTML.

## Information Architecture

| Superfície | Chega-se por | Serve para |
|---|---|---|
| Home | abertura, logótipo | orientar: quem é o IPCN, últimas Notícias, vitrine do Acervo, próximos encontros, contacto |
| Notícias | menu | hub das Notícias e porta para as Secções |
| Secção (Destaques, Diáspora, Colunistas, Notas, Editorial, Drops, Memórias) | Notícias | grelha de Notícias dessa Secção |
| Notícia | cartão | leitura longa de uma Notícia |
| Acervo | menu, hero, vitrine | lista do Acervo, filtrável por Tema, com paginação |
| Tema | filtro do Acervo | a mesma lista, filtrada por um Tema |
| Item de Acervo | cartão | leitura de uma peça, com convite a associar-se |
| Agenda | menu, Home | lista completa de encontros por vir |
| Quem somos | rodapé | apresentação do instituto |
| Projetos | rodapé | projectos do instituto |
| Fale conosco | Home, rodapé | mensagem para o instituto |
| Associe-se | botão do cabeçalho, hero, Item | pedido de associação |
| Apoia-se | botão do cabeçalho, rodapé | apoio por PIX |
| Política de privacidade | rodapé | privacidade e cookies |
| 404 | endereço errado | voltar ao início |

**Primeiro nível:** Home · Notícias · Acervo · Agenda, mais as pílulas `Associe-se` e `Apoia-se`. As Secções e as páginas institucionais não ocupam o primeiro nível.

**A Home, por ordem.** Cinco blocos, nesta sequência:

1. **Hero** — eyebrow `Desde 1975 · Lapa, Rio de Janeiro`, título, uma linha de substância, e dois CTAs: `Explorar Acervo` (secundário) e `Associe-se` (primário). O título **não** contém número de anos; o eyebrow já dá a idade, o título não caduca.
2. **Notícias** — três cartões compactos. Só Notícias: uma pauta de Agenda aqui é um defeito, não variedade. Ligação `Últimas notícias →` para `/noticias/`.
3. **Acervo** — vitrina, não grelha: uma peça em destaque e duas secundárias, com o Tema visível. Fundo `subtle`. Ligação `Vozes do IPCN →` para `/acervo/`.
4. **Agenda** — até três encontros por vir, ou o estado vazio.
5. **Faixa de contacto** — ocre, com endereço, telefone e e-mail como texto, e botão para Fale conosco.

Notícias e Acervo usam composições diferentes de propósito — cartão compacto contra vitrina. Duas grelhas de três iguais, seguidas, leem-se como dois feeds iguais e fazem a Home parecer um blog.

**Sem busca.** Quem procura uma coisa específica procura pelo menu, pelo hub de Notícias, ou pelo Tema dentro do Acervo. Em assuntos que atravessam Secções, a descoberta depende de o Tema estar preenchido.

**Temas não têm ecrã próprio.** `/temas/<slug>` responde com a lista do Acervo filtrada; não há template de taxonomia separado.

**Um só cabeçalho de página.** As superfícies de entrada — tudo o que não é a Home nem uma leitura (Notícia, Item de Acervo) — abrem com a mesma `page-hero`: Notícias, Secção, Tema, Acervo, Agenda, Quem somos, Projetos, Fale conosco, Associe-se, Apoia-se, Política de privacidade e 404. As três formas que coexistiam a 2026-09-29 — o `post-title` nu do `page.html`, o hero próprio de `/noticias/` e o `[ipcn_archive_hero]` das Secções e Temas — convergem nela. Uma página nunca abre com dois títulos: o `h1` é da `page-hero` e o conteúdo retoma no primeiro `h2`. Nas leituras o título continua editorial, sobre base.

**O rodapé atual não serve esta arquitectura.** Tem três colunas (Instituto · Contato · Redes sociais) e não inclui a Política de privacidade nem uma ligação directa a Fale conosco. A Política de privacidade passa a ser obrigatória no rodapé (FR-9, §15).

## Voice and Tone

Microcopy. A voz da marca vive em `DESIGN.md` → *Brand & Style*. Português do Brasil na interface.

| Faça | Evite |
|---|---|
| "A agenda está sendo montada. Siga o IPCN no Instagram." | "Nenhum evento encontrado." |
| "Recebemos seu pedido. Vamos responder pelo e-mail informado." | "Cadastro realizado com sucesso! Bem-vindo(a)!" |
| "Não foi possível enviar agora. Seus dados continuam aqui — tente de novo." | "Erro 500" |
| "Em breve, novos itens do acervo." | "Nenhum resultado." |
| "Código PIX em atualização. Fale conosco para apoiar agora." | mostrar um QR vencido |
| "Próximos encontros" · "Explorar Acervo" · "Associe-se" | "Desbloqueie o acervo" · "Saiba mais" |
| "IPCN · Quem somos" no eyebrow da `page-hero` | "Instituto · Desde 1975" fora da Home |
| Frases completas, voz institucional. | Entusiasmo de marketing, pontos de exclamação, emoji. |

## Component Patterns

Comportamento. A especificação visual de cada um vive em `DESIGN.md` → *Components*, sob o mesmo nome.

| Componente | Onde | Regras de comportamento |
|---|---|---|
| `header` | todas | Marca em cima, navegação por baixo, centrado. No telemóvel a navegação colapsa e não empurra o conteúdo de forma permanente. É o primeiro destino do skip link. |
| `page-hero` | superfícies de entrada (Home, Notícia e Item de fora) | Abre a página e carrega o `h1`. O eyebrow nomeia a superfície; a linha de apoio sai só quando existe (o `description` do termo, nas Secções e Temas). O conteúdo retoma no primeiro `h2` e nunca repete o título da página. |
| `footer` | todas | Três colunas e uma linha final. O contacto e a liberdade de sair têm de estar alcançáveis de qualquer página, em posição estável. |
| `link` | todas | Navegável por teclado, com foco visível. Distinguido por mais do que cor. |
| `button-primary` | hero, Item de Acervo | Leva a `Associe-se`. É o gesto principal onde aparece. |
| `button-secondary` | faixas claras | Ação alternativa; nunca compete com o primário no mesmo bloco. |
| `button-pill` / `button-pill-outline` | cabeçalho | `Associe-se` preenchido, `Apoia-se` em contorno. Mantêm-se visíveis ao rolar. |
| `cookie-action` | barra de cookies | Cada uma das três ações fecha a barra e persiste a escolha. Igual proeminência; nenhuma é escondida nem pré-selecionada. |
| `cookie-bar` | todas | Aparece só sem escolha registada. Não bloqueia a leitura e cede o passo ao foco do teclado. |
| `card` | Home, Notícias, Secção, Acervo, Tema | Toca → Notícia ou Item. Mostra tag, título e data. Sem imagem, mostra o marcador tipográfico `IPCN`. |
| `card-agenda` | Home, Agenda | Dia, nome e lugar. Sem lugar, não se inventa um. |
| `card-feature` | Home (Acervo) | Vitrine: uma peça em destaque com dois cartões compactos ao lado. Nunca duas grelhas iguais seguidas na mesma página. |
| `contact-band` | Home | Endereço, telefone e e-mail como texto, não como imagem. Leva a Fale conosco. |
| `theme-filter` | Acervo | Escolher um Tema muda a lista e o endereço. É navegação, não estado escondido: dá para partilhar e voltar atrás. |
| `pagination` | Notícias, Secção, Acervo | Numerada. Página atual é texto, não ligação. Só aparece com mais de uma página. |
| `accordion` | Item de Acervo | Fechado por omissão. Abre por teclado e anuncia o estado. |
| `input` | Associe-se, Fale conosco | Etiqueta visível e associada, acima do campo. `autocomplete` preenchido (`name`, `email`, `tel`). Erro ligado ao campo. |
| `error-text` / `success-text` | formulários | Sempre acompanhados de uma palavra, nunca só cor. |
| `eyebrow` | cabeçalho de secção | Marca o ritmo da página; não é interativo. |
| `tag` | cartões, Item | Distingue o tipo de conteúdo. Não é interativo no cartão. |
| `focus-ring` | todas | Foco visível em todos os elementos interativos, nunca removido sem substituto. O conteúdo focado nunca fica sob a barra de cookies. |

## State Patterns

| Estado | Superfície | Tratamento |
|---|---|---|
| Abertura | Home | O primeiro ecrã mostra o que o IPCN é, sem depender de imagem carregada. |
| Página sem linha de apoio | `page-hero` | Eyebrow e título apenas; a faixa encolhe, não reserva o espaço da linha. |
| Home sem Notícias | Home | "Em breve, novidades por aqui." A secção mantém-se, não colapsa. |
| Notícias vazias | Notícias, Secção | Explica que ainda não há conteúdo e oferece as restantes Secções. |
| Acervo vazio | Acervo | "Em breve, novos itens do acervo." |
| Tema sem itens | Tema | Explica que ainda não há peças neste Tema e oferece o Acervo inteiro. |
| Nenhum encontro por vir | Agenda, Home | "A agenda está sendo montada" e ligação ao canal público do IPCN. Nunca lista encontros passados no lugar dos próximos. |
| Encontro sem lugar | Agenda | Mostra dia e nome; o lugar não aparece. |
| Encontro sem data | Agenda | O encontro sem data não conta como próximo. Se estiver listado, aparece sem data em vez de com uma data inventada. |
| Sem imagem de destaque | `card`, `card-feature` | Marcador tipográfico `IPCN`, nunca um bloco vazio nem uma imagem genérica. Na Home, o cartão mantém a altura dos vizinhos. |
| Formulário em envio | formulários | Botão desativado e legível; diz que está a enviar. Nunca um segundo envio por impaciência. |
| Envio aceite | formulários | Confirmação visível, com o que acontece a seguir. |
| Envio falhado | formulários | Erro na mesma página, campos mantidos, resumo de erro no topo e mensagem ligada ao campo, com possibilidade de repetir. |
| Honeypot preenchido | formulários | Mesmo estado de erro de um envio falhado. Nunca silêncio: quem o accionar por engano veria a mensagem desaparecer sem explicação. O campo escondido é inalcançável por teclado e invisível para leitores de ecrã. |
| QR de PIX desatualizado | Apoia-se | "Código PIX em atualização. Fale conosco para apoiar agora." Nunca mostrar um código vencido. |
| Escolha de cookies pendente | todas | Nada de analítica ou marketing dispara antes de uma escolha. |
| Endereço errado | 404 | Uma frase e um caminho de volta ao início. |
| Foco sob a barra de cookies | todas | O conteúdo focado nunca fica escondido pela barra: há afastamento de scroll reservado. |

## Interaction Primitives

- Tocar para agir. Sem gestos escondidos, sem swipe para revelar ações.
- Filtros e páginas são ligações reais: o botão de voltar do browser funciona sempre.
- Navegação do telemóvel abre e fecha pelo controlo do cabeçalho; não sequestra o ecrã.
- Primeiro destino de tabulação é o skip link para o conteúdo principal.
- **Banido:** carrossel automático, animação na abertura, contador de artigos, pop-up a pedir subscrição, auto-play de vídeo.

## Accessibility Floor

Comportamento. O contraste vive em `DESIGN.md`, declarado por par.

- WCAG 2.2 AA em texto, controlos, foco, reflow e alvos de toque.
- Alvo mínimo de 44px em todo o controlo: paginação, filtros, ações de cookies e cabeçalho do acordeão.
- Skip link como primeiro elemento focável.
- Um só `h1` por página: nas superfícies de entrada é o da `page-hero`, nas de leitura é o título do conteúdo. A hierarquia desce a partir dele e o conteúdo nunca repete o título da página.
- Todo o campo tem etiqueta associada e, quando aplicável, `autocomplete`. Erro de campo identificado por `aria-invalid` e ligado por `aria-describedby`; um resumo de erro no topo recebe o foco.
- O resultado do envio é anunciado a leitores de ecrã, incluindo quando falha.
- Foco visível em todos os elementos interativos, com o anel duplo de `{components.focus-ring}`. O foco nunca fica obscurecido pela barra de cookies.
- Imagens com texto alternativo; imagem decorativa marcada como tal.
- Movimento reduzido: transições de cartão, de foco, de abertura do acordeão e de entrada da barra tornam-se instantâneas quando o sistema o pede.
- Navegação por teclado segue a ordem de leitura, cabeçalho incluído, sem armadilhas de foco.
- Reflow a 320px sem scroll horizontal.
- Ajuda consistente: o contacto e a Política de privacidade estão em todas as páginas, no rodapé.

## Key Flows

### UJ-1 — Camila lê o IPCN no autocarro

1. Camila abre a Home no telemóvel, deslogada.
2. Primeiro ecrã: o que o IPCN é, sem banner a ocupar tudo.
3. Desce: Notícias recentes, vitrine do Acervo, próximos encontros.
4. Abre uma Notícia e lê.
5. Volta e entra no Acervo.
6. Escolhe um Tema.
7. Abre um Item de Acervo.
8. **Clímax:** percebe que está a ler uma peça de Acervo, não uma Notícia — o Tema e o enquadramento dizem-lho — e não teve de criar conta. Realiza FR-1, FR-2, FR-3, FR-4.
9. **Resolução:** partilha o endereço da peça. O site não pediu cadastro.

Falha: o Tema escolhido não tem itens → vê a explicação e o caminho para o Acervo inteiro.

### UJ-2 — João quer saber se há encontro esta semana

1. João abre a Agenda, do menu ou da Home.
2. Vê dia, nome e lugar do próximo encontro.
3. **Clímax:** numa olhadela sabe se vai ou se espera. Realiza FR-6.
4. **Resolução:** sai a saber. Nada lhe é pedido.

Falha: sem encontros por vir → "A agenda está sendo montada" e o canal público. Sem lista de encontros passados a fingir de agenda.

### UJ-3 — Lúcia pede para se associar

1. Lúcia toca `Associe-se`, no cabeçalho ou no hero.
2. Preenche nome, e-mail e telefone.
3. Envia.
4. **Clímax:** vê que o pedido foi aceite para envio e que a resposta vem por e-mail. Alguém no IPCN recebe. Realiza FR-8.
5. **Resolução:** espera contacto humano. Não nasceu conta, não há cobrança.

Falha: envio falha → erro na mesma página, campos mantidos, "tente de novo". Nunca confirma o que não aconteceu.

### UJ-4 — Rita deixa uma notícia por aprovar *(fase seguinte)*

1. Rita entra como Criadora.
2. Cria uma Notícia ou um Item de Acervo.
3. Submete.
4. **Clímax:** o texto fica por aprovar e não aparece ao público. Realiza FR-14.
5. **Resolução:** quem publica é o Aprovador. Rita não altera Agenda nem páginas institucionais.

Falha: tenta publicar direto → a ação não existe para ela, em vez de dar erro depois.

### UJ-5 — A contratante diz se pode ir para produção

1. A contratante percorre o site de revisão no telemóvel.
2. Repete no computador.
3. Home, Notícia, Agenda, Acervo, Associe-se, Apoia-se.
4. **Clímax:** ou aprova, ou devolve uma lista do que falta. Realiza FR-17.
5. **Resolução:** só depois disso a produção muda, à mão. Sem veredicto, produção não muda.

Falha: um bloqueio de leitura no telemóvel é motivo de devolução — não um detalhe a corrigir depois.

## Inspiration & Anti-patterns

- **Do Museu Afro Brasil:** menu por tarefa, acervo acessível a distância, agenda própria, apoio no primeiro nível sem área de sócio.
- **Do Amistad Research Center:** a tensão entre herança e trabalho vivo; arquivo e sala de aula lado a lado.
- **Do NYPL Events:** a agenda lida-se como programação cultural, não como feed.
- **Rejeitado — login para ver acervo:** nenhum dos pares consultados fecha o acervo de pesquisa.
- **Rejeitado — carrossel de destaques na Home:** empurra o conteúdo para baixo do primeiro ecrã e esconde a hierarquia.
- **Rejeitado — QR de PIX vencido no ar:** um código morto destrói mais confiança do que a ausência de código.
- **Rejeitado — busca nesta entrega:** sem Tema preenchido, promete mais do que entrega.
- **Rejeitado — barra de cookies translúcida:** sobre conteúdo variável a razão de contraste deixa de ser computável e o foco deixa de ser fiável.

## Responsive & Platform

Piso de largura 320px, sem scroll horizontal; 375px é a largura confortável de revisão. Computador a 1440px como referência. Ponto de viragem em 782px, como o resto do tema.

No telemóvel: uma coluna, cabeçalho compacto, grelhas a uma coluna, paginação e filtros alcançáveis com o polegar. No computador: três colunas de cartões, `{spacing.wide}` de largura, mesma ordem de leitura.

Sem app nativa. Sem modo escuro nesta entrega. A leitura longa mantém a mesma medida em ambos: `{spacing.content}`.
