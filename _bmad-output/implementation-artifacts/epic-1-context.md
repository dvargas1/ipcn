# Epic 1 Context: O site público do IPCN, legível e acessível

<!-- Generated from planning artifacts. Regenerate with compile-epic-context if planning docs change. -->

## Goal

O Visitante lê a Home, uma Notícia, as Secções, o Acervo e um Tema no telemóvel, sem conta, com a identidade do instituto e sem barreiras. O épico traz também a arrumação que estabiliza o tema — um cartão só, markup em patterns, comportamento em `inc/`, CSS num ficheiro — e as correcções de contraste, foco, reflow e cabeçalho de página. Cada história deixa uma superfície completa e verificável no ambiente de revisão.

## Stories

- Story 1.1: Verificação de sintaxe antes de mexer no tema
- Story 1.2: O comportamento sai de `functions.php` para `inc/`
- Story 1.3: Um cartão só, com as casas fixadas
- Story 1.4: A identidade e o foco
- Story 1.5: A Home em cinco blocos, com a vitrine do Acervo
- Story 1.6: Notícias puras e o hub das Secções
- Story 1.7: O Acervo e os Temas, públicos e filtráveis
- Story 1.8: Menu de primeiro nível curto e rodapé completo
- Story 1.9: Leitura no telemóvel até 320px
- Story 1.10: Os estados vazios e a página 404
- Story 1.11: A copy acentuada em português do Brasil
- Story 1.12: O mu-plugin deixa de servir o site FSE
- Story 1.13: Um só cabeçalho de página

## Requirements & Constraints

- **Sem conta.** Home, Notícias, Agenda, institucionais e Acervo abrem sem login, e um endereço de Notícia ou Item abre directo. O Acervo é público por inteiro (sem "últimos N", sem 403); nenhuma conta nasce nesta entrega.
- **Home em cinco blocos**, por esta ordem: hero, Notícias, Acervo, Agenda, faixa de contacto. Bloqueadas as imagens, o primeiro ecrã diz o que o IPCN é; o título do hero não contém número de anos. Notícias mostra só Notícias (filtro por slug, nunca `term_id`) e a vitrine do Acervo não as repete — uma peça em destaque e duas secundárias. A faixa dá endereço, telefone e e-mail como texto.
- **Leitura longa:** medida do `DESIGN.md`, sem scroll horizontal a 375px e com caminho de volta visível do fim do texto.
- **Acervo e Temas:** filtrar por Tema muda o endereço e o voltar funciona; o Item distingue-se de Notícia pela etiqueta de Tema e, sem Tema, mostra a ausência em vez de inventar; paginação só com mais de uma página.
- **Menu:** primeiro nível com Home, Notícias, Acervo, Agenda e as pílulas `Associe-se` e `Apoia-se`; Secções a partir de Notícias, institucionais a partir do rodapé, com contacto e Política de privacidade alcançáveis de qualquer página.
- **Um só cabeçalho de página:** as superfícies de entrada abrem com a `page-hero` e é ela que carrega o `h1`; o conteúdo retoma no `h2` e nunca repete o título. A contagem de `h1` mede-se no HTML servido depois da purga, não no template. As leituras (Notícia, Item) mantêm o título editorial sobre base, sem tarja.
- **Acessibilidade e identidade:** WCAG 2.2 AA nos pares usados, com razão declarada por componente; texto claro sobre ocre reprovado; foco visível também sobre navy, ocre e terracota; links distinguidos por mais do que cor; interface em português do Brasil acentuado.
- **Telemóvel primeiro:** sem scroll horizontal a 320px; alvo de toque de 44px em todo o controlo, incluindo paginação, filtros e cabeçalho do acordeão; sem app e sem modo escuro.
- **Verificação:** Lighthouse ≥ 90 em mobile no ambiente de revisão, depois da purga (sem purga, o HTML servido não é prova); sem build step, testes ou CI — `php -l` no PHP alterado mais observação no browser; nenhum pipeline para produção.
- **Guardrails:** nenhum URL existente muda (`/noticias/`, `/acervo/`, `/temas/<slug>/`, `/associe-se/`, `/apoia-se/`, `/fale-conosco/`, `/politica-de-privacidade/`) e o plugin de SEO mantém-se; nada de tema ou plugin pago onde os blocos nativos bastam; PHP ≥ 8.1 e WordPress ≥ 6.4 declarados.
- **Fora de âmbito:** sem busca; banidos carrossel automático, animação na abertura, contador de artigos, pop-up de subscrição e auto-play. Os papéis editoriais são fase seguinte.

