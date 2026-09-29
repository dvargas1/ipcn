---
title: 'Um só cabeçalho de página'
type: 'feature'
created: '2026-09-29'
baseline_revision: '11d79f3764c44b775faa349f3daeb49e290ece5d'
status: 'done'
review_loop_iteration: 0
followup_review_recommended: false
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
warnings:
  - oversized
deferred:
  - summary: >-
      O `base` do eyebrow da `page-hero` só escapa à regra terracota do `style.css` por acidente: o selector casa pelo texto do `style` inline e o eyebrow novo usa `letter-spacing:2px`.
    evidence: >-
      Achado BH3 da revisão da 1.13. `style.css:742` pinta `color:#a85a32 !important` em qualquer parágrafo cujo `style` contenha `letter-spacing:0.2em`, e nada regista que o eyebrow do part depende de usar `2px`. Fecha-se dando ao eyebrow uma classe explícita no `parts/page-hero.html` e escopando a regra terracota aos eyebrows de secção — é trabalho de uma passagem de CSS, não desta história.
    location: >-
      wp-content/themes/ipcn-fse/style.css:742
    severity: low
  - summary: >-
      A verificação durável do que esta história acrescenta — o texto por superfície do resolvedor, o markup do `page-hero` e as duas regras do CSS — continua a não existir no repositório.
    evidence: >-
      Achados BH12, BH13, BH14, EC6, VG1 e VG2 da revisão da 1.13. O `check-php.sh` corre `php -l` e greps sob `inc/` (não lê `.html` nem `.css`), o `check-php.test.sh` não carrega o tema, e o harness que afirma as linhas da matriz vive na scratchpad da sessão e morre com ela. É a mesma infraestrutura sem dono diferida pela 1.3 a 1.10, e o `Never` desta história proíbe acrescentar testes. Fecha-se com um harness durável em `scripts/`, no estilo do `check-php.test.sh`, que carregue `inc/page-hero.php` com stubs e enumere `templates/*.html` e `parts/`.
    location: >-
      scripts/check-php.sh
    severity: low
  - summary: >-
      O fecho dos AC medidos no HTML servido depende de acções do dono: remover o hero do conteúdo das cinco institucionais na base de dados e fazer a passagem no browser depois do deploy e da purga.
    evidence: >-
      Achados BH9 e IA2 da revisão da 1.13. O hero duplicado vive no `post_content` (nenhum mecanismo do repositório o alcança e a política manda parar antes de alterações na base de dados) e o repositório não mede o renderizado. A lista do que verificar está na secção `## Verification` desta spec e a entrada equivalente está no `deferred-work.md`.
    location: >-
      _bmad-output/implementation-artifacts/deferred-work.md
    severity: low
---

<intent-contract>

## Intent

**Problem:** As cinco páginas institucionais (`/quem-somos/`, `/projetos/`, `/editorial/`, `/associe-se/`, `/apoia-se/`) servem dois `h1` com o mesmo texto, empilhados: o `wp:post-title` que a 1.10 subiu a `"level":1` em `templates/page.html:4` e o hero que vive dentro do conteúdo escrito no editor. Ao mesmo tempo as superfícies de entrada abrem de três formas diferentes: hero navy escrito à mão no template (`page-noticias.html:6-18`, `archive-acervo_ipcn.html:5-19`), hero fabricado em PHP pelo shortcode `[ipcn_archive_hero]` (`inc/listings.php:152-195`) e título a seco (`page.html:4`, `404.html:3`).

**Approach:** Um só template part — `parts/page-hero.html` — que passa a ser a única forma de cabeçalho das superfícies de entrada (`page.html`, `page-noticias.html`, `archive.html`, `archive-acervo_ipcn.html`, `404.html`): faixa navy com `assets/hero-bg.jpg` por trás e sobreposição de navy com alfa >= 0.88, eyebrow `IPCN · <superfície>` em `base`, `h1` na escala `page-title` e linha de apoio só quando existe. O texto de cada superfície é resolvido em `inc/page-hero.php` (título da página, nome e descrição do termo, o hub, o Acervo, a 404). O `h1` do `page.html` sai, o `[ipcn_archive_hero]` retira-se e o hero dentro do conteúdo das institucionais é removido pelo dono na base de dados.

## Boundaries & Constraints

**Always:** o único `h1` das superfícies de entrada é o da `page-hero`; o eyebrow é `IPCN · <superfície>` em `base` (nunca ocre) e a linha de apoio vem da descrição do termo quando existe, sem frase de reserva; sem linha de apoio, a faixa encolhe em vez de reservar o espaço; as leituras (`single.html`, `single-acervo_ipcn.html`) ficam intocadas; o `main#conteudo`, o skip link e a estrutura `align:full` + `layout default` de cada superfície mantêm-se; CSS só em `style.css`, tokens em `theme.json`; nenhum PHP sob `inc/` emite comentários de bloco nem `<style>`; todo o texto resolvido em PHP sai escapado (`esc_html`, `wp_strip_all_tags` na descrição); `functions.php` é o carregador e ganha `inc/page-hero.php` por ordem explícita; `bash scripts/check-php.sh` passa a acusar 12 problemas (todos `block markup` em `inc/agenda-block.php`), sem `sintaxe:` novos, e `bash scripts/check-php.test.sh` continua 14/14.

**Never:** tocar no hero da Home (`front-page.html:6-28`, classe `.ipcn-hero`) nem na sua sobreposição de 0.68/0.82; tocar em `templates/index.html`; mexer em `inc/forms.php`, `inc/cookie-bar.php`, `inc/agenda-block.php`, `inc/setup.php`, `inc/content-model.php`, nos patterns `ipcn/*` ou no mu-plugin; escrever markup de bloco como literal em PHP; reintroduzir URL absoluto de ambiente; mexer em `sprint-status.yaml`, `README.md`, `STATUS.md`, `ROADMAP.md`, no bloco gerido do `AGENTS.md` ou nos artefactos de planeamento; editar a base de dados, fazer deploy ou purgar (acção do dono); acrescentar dependências, testes ou CI.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Página institucional | `/quem-somos/` (uma página) | eyebrow `IPCN · Quem Somos`; `h1` `Quem Somos`; sem linha de apoio (a faixa encolhe) | — |
| Hub de Notícias | `/noticias/` | eyebrow `IPCN · Notícias`; `h1` `Notícias do IPCN`; linha de apoio a frase de hoje, verbatim | — |
| Secção com descrição | `/category/<seccao>/`, termo com `description` | eyebrow `IPCN · <nome>`; `h1` `<nome>`; linha de apoio = descrição sem etiquetas | — |
| Tema | `/temas/<slug>/` (`tema_acervo`) | idem Secção | — |
| Termo sem descrição | descrição vazia ou só espaços | sem linha de apoio; a faixa encolhe | — |
| Termo sem nome | `name` vazio | `h1` derivado do slug (`ucwords` com `-` → espaço); slug vazio → `Conteúdo IPCN` | — |
| Acervo | `/acervo/` (archive do CPT) | eyebrow `IPCN · Acervo`; `h1` `Memória e história do IPCN`; linha de apoio a frase de hoje | — |
| Endereço errado | 404 | eyebrow `IPCN · Erro 404`; `h1` `Página não encontrada`; sem linha de apoio | — |
| Arquivo de autor | `/author/<slug>/` | eyebrow `IPCN · <nome de exibição>`; `h1` o nome | — |
| Arquivo de data | `/2026/09/` | eyebrow/h1 com o título do arquivo sem o prefixo do núcleo | — |
| Nome ou descrição com markup | `name`/`description` com `<script>` ou `<em>` | sai escapado, sem etiquetas literais | `esc_html` / `wp_strip_all_tags` |
| Página fora do hub | uma página com slug diferente de `noticias` | eyebrow `IPCN · <título>`; `h1` o título da página (é a degradação do caso do hub) | — |
| Leituras | `/noticia/<slug>/`, item do Acervo | inalteradas: o título editorial do conteúdo, sem `page-hero` | — |
| Conteúdo institucional | páginas institucionais com o hero no conteúdo | com o hero removido na base de dados, um só `h1` por página | sem a remoção do dono, persistem dois `h1` (registado em `deferred`) |

</intent-contract>

## Code Map

- `wp-content/themes/ipcn-fse/parts/page-hero.html` — **novo**, a forma única. Grupo `align:full`, `className:"ipcn-hero ipcn-page-hero"`, `backgroundColor:navy`, `textColor:base`, sem `layout` próprio, com grupo interno `layout constrained contentSize:"1100px"` (é a anatomia de `page-noticias.html:6-18` e de `inc/listings.php:177-191`). Dentro: eyebrow (`wp:paragraph`, 13px/600/`letter-spacing:2px`/uppercase, `textColor:base`) com `[ipcn_page_hero_eyebrow]`; `wp:heading {"level":1}` (Oswald `clamp(28px,5vw,44px)`, 700, lh 1.15, `textColor:base`) com `[ipcn_page_hero_title]`; e o apoio com `className:"ipcn-page-hero-apoio"` e `[ipcn_page_hero_apoio]`. Sem padding inline: `.ipcn-hero` dá 72px no telemóvel e 120px a >= 782px (`style.css:893-902`) — o tratamento da home, como a decisão de UX manda. O ficheiro é um `parts/*.html` normal: o núcleo descobre-o por slug e o `theme.json` declara-o.
- `wp-content/themes/ipcn-fse/inc/page-hero.php` — **novo**. `ipcn_page_hero_data()` devolve `array( 'eyebrow' => …, 'title' => …, 'apoio' => … )`, com cache estática, filtrado por `ipcn_page_hero_data`, resolvido por esta ordem: `is_404()` → `IPCN · Erro 404` / `Página não encontrada` / `''`; `is_post_type_archive( 'acervo_ipcn' )` → `IPCN · Acervo` / `Memória e história do IPCN` / `Depoimentos, fotos e documentos preservados pelo instituto.`; `is_page( 'noticias' )` → `IPCN · Notícias` / `Notícias do IPCN` / a frase de `page-noticias.html:16` verbatim; `WP_Term` → `IPCN · <nome>` / `<nome>` / descrição, com os fallbacks de `inc/listings.php:170-171` (slug, `Conteúdo IPCN`); `WP_User` → `IPCN · <display_name>` / o nome / `''`; `is_page()` → `IPCN · <título>` / `<título>` / `''`; resto (arquivos de data e de post type) → título de `get_the_archive_title()` sem o prefixo do núcleo, em eyebrow e título. Três shortcodes (`ipcn_page_hero_eyebrow`, `ipcn_page_hero_title`, `ipcn_page_hero_apoio`) devolvem `esc_html()` do campo — nenhum markup de bloco, nenhum `<style>`.
- `wp-content/themes/ipcn-fse/functions.php` — o carregador: `require_once __DIR__ . '/inc/page-hero.php';` na lista explícita (`:16-21`) e a ordem no docblock (`:7-11`).
- `wp-content/themes/ipcn-fse/templates/page.html` — 7 linhas. O `main` constrained 720 com `wp:post-title {"level":1}` (`:4`) passa a `main` `tagName:main`+`anchor:conteudo`+`align:full`+`layout default`, com `wp:template-part {"slug":"page-hero"}` como primeiro filho e um grupo `constrained contentSize:"720px"` com `padding 40/40` a envolver o `wp:post-content` — o `post-title` sai e o `h1` passa a ser o da `page-hero`.
- `wp-content/themes/ipcn-fse/templates/page-noticias.html` — o hero inline (`:6-18`) sai e entra `wp:template-part {"slug":"page-hero"}`; o pattern `ipcn/seccoes` (`:21`) e a listagem (`:28-52`) ficam intactos.
- `wp-content/themes/ipcn-fse/templates/archive.html` — `:5` `[ipcn_archive_hero]` passa a `wp:template-part {"slug":"page-hero"}`; a listagem, o pattern `ipcn/card` e `[ipcn_archive_vazio]` (`:23`) ficam.
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — o hero inline (`:5-19`) sai e entra `wp:template-part {"slug":"page-hero"}`; o `h1`/eyebrow/apoiador saem do ficheiro.
- `wp-content/themes/ipcn-fse/templates/404.html` — o `main` `subtle` centrado (`:2-4`) passa a `main` `align:full`+`layout default` com o part e um grupo `subtle` `constrained 720` + `padding 80/80` com a frase e a ligação de volta (`:3-4`); o `h1` sai do template.
- `wp-content/themes/ipcn-fse/inc/listings.php` — apagar o `add_shortcode( 'ipcn_archive_hero', … )` (`:145-195`) e o comentário que o documenta; os 5 problemas `block markup` aceites deste ficheiro desaparecem. O resto fica: o filtro `query_loop_block_query_vars`, `[ipcn_query_posts]`, `[ipcn_tema_filter]` (`:198-`) e `[ipcn_archive_vazio]` (`:282-319`).
- `wp-content/themes/ipcn-fse/style.css` — `.ipcn-hero` (`:193-211`) e a sua sobreposição ficam intactos (a Home não muda); acrescentar `.ipcn-page-hero::after` com o gradiente a `alfa >= 0.88` (pior caso `rgba(13,23,107,0.88)`), depois da regra da home para vencer por ordem; e `.ipcn-page-hero-apoio:empty { display: none; }` — o apoio vazio não reserva espaço. Nenhum `<style>` novo sai de PHP (`AD-5`).
- `wp-content/themes/ipcn-fse/theme.json` — `templateParts` (`:62-65`) ganha `{ "name": "page-hero", "title": "Cabeçalho de página", "area": "uncategorized" }`. Paleta, fontes e `customTemplates` não se tocam.
- `_bmad-output/implementation-artifacts/deferred-work.md` — as entradas da 1.13 e o fecho da entrada da 1.10 dos archives de autor e data sem `h1` (`:203-204`), que esta história fecha por via do resolvedor.
- `docs/auditoria-redesign-fse-2026-09-17.md` — anotar o achado #8 (`:28`) como fechado pela 1.13.
- `_bmad-output/planning-artifacts/sprint-change-proposal-2026-09-29.md` — a proposta que abriu esta história; `:113-120` o AC, `:64-67` o âmbito, `:25` a causa raiz (o `post-title` subido pela 1.10).