## Technical Decisions

- **Block-first (FSE).** O markup é block markup escrito em ficheiros — `templates/` para a estrutura de cada superfície, `patterns/` para markup reutilizável, `parts/` para header e footer — e o PHP nunca escreve comentários de bloco como literais. Excepções: entregar o markup de um pattern registado ao interpretador de blocos, e um `render_callback` que imprime HTML com as classes do tema. A `page-hero` é um template part, para que uma só forma de cabeçalho substitua as três que hoje coexistem.
- **Listar é `core/query`**, ou o herdado do archive; nada de `WP_Query` novo em shortcodes. A única excepção do projecto é a Agenda (Épico 2).
- **Um cartão só:** dois patterns com `Inserter: false` — `ipcn-card` (imagem 16:9) e `ipcn-card-feature` (4:3, título de headline) — com casas fixas: imagem, marcador de ausência, etiqueta por superfície (`category` em Notícias/Secção/Home, `tema_acervo` no Acervo e no Tema), título, data. Sem imagem, `span.ipcn-card-noimg` com `IPCN`, sem encolher o cartão. Classes `.ipcn-card*`; `ipcn-card-v2` está retirada. Render único: dentro de `core/post-template`.
- **Comportamento em `inc/`:** `setup.php`, `content-model.php`, `listings.php`, `forms.php`, `cookie-bar.php`, `agenda-block.php` — um ficheiro por preocupação, sem hooks repetidos; `functions.php` é carregador fino.
- **CSS num ficheiro:** regras em `style.css`, tokens em `theme.json`; nenhum PHP do tema emite `<style>`, com a única inline `--ipcn-hero-bg` (apontada ao asset do tema, nunca a um URL de ambiente).
- **Selecção por slug, nunca `term_id`** — é o que parte entre ambientes. A referência da navegação e os ids de páginas no rodapé são conteúdo na base de dados, não código.
- **Modelo de conteúdo:** Secção é categoria filha de `noticias`, com o texto de apresentação na descrição do termo; `acervo_ipcn` e `tema_acervo` (`/temas/`) ficam separados de `post`, e uma consulta ao Acervo nunca inclui `post`; `/temas/<slug>` é servido deliberadamente pelo template do archive, a ler a descrição do termo.
- **Vitrine do Acervo:** dois `core/query` irmãos — `perPage: 1` para o `ipcn-card-feature`, `perPage: 2` com `offset: 1` para dois `ipcn-card`, por data decrescente.
- **mu-plugin congelado, com um guard autorizado:** os três blocos só-Divi no `wp_head` (`#ipcn-etmodules-fix`, `#ipcn-form-style`, `#ipcn-footer-logo-fix`) saem só com o tema Divi activo, e nada mais muda.
- **Limite da verificação:** `php -l` não apanha as duas classes de defeito já produzidas — block markup impresso como texto e um cartão a encolher por falta do marcador. Essas verificam-se no browser.
- **Convenções:** patterns `ipcn/<nome>`; classes `ipcn-*`; hooks e shortcodes `ipcn_*`; datas de meta `Y-m-d`, mostradas em `j \d\e M \d\e Y`; paginação `/page/N/`. Tema `ipcn-fse` 0.2.0, block theme, sem build step, fontes do Google enfileiradas por PHP.