## Tasks & Acceptance

**Execution:**
- `wp-content/themes/ipcn-fse/parts/page-hero.html` — criar a faixa única com eyebrow/título/apoio alimentados pelos três shortcodes — é a forma que substitui as três que coexistem.
- `wp-content/themes/ipcn-fse/inc/page-hero.php` — criar o resolvedor e os três shortcodes — o texto é dado de cada superfície, com a descrição do termo a alimentar o apoio.
- `wp-content/themes/ipcn-fse/functions.php` — acrescentar `require_once __DIR__ . '/inc/page-hero.php';` à lista e à ordem documentada — o carregador é o único ponto de inclusão.
- `wp-content/themes/ipcn-fse/templates/page.html` — trocar o `post-title` pelo part e envolver o conteúdo no grupo constrained 720 — as institucionais deixam de ter dois títulos.
- `wp-content/themes/ipcn-fse/templates/page-noticias.html` — substituir o hero inline pelo part — o hub mantém a copy e ganha a imagem e a sobreposição.
- `wp-content/themes/ipcn-fse/templates/archive.html` — substituir `[ipcn_archive_hero]` pelo part — Secções, Temas e Agenda convergem na mesma faixa.
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — substituir o hero inline pelo part.
- `wp-content/themes/ipcn-fse/templates/404.html` — trocar o `h1` do template pelo part e manter a frase e a ligação de volta.
- `wp-content/themes/ipcn-fse/inc/listings.php` — apagar `[ipcn_archive_hero]` e o seu comentário — o markup fabricado em PHP sai e o `check-php.sh` perde os 5 problemas.
- `wp-content/themes/ipcn-fse/style.css` — a sobreposição `>= 0.88` do `page-hero` e o `:empty` do apoio — o eyebrow passa os 4.5:1 sobre a imagem e a faixa encolhe sem linha de apoio.
- `wp-content/themes/ipcn-fse/theme.json` — declarar o template part `page-hero` — o editor passa a conhecê-lo com título e área.
- `_bmad-output/implementation-artifacts/deferred-work.md` e `docs/auditoria-redesign-fse-2026-09-17.md` — registar as duas entradas novas (a remoção do hero no conteúdo e a passagem no browser, ambas acção do dono), fechar a entrada dos archives sem `h1` e anotar o achado #8.
- Harness de sessão (na scratchpad, fora do deliverable) — `php verify-hero.php` com stubs de `is_404()`, `is_page()`, `is_post_type_archive()`, `get_queried_object()`, `get_the_archive_title()`, `get_the_title()`: uma asserção por linha da matriz de I/O.

**Acceptance Criteria:**
- Given qualquer superfície de entrada servida no ambiente de revisão depois da purga, when se conta os `h1` do HTML servido, then há exactamente um, e é o da `page-hero`.
- Given as páginas institucionais, when o hero do conteúdo é removido na base de dados, then cada uma abre com um só título e o conteúdo retoma no `h2`.
- Given a faixa sem linha de apoio (páginas institucionais e 404), when é renderizada, then não reserva o espaço da linha.
- Given o eyebrow, when é lido sobre a faixa, then usa `base` sobre navy com alfa >= 0.88 e nomeia a superfície.
- Given as leituras, when são comparadas com o estado anterior, then `single.html` e `single-acervo_ipcn.html` não mudaram.
- Given `grep -rn 'ipcn_archive_hero' wp-content/`, when corro, then não devolve nada.
- Given `bash scripts/check-php.sh`, when corro, then 0 problemas, 0 `sintaxe:` e saída 0 — o baseline imediatamente antes desta história já era 5 (todos em `inc/listings.php`), porque o commit `3fb670a` tinha retirado os 12 de `inc/agenda-block.php`, e esta história retira os 5 últimos.
- Given `bash scripts/check-php.test.sh`, when corro, then 14/14.

## Spec Change Log

### 2026-09-29 — o número do `check-php.sh` no `Always` (o contrato não muda; a cláusula fica superada)

O `Always` diz «`check-php.sh` passa a acusar 12 problemas (todos `block markup` em `inc/agenda-block.php`)». O número é obsoleto: o commit `3fb670a`, anterior ao `baseline_revision`, já tinha retirado os 12 problemas de `block markup` do `agenda-block.php`, e o baseline imediatamente antes desta história era **5**, todos em `inc/listings.php` — medido na fonte (`git show 11d79f3:…/inc/agenda-block.php | grep -c -F '<!-- wp:'` → 0; `…/inc/listings.php` → 5). Com o hero fabricado retirado, o resultado é **0 problemas**, `0 sintaxe:` e saída 0: estritamente menos problemas do que o baseline, que é o que a cláusula quer garantir. Só o número fica superado; a intenção («sem problemas novos») manteve-se, e o `Never` proibiu inflar o `agenda-block.php` para chegar a 12. Os AC e a secção `## Verification` foram corrigidos para o valor medido. **KEEP:** as três verificações do script e o `check-php.test.sh` em 14/14.

## Review Triage Log

### 2026-09-29 — Review pass

- verdicts: 36 findings — high 0, medium 3, low 23, false 9, maybe-false 1
- findings:
  - `[low]` `[reject]` **BH1** a aritmética do contraste não fecha (o ocre a 0.88 dá 4.97:1, que passa os 4.5:1) — Verdadeiro na prosa das `## Design Notes`; o AC da história fixa a regra certa (o ocre a 0.68 dá 2.57:1 e falha) e é isso que o código cumpre (eyebrow em `base` sobre alfa >= 0.88, pior caso 11.24:1). O remédio é prosa da spec; a frase foi corrigida no lugar do número errado.
  - `[false]` `[reject]` **BH2** tokens citados que não existem no `theme.json` — `typography.eyebrow` e `page-title` são tokens do `DESIGN.md` (a espinha de desenho), não do `theme.json`; a receita inline da `page-hero` é a da superfície que substitui (`page-noticias.html:6-18`), e nenhum AC pediu tokens novos de escala.
  - `[low]` `[defer]` **BH3** o `base` do eyebrow só escapa à regra terracota por acidente (`style.css:742` pinta `letter-spacing:0.2em`) — Verdadeiro e real: o selector casa pelo texto do `style` inline e o eyebrow novo usa `2px`. O remédio (classe explícita no eyebrow + escopo da regra terracota) mexe numa regra partilhada com os eyebrows de secção, mais do que uma correcção directa — vai para `deferred` com o fecho nomeado.
  - `[low]` `[reject]` **BH4** três receitas de eyebrow continuam a coexistir — A da `page-hero` é literalmente a da banda que substitui; a da Home e a do Item são outros componentes, fora do âmbito do AC. Registar a receita canónica é prosa da spec.
  - `[false]` `[reject]` **BH5** a altura da faixa muda nas superfícies que ninguém nomeia — A mudança é intencional e está registada (a decisão de UX do `memlog` manda "igual ao tratamento da home", e o Code Map fixa-o): o `.ipcn-hero` passa a dar 72/120px onde havia 56-64/48-56px. Não é defeito; a ausência de alturas no *manual check* é prosa da spec.
  - `[false]` `[reject]` **BH6** entrega trabalho que a proposta de mudança pôs fora de âmbito — O AC da história (`epics.md:352-367`) nomeia Secção, Tema, Acervo, Agenda e a 404 e manda que o `h1` seja o da `page-hero`; o `UX-DR26` e a `epic-1-context.md:41` fixam o part como a forma única que substitui as três. A linha "candidato, não requisito" da proposta foi ultrapassada pelo próprio AC escrito na mesma passagem de emenda.
  - `[low]` `[reject]` **BH7** o `Always` fica factualmente falso (12 problemas) — O contrato é imutável nesta passagem; a supersessão está registada no `## Spec Change Log` com a medição na fonte, e os AC e a `## Verification` levam o valor medido (0 problemas).
  - `[false]` `[reject]` **BH8** o frontmatter contradiz o corpo (`deferred: []` e triage vazio) — Ambos são preenchidos por esta própria passagem: a lista `deferred` ganha as três entradas do encaminhamento e este log é o que estava vazio.
  - `[low]` `[defer]` **BH9** o AC principal não se fecha com este diff (depende da base de dados) — Verdadeiro e inevitável: o hero vive no `post_content`, que nenhum mecanismo do repositório alcança, e a política manda parar antes de alterações na base de dados. O AC está escrito de forma condicional, o passo manual e o deploy/purga estão no ledger e no `deferred` desta spec.
  - `[low]` `[reject]` **BH10** o invariante das "superfícies de entrada" é mais largo do que a implementação (`index.html`) — `index.html` não está na lista do AC e a excepção está registada nas `## Design Notes`; qualificar o contrato ou fixar o `level` do `query-title` é prosa da spec. (Uma página de posts sem `h1` pelo `query-title` é pré-existente e nenhum AC a nomeia.)
  - `[false]` `[reject]` **BH11** as leituras mantêm um segundo eyebrow ocre sem excepção registada — As `## Design Notes` registam-no com as razões medidas: a faixa do Item é navy liso (sem imagem), onde o ocre dá 6.87:1 e passa.
  - `[low]` `[defer]` **BH12** não há guarda durável para o invariante que a história estabelece — Verdadeiro: nem `check-php.sh` lê `.html`/`.css` nem nenhum comando carrega a `page-hero`. É a mesma infraestrutura sem dono já diferida pela 1.3 a 1.10 (o NFR9 declara "sem build step, testes ou CI" e o `Never` proíbe acrescentar testes nesta história); o fecho nomeado é o harness durável em `scripts/`, no estilo do `check-php.test.sh`.
  - `[low]` `[defer]` **BH13** os greps da verificação não vêem o que o AC precisa — Grupo do BH12: `grep -c` prova presença, não exclusividade, e o `h1` mudou de sítio (passou para `parts/`). Mesmo fecho (harness durável que enumere templates e part).
  - `[low]` `[defer]` **BH14** as 14 asserções não são reproduzíveis (harness na scratchpad) — Grupo do BH12. O harness correu (18/18 depois das correcções) mas morre com a sessão; é o mesmo diferido das histórias 1.3 a 1.10.
  - `[false]` `[reject]` **BH15** faltam linhas na matriz para estados que o resolvedor alcança — O autor com `display_name` vazio não é alcançável (o `wp_insert_user` preenche-o com o `user_login`), a página 2 de uma Secção repetir o nome do termo é o comportamento certo, um archive de outro CPT recebe o título do núcleo, e a descrição longa é o comportamento pré-existente do hero retirado (nenhum AC pede corte).
  - `[low]` `[reject]` **BH16** o último recurso do `get_bloginfo` é código morto e o filtro pode esvaziar o `h1` — Apagar a guarda arrisca repor um `h1` vazio numa superfície que esta história existe para corrigir, e não há WordPress aqui para provar o negativo; a guarda passou a usar o teste de vazio partilhado. O filtro devolver não-array exige que um terceiro se pendure num hook que nada regista; `@since` e docblocks são cosméticos.
  - `[low]` `[reject]` **BH17** `static $data` memoriza um valor dependente do contexto — No pedido que o AC mede (o render do template principal) resolve-se uma vez, e é isso que a cache serve; no editor a pré-visualização pode mostrar o texto de outra superfície, o que é cosmético, e descachear é um desenho novo, mais do que uma correcção directa.
  - `[false]` `[reject]` **BH18** copy para um mapa fixo em PHP contra o AD-8 — O AD-8 governa a selecção de termos: o nome e a descrição continuam a vir do termo, nunca de `term_id`. O mapa novo serve as duas superfícies que **não têm termo** (o hub e o archive do CPT), é chaveado por slug de página e por post type (nunca por id nem URL), preserva a copy que a proposta manda manter e degrada para o título da página sem erro.
  - `[low]` `[reject]` **BH19** a reconciliação do ledger é o inverso do plano e prematura — O fecho assenta em código que o harness afirma (os cenários `autor` e `data`), que é o mesmo padrão de evidência das entradas do ledger; a confirmação renderizada está registada à parte (entrada própria e `deferred`).
  - `[low]` `[patch]` **BH20** registos auxiliares meio actualizados — A metade accionável é o comentário do `style.css:1142-1143`, que apontava para `inc/listings.php:178,186` (apagado): corrigido para `parts/page-hero.html` e para o hero do Item. A outra metade (o texto histórico do achado #8 na auditoria de 17/09) não se reescreve: é uma fotografia datada e a anotação de fecho é a acção registada.
  - `[low]` `[reject]` **BH21** âncoras de linha da spec desactualizadas — O Code Map cita de propósito as âncoras **antes** da mudança (é o mapa do que mexer); a deriva do que ficou é prosa da spec.
  - `[low]` `[reject]` **BH22** o `theme.json` convida a partir o part — O título em português é o que a spec fixa (a interface é pt-BR) e um template part não tem equivalente ao `Inserter: false` dos patterns: a entrada é o que o faz aparecer no editor, que é o que o AC quer.
  - `[low]` `[reject]` **BH23** o eyebrow anuncia a superfície duas vezes — O eyebrow é exigido pelo AC; um anúncio repetido não é falha WCAG, e `aria-hidden` inventaria markup que a UX nunca pediu — mais do que uma correcção directa.
  - `[medium]` `[patch]` **EC1** uma página com título sem texto visível dá `h1` vazio — Verdadeiro e alcançável: o `get_the_title()` devolve `''` (o `core/post-title` responde com `return '';`, ou seja, nem `h1` havia, em WP 6.4) e o resolvedor imprimia `<h1></h1>`. Corrigido: a guarda institucional (`Conteúdo IPCN`) passou a cobrir o ramo da página.
  - `[medium]` `[patch]` **EC2** descrição só com espaços inquebráveis não encolhe a faixa — Verdadeiro: o `trim` do PHP não tira o U+00A0 que os editores colam, e o `<p>` ficava com conteúdo (não casava `:empty`). Corrigido com o teste de vazio partilhado, que devolve `''`.
  - `[medium]` `[patch]` **EC3** nome de termo só com espaços inquebráveis imprimia um `h1` invisível — Mesma raiz do EC2; corrigido: o nome derivado do slug (ou `Conteúdo IPCN`) passa a decidir pelo teste de vazio.
  - `[low]` `[patch]` **EC4** o comentário do `style.css` cita o ficheiro apagado — Ver BH20 (mesma localização, mesma correcção).
  - `[maybe-false]` `[reject]` **EC5** conteúdo na base de dados pode ainda conter `[ipcn_archive_hero]` — O único uso documentado era o `archive.html` (um template) e a guarda do shortcode devolvia `''` fora de um termo, pelo que nada no conteúdo mostrava saída. O que faltaria para fechar: `wp post list --search=ipcn_archive_hero` na instância. Se fosse verdade seria `low` (um literal num conteúdo nunca visitado).
  - `[low]` `[defer]` **EC6** o harness cobre 12 das 14 linhas da matriz — Verdadeiro (as linhas «Leituras» e «Conteúdo institucional» não têm cenário) e já qualificado na `## Auto Run Result`, com a verificação alternativa de cada uma. O fecho durável é o mesmo harness em `scripts/` do BH12.
  - `[low]` `[defer]` **VG1** a resolução por superfície só é afirmada por um harness de sessão que nenhum comando corre — Pré-verificado pela camada. Verdadeiro; é a infraestrutura diferida pela 1.3 a 1.10, e o fecho nomeado está no ledger (`deferred-work.md:32`).
  - `[low]` `[defer]` **VG2** a troca dos cinco templates só é verificada pela passagem manual do dono — Grupo do VG1: nada no repositório lê `.html` nem `.css`, e o AC central mudou para essa superfície.
  - `[low]` `[patch]` **VG3** a matriz da 1.11 continua a fixar a frase de reserva que esta história retira — Verdadeiro e diferente dos outros dois itens superados (o ledger da 1.10 e o achado #8 foram anotados; este não). Corrigido: entrada no `## Spec Change Log` da spec da 1.11 a apontar para esta, com o KEEP da acentuação.
  - `[false]` `[reject]` **IA1** o diff faz a parte opcional do plano e adia a obrigatória — Ver BH6: o AC é a autoridade e inclui a conversão; a remoção na base de dados não é do repositório.
  - `[low]` `[defer]` **IA2** as expectativas do intento vivem nas superfícies servida/base de dados e o diff mede-se na fonte — Verdadeiro; a parte servida e de base de dados é acção do dono (grupo do BH9) e a parte de código é o grupo do BH12.
  - `[low]` `[reject]` **IA3** o tracker (`sprint-status.yaml`) não avança — Não é artefacto deste fluxo (nenhum passo o escreve; a chave de 1.13 já lá está) e a reconciliação está atribuída ao planeamento pela própria proposta de mudança.
  - `[false]` `[reject]` **IA4** os artefactos do intento contradizem-se e o diff resolve-o em silêncio — Ver BH6: a leitura implementada é a do AC e a decisão está registada nas `## Design Notes`.

**Encaminhamento.** **patch** — A (EC1, EC2, EC3: a guarda de vazio no resolvedor), B (EC4, BH20: o comentário do `style.css`) e C (VG3: a anotação na spec da 1.11). **defer** — D (BH3: a classe do eyebrow e o escopo da regra terracota), E (BH12, BH13, BH14, EC6, VG1, VG2: a verificação durável do markup, do CSS e do resolvedor) e F (BH9, IA2: a remoção do hero no conteúdo e a passagem no browser — acção do dono). **reject** — 10 `false` (BH2, BH5, BH6, BH8, BH11, BH15, BH18, IA1, IA4 e o `maybe-false` EC5) e 20 `low` cujo remédio seria prosa da spec ou cujo defeito foi refutado. Sem `intent_gap` nem `bad_spec`: sem loopback.

## Design Notes

**Porquê um só part e o texto resolvido em `inc/`.** A decisão de UX (`memlog`, 2026-09-29) manda as três formas convergirem num componente `page-hero`, e a `epic-1-context.md` fixa-o como template part. Um template part é um ficheiro só: o texto de cada superfície não cabe nele e não há atributos de `wp:template-part` que cheguem aos blocos do part. Das duas saídas — texto como atributos de um shortcode que renderiza o part (levaria markup de bloco para PHP e tiraria o part do mecanismo `wp:template-part`), ou texto como dado resolvido por contexto — escolheu-se a segunda: o ficheiro é a forma, `inc/page-hero.php` é o dado, e nenhum comentário de bloco entra em PHP (`AD-1`).

**Porquê a copy do hub e do Acervo vive no resolvedor.** `/noticias/` e `/acervo/` não têm dado nenhum de que derivar o título (o hub é uma página, o Acervo é um archive de CPT) e a proposta fixa que a copy do hub se mantém. As duas frases e o `IPCN · Acervo` ficam no resolvedor, por condição (`is_page( 'noticias' )`, `is_post_type_archive( 'acervo_ipcn' )`), nunca por id nem por URL. Se a página do hub mudar de slug, o caso degrada para o tratamento genérico de página (título da página), sem erro.

**Porquê a sobreposição é uma regra nova e não a da home.** `style.css:205-211` serve o hero da Home, cujo eyebrow é ocre e cujas razões de contraste já estão fixadas. O `page-hero` precisa de alfa >= 0.88: `base` dá 11.24:1, e o caso que falha os 4.5:1 de `typography.eyebrow` é o ocre a 0.68 (2.57:1) — o ocre a 0.88 daria 4.97:1, e é o pior caso que esta regra fecha. Sobrepor a regra só no `.ipcn-page-hero` deixa a Home exactamente como está. O ocre do eyebrow da Home continua como estava e fica fora do âmbito desta história.

**Porquê o apoio vazio desaparece por CSS.** O `[ipcn_page_hero_apoio]` vazio deixa um `<p>` sem conteúdo, que o `blockGap` do layout de fluxo ainda marginava. `.ipcn-page-hero-apoio:empty { display: none; }` cumpre o estado "Página sem linha de apoio" (`EXPERIENCE.md:113`) sem condicional em PHP.

**Porquê os archives de autor e data ganham título.** `archive.html` serve também os archives de tag, autor e data e o `[ipcn_archive_hero]` devolvia `''` fora de `WP_Term` — a origem da entrada dos archives sem `h1` no ledger de diferidos. Com o part a ler o objecto consultado, esses archives recebem título (nome de exibição do autor, título do arquivo sem prefixo) e o defeito fecha-se em vez de se reimplementar. `templates/index.html` não é tocado e continua a ter o seu `h1` por `wp:query-title`.

**Porquê a 404 entra.** O AC nomeia-a entre as superfícies de entrada e manda que o `h1` seja o da `page-hero`; o `h1` do template sai e a frase com a ligação de volta fica no `main`.

**As leituras e o "sobre base, sem tarja".** O AC da história manda as leituras manterem o título editorial "sobre base, sem tarja"; no código, `single.html:6-24` está sobre base (fundo claro) e `single-acervo_ipcn.html:6-22` tem uma faixa navy lisa — sem imagem por trás e com o ocre a 6.87:1 sobre o navy puro, dentro dos 4.5:1. A leitura implementada é "as leituras não mudam": a `page-hero` não lhes é aplicada e nenhum dos dois ficheiros é tocado. O que falha o contraste é o ocre sobre a imagem com alfa 0.68, que só existe na faixa da `page-hero` e na Home — a da Home fica fora do âmbito.

**A frase de reserva da 1.11 fica superada nesta linha.** A matriz de I/O da 1.11 prevê `Seleção de conteúdo publicado pelo IPCN.` quando o termo não tem descrição; o AC desta história manda a linha de apoio vir da descrição "quando existe" e a faixa encolher sem ela, pelo que a frase de reserva sai e aquela linha lê-se por esta. O fallback do **nome** (`Conteúdo IPCN`, derivado do slug) mantém-se — é a guarda que impede um `h1` vazio.

**Fora de âmbito, registado.** A remoção do hero no conteúdo das institucionais é escrita na base de dados: nenhum mecanismo do repositório a alcança e `AGENTS.md` manda parar antes de alterações na base de dados — fica como acção do dono, com a passagem no browser depois da purga. O `AGENTS.md` (bloco gerido) e o `sprint-status.yaml` são refrescados por outros fluxos, não por esta história.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: 0 problemas, 0 `sintaxe:`, saída 0. (A rede fica verde: o baseline imediatamente antes desta história era 5, todos em `inc/listings.php` — o commit `3fb670a` já retirara os 12 de `inc/agenda-block.php` — e esta história retira os 5 que viviam no hero fabricado.)
- `bash scripts/check-php.test.sh` — esperado: 14/14.
- `php -l wp-content/themes/ipcn-fse/inc/page-hero.php` (e nos ficheiros PHP alterados) — esperado: `No syntax errors detected`.
- `grep -rn 'ipcn_archive_hero' wp-content/ _bmad-output/` — esperado: só as specs e o ledger antigos; nada em `wp-content/`.
- `grep -c 'template-part {"slug":"page-hero"}' wp-content/themes/ipcn-fse/templates/*.html` — esperado: 1 em `page.html`, `page-noticias.html`, `archive.html`, `archive-acervo_ipcn.html` e `404.html`.
- `grep -rn 'post-title {"level":1}\|wp:heading {"level":1' wp-content/themes/ipcn-fse/templates/page.html wp-content/themes/ipcn-fse/templates/page-noticias.html wp-content/themes/ipcn-fse/templates/archive.html wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html wp-content/themes/ipcn-fse/templates/404.html` — esperado: vazio.
- `grep -n 'ipcn-page-hero' wp-content/themes/ipcn-fse/style.css` — esperado: a sobreposição e o `:empty` do apoio.
- `git diff --stat` — esperado: só `parts/page-hero.html`, `inc/page-hero.php`, `functions.php`, os cinco templates, `inc/listings.php`, `style.css`, `theme.json`, os três documentos de registo (`deferred-work.md`, a auditoria de 17/09 e o `Spec Change Log` da 1.11) e esta spec.
- Harness de sessão (`php $COMMANDCODE_SCRATCHPAD/verify-hero.php`): uma asserção por linha da matriz de I/O, incluindo o escape de um nome com `<script>` e a ausência de linha de apoio em descrição vazia, só espaços e página.

**Manual checks (if no CLI):**
- Sem WordPress local, a prova renderizada é do dono: em `stagingredesign`, depois do deploy e da purga (`?nocache=1`), contar os `h1` do HTML servido em `/noticias/`, numa Secção, num Tema, na Agenda (`/category/agenda-ipcn/`), em `/acervo/`, nas cinco institucionais e num endereço inventado; confirmar o eyebrow `IPCN · <superfície>` legível sobre a imagem, a faixa mais curta onde não há linha de apoio, e `/noticias/` com a copy de hoje.
- Remover, no editor, o bloco de hero do conteúdo de `/quem-somos/`, `/projetos/`, `/editorial/`, `/associe-se/` e `/apoia-se/` — sem isso, cada uma dessas páginas continua a servir dois `h1`.
- Confirmar que `/noticia/<slug>/` e um item do Acervo ficaram como estavam.

## Auto Run Result

**Implementado.** `parts/page-hero.html` (faixa única: navy + `assets/hero-bg.jpg` + sobreposição, eyebrow `base`, `h1` `page-title`, apoio opcional) e `inc/page-hero.php` (resolvedor `ipcn_page_hero_data()` + os três shortcodes `ipcn_page_hero_eyebrow/title/apoio`) são novos. `functions.php` ganha o `require_once` de `page-hero` (a seguir a `listings`). `page.html`, `page-noticias.html`, `archive.html`, `archive-acervo_ipcn.html` e `404.html` passam a abrir pelo part; o `h1`/`post-title` sai dos cinco. `inc/listings.php` perde `[ipcn_archive_hero]` e o seu comentário. `style.css` ganha `.ipcn-page-hero::after` (alfa >= 0.88, vence a regra da Home por ordem) e `.ipcn-page-hero-apoio:empty`. `theme.json` declara o part. Registos: `deferred-work.md` (duas entradas novas, fecho da entrada dos archives de autor/data da 1.10) e `docs/auditoria-redesign-fse-2026-09-17.md` (achado #8).

**Verificação executada.**
- `bash scripts/check-php.sh` → **0 problemas**, `0 sintaxe:`, saída **0** (a spec previa 12; ver *Desvio*).
- `bash scripts/check-php.test.sh` → **14/14**.
- `php -l` em `inc/page-hero.php`, `inc/listings.php`, `functions.php` → sem erros; `theme.json` faz parsing.
- `grep -rn 'ipcn_archive_hero' wp-content/` → vazio.
- `grep -c 'template-part {"slug":"page-hero"}'` → 1 em cada um dos cinco templates.
- `grep -rn 'post-title {"level":1}\|wp:heading {"level":1'` nos cinco templates → vazio (só `front-page.html` mantém o seu `h1`).
- Harness de sessão `php $COMMANDCODE_SCRATCHPAD/verify-hero.php` → **18/18** (uma asserção por linha da matriz mais os quatro casos-limite da guarda de vazio, incluindo o escape de um nome com `<script>`).

**Desvio do AC `check-php.sh` (12 problemas).** O número da spec é obsoleto: `baseline_revision` (`11d79f3`) já inclui o commit `3fb670a` ("fix: a agenda da home mostrava a página actual…"), que retirou os 12 problemas de `block markup` de `inc/agenda-block.php` e baixou o total de 17 para 5 (os 5 do hero do `listings.php`). Sem mais markup de bloco fabricado em PHP sob `inc/`, esta história só podia retirar os 5 do `listings.php`: o resultado é 0 problemas e saída 0, e não 12 com saída 1. O `check-php.test.sh` continua 14/14 e não apareceu nenhum `sintaxe:` novo. Não se acrescentou markup a `agenda-block.php` (proibido pelo *Never*).

**Auditoria da matriz de I/O.** O harness corre 18 cenários: 12 das 14 linhas da matriz (`instituicao`, `pagina-generica` — a degradação do hub —, `hub`, `seccao`, `tema`, `termo-sem-descricao`, `termo-sem-nome`, `acervo`, `404`, `autor`, `data`, `markup`) e 6 casos-limite (`termo-descricao-espacos`, `termo-vazio` e os quatro novos da guarda de vazio: `pagina-sem-titulo`, `pagina-titulo-espacos`, `termo-nome-nbsp`, `termo-descricao-nbsp`), todos verdes. As duas linhas sem cenário têm verificação própria: «Leituras» pelo `git diff --stat` (nenhum dos dois `single*` aparece na mudança) e «Conteúdo institucional» pela remoção no editor mais a contagem no HTML servido, registada no ledger como acção do dono.

**Triagem da revisão.** 36 achados de quatro camadas — `high` 0, `medium` 3, `low` 23, `false` 9, `maybe-false` 1 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **6 achados corrigidos por patch em 3 correcções:** A) a guarda de vazio no resolvedor — um título ou descrição só com caracteres invisíveis (incluindo o U+00A0 que o `trim` do PHP não tira) deixava um `h1` sem texto ou uma linha de apoio fantasma, e o ramo da página recebe a guarda institucional que o hero retirado já tinha (EC1, EC2, EC3 — o único `medium`); B) o comentário do `style.css:1142` que apontava para o `inc/listings.php` apagado (EC4, BH20); C) a anotação da supersessão da frase de reserva na spec da 1.11 (VG3). **9 achados diferidos em 3 entradas:** a classe do eyebrow e o escopo da regra terracota (BH3); a verificação durável do markup, do CSS e do resolvedor (BH12, BH13, BH14, EC6, VG1, VG2); e o fecho dos AC pela mão do dono (BH9, IA2). **21 rejeitados:** 9 `false` (BH2, BH5, BH6, BH8, BH11, BH15, BH18, IA1, IA4) e o `maybe-false` EC5, mais 11 `low` cujo remédio seria prosa da spec ou cujo defeito foi refutado.