## UX & Interaction Patterns

- **Paleta (13 tokens).** Navy é a cor da instituição; ocre é convite e só leva texto chumbo (6.75:1); terracota é acento de rótulo, nunca fundo; `muted` só em bordas de cartão; `text-muted` é borda de campo (4.76:1) e cor de data.
- **Tipografia e layout.** Oswald em títulos e cartões (incluindo `page-title`, a escala do título da `page-hero`), Playfair Display só em leitura longa, Inter em corpo e controlos. Uma coluna centrada, medida 720, grelhas 1100, margem de 20px; cartões a três colunas no computador, uma no telemóvel; viragem a 782px, piso 320px, revisão a 375px.
- **Cabeçalho de página.** Navy, a imagem `assets/hero-bg.jpg` por trás com gradiente, eyebrow, título e linha de apoio só quando existe (a descrição do termo, em Secções e Temas). Sem linha de apoio, a faixa encolhe em vez de reservar espaço. O eyebrow usa `base` sobre navy com alfa ≥ 0.88 — o ocre a 0.68 dá 2.57:1 e falha os 4.5:1 de `typography.eyebrow`.
- **Foco:** anel duplo — `base` 2px por dentro, `navy` 2px por fora, 2px de afastamento — visível sobre base, subtle, ocre, navy, terracota e rodapé; nenhum `outline: none` sem este substituto.
- **Acessibilidade:** skip link como primeiro focável; etiquetas associadas e `autocomplete`; erro ligado ao campo por `aria-invalid` e `aria-describedby`, com resumo no topo; texto alternativo; movimento reduzido; ordem de foco sem armadilhas.
- **Interacção e estados.** Tocar para agir, sem gestos escondidos; filtros e paginação são ligações reais, por isso o voltar funciona; nunca duas grelhas iguais seguidas. Vazios: "Em breve, novidades por aqui." (Home sem Notícias, secção mantida), "Em breve, novos itens do acervo." (Acervo), explicação com as restantes Secções (Notícias/Secção), oferta do Acervo inteiro (Tema sem itens), uma frase e caminho de volta (404).
- **Rodapé e voz.** Três colunas (Instituto · Contato · Redes sociais), linha final e a ligação à Política de privacidade, hoje ausente. Voz institucional em português do Brasil, frases completas, sem entusiasmo de marketing; o eyebrow usa a forma `IPCN · <superfície>`.

## Cross-Story Dependencies

- **1.1 é pré-requisito de todas** — sem o script não há forma barata de apanhar PHP que não compila antes do ambiente de revisão.
- **1.2 condiciona o resto do épico:** a repartição em `inc/` tem de deixar o site a comportar-se como antes, e 1.3, 1.6, 1.7 e 1.12 acrescentam a ficheiros que só existem depois dela.
- **1.3 é consumido fora do épico:** o Épico 2 (Agenda) renderiza pelo cartão compacto, logo o contrato de classes e o render único têm de ficar estáveis.
- **1.13 depende de 1.10:** a 1.10 subiu o título do `page.html` a `level:1` e passou a haver dois `h1` nas páginas institucionais; a 1.13 fecha isso com a `page-hero` e obriga a remover, não a substituir, o hero que vive dentro do conteúdo dessas páginas. Toca `templates/page.html`, `templates/page-noticias.html`, `parts/page-hero.html` e `style.css`; converter `templates/archive.html`/`inc/listings.php` ao mesmo part é candidato, não requisito.
- **Sobreposição aceite:** os Épicos 1 e 3 tocam ambos `inc/forms.php` e `inc/cookie-bar.php` (a partilha de `style.css` é incidental) e foram mantidos distintos. **1.5 agrega 1.6 e 1.7**; **1.4 e 1.9 são transversais**; **1.8, 1.10, 1.11 e 1.13 fecham as superfícies.**