**Recomendação de passagem seguinte:** `followup_review_recommended: false` — nesta primeira passagem nenhum patch foi `high` e só uma entrada patchada foi `medium` (EC1-EC3), pela regra da primeira passagem. Contagem por veredicto: `medium` 1, `low` 2.

**Fecho posterior (2026-09-29, fora do fluxo, com o dono a autorizar).** O hero que vivia no conteúdo foi removido de **15** páginas servidas por `page.html` — as cinco do AC mais dez (`/fale-conosco/`, `/politica-de-privacidade/`, `/colunistas/`, `/notas/`, `/destaques-3/`, `/diaspora/`, `/memorias/`, `/drops-antirracista/`, `/agenda-ipcn/`, `/conteudos-restritos/`) — com guardas antes, leitura de volta byte-a-byte depois (15/15) e backup em `/tmp/ipcn-1-13-backup-pages-20260929-215942.json` no servidor. O tema foi deployado em `stagingredesign` (`scripts/deploy-staging.sh`) e o HTML servido, depois da purga, medido em **21 superfícies** com `?nocache=1`: exactamente um `h1` em todas, o da `page-hero`, com o eyebrow `IPCN · <superfície>`; linha de apoio só onde existe (descrição do termo na Secção, a copy do hub, a frase do Acervo) e a faixa sem ela onde não há; a leitura mantém o cabeçalho claro e sem a `page-hero`. O `style.css` servido traz `.ipcn-page-hero::after` (nunca abaixo de 0.88) e `.ipcn-page-hero-apoio:empty`. As duas entradas do `deferred` desta spec ficam fechadas no ledger; resta o julgamento visual da faixa. Commit `6b28249`, empurrado para `origin/main`.

**Riscos residuais.** A remoção no conteúdo e a medição no HTML servido fecharam-se a 2026-09-29 (fecho posterior acima). O que continua sem prova automática é o que só o olho mede — a legibilidade do eyebrow sobre a imagem e o desenho da faixa mais curta — e a verificação durável do markup, do CSS e do resolvedor, que segue no `deferred-work.md` e no `deferred` desta spec. O mesmo levantamento deixou no ledger o `projeto-nossos-passos-vem-de-longe` (três `h1` servidos, dois deles no conteúdo) e três rascunhos com `h1` no conteúdo.

