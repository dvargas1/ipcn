---
title: 'Story 1.3 — Um cartão só, com as casas fixadas'
type: 'refactor'
created: '2026-09-28'
status: 'done'
route: 'dispatch'
review_loop_iteration: 0
baseline_commit: '5aae14c45b43b463407524bbff40ded2c6ce94db'
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** O tema tem três implementações de cartão a divergir — `ipcn-card` à mão no shortcode `ipcn_query_posts`, `ipcn-card-v2` nos blocos da `front-page`, dos archives e do `index` (e na agenda), e um `wp:group` sem classe nos archives — e cada uma mostra casas diferentes: umas etiqueta sem data, outras data sem etiqueta, outras excerto. Um Item do Acervo e uma Notícia não se leem da mesma maneira em todas as superfícies, e o Épico 2 depende de um contrato de cartão que ainda não existe.

**Approach:** Criar os dois patterns registados `ipcn/card` e `ipcn/card-feature` com as casas fixadas da tabela do AD-3/`DESIGN.md` e passar todas as listagens a renderizar o compacto dentro de `core/post-template`. É mudança de sítio e de composição do markup, não de desenho.

## Boundaries & Constraints

**Always:** As casas são só as da tabela: imagem 16:9 no compacto e 4:3 no destaque, marcador `IPCN` quando não há imagem, etiqueta conforme a superfície (`category` em Notícias, Secção e Home; `tema_acervo` no Acervo e no Tema), título e data — sem excerto. O contrato de classes é `.ipcn-card`, `.ipcn-card-media`, `.ipcn-card-noimg`, `.ipcn-card-title`, `.ipcn-card-date`. O markup do cartão vive só em `patterns/`, e nas listagens é usado dentro de `core/post-template`, com o contexto do post vindo do loop. Cada cartão é um alvo de toque só. `ipcn_query_posts` mantém-se, mas como wrapper fino: entrega o pattern registado ao API de blocos dentro do seu loop, deixando de escrever markup de cartão à mão; o `WP_Query` e a paginação manuais que já lá estão ficam como excepção tolerada (AD-2).

**Never:** Não alterar `inc/agenda-block.php` nem as regras `.ipcn-card-v2` de `style.css`: o AC «nenhum markup de cartão à mão em PHP» vale para as listagens, e a agenda — o cartão `v2` incluído — fica para a 2.2, que a fecha e retira o `v2`. Não construir a vitrine de destaque da Home (AD-15 / história 1.5): o `ipcn-card-feature` nasce sem consumidor. Não alterar `theme.json`, o mu-plugin, os formulários nem a barra de cookies. Não introduzir PHP no corpo dos patterns, nem markup dependente do loop, do post type ou do pedido. Não escrever comentários de bloco como literais em PHP.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Notícia com imagem | `post` com destaque | Cartão 16:9 com imagem, etiqueta de `category`, título e data | — |
| Notícia sem imagem | `post` sem destaque | `span.ipcn-card-noimg` com `IPCN`, à altura dos vizinhos | — |
| Item do Acervo | `acervo_ipcn` | Etiqueta de `tema_acervo`; a de `category` não sai | — |
| Tema | `/temas/<slug>` | Archive com cartões e etiqueta de Tema | — |
| Destaque | `ipcn-card-feature` | Imagem 4:3 e título em escala de headline | — |
| Listagem vazia | Sem publicações | O `query-no-results` de cada listagem mantém-se | — |

</frozen-after-approval>

## Code Map

- `wp-content/themes/ipcn-fse/patterns/` — não existe: a criar com os dois ficheiros. Patterns de tema são auto-registados a partir desta pasta (`_register_theme_block_patterns`, desde o 6.0); o `Slug:` do cabeçalho fixa `ipcn/card` e `ipcn/card-feature` (AR3) e `Inserter: false` (a core trata como falso qualquer valor que não seja `yes`/`true`) esconde-os do inseridor.
- `templates/front-page.html` — cartão `ipcn-card-v2` na secção Notícias (L41-57) e na do Acervo (L74-88), ambos já dentro de `core/post-template`; trocar o `wp:group` do cartão por `<!-- wp:pattern {"slug":"ipcn/card"} /-->`.
- `templates/archive.html:7-11`, `templates/archive-acervo_ipcn.html:24-28`, `templates/index.html:5-9` — cartão de `wp:group` sem classe (radius 14, borda muted, `post-featured-image` 16:9, título, termos, excerto); mesma troca. O `index.html` hoje não tem termos nem data; os archives têm excerto («Ler mais»/«Ver item»), que sai por não ser casa.
- `inc/listings.php` — L14-80 `ipcn_query_posts`: o cartão escrito à mão (`ipcn-card`, `ipcn-card-media`, `ipcn-card-noimg`, `ipcn-card-title`, `ipcn-card-date`) e a paginação manual. L84-138 `ipcn_archive_hero`: hero do archive, **fora de âmbito** (1.7).
- `inc/agenda-block.php:96-124` — cartão `ipcn-card-v2` com comentários de bloco literais; **não se toca** (2.2).
- `style.css` — L359-409 as regras `.ipcn-card*` que o pattern tem de satisfazer (hoje escritas para o `<a>` do shortcode: `padding` no título/data, `aspect-ratio` na `img`); L478-513 `.ipcn-card-v2` (mantém-se); L124-148 grelha do `post-template`. Falta a regra 4:3 do destaque.
- `theme.json` — paleta (navy, ink, muted, ocre, base, subtle, body) e `--wp--preset--font-family--oswald`; nada a mudar.
- `functions.php` e `inc/*.php` (fora `listings.php`) — nada a mudar: o cartão não passa por PHP.
- `AGENTS.md` — o bloco gerido afirma que `patterns/` «não existem», o que passa a ser falso.

## Tasks & Acceptance

**Execution:**
- [x] `wp-content/themes/ipcn-fse/patterns/ipcn-card.php` — criar o cartão compacto: `wp:group` com `.ipcn-card` › media `.ipcn-card-media` com o marcador (`core/html`, `span.ipcn-card-media.ipcn-card-noimg`, `aria-hidden`) e o `core/post-featured-image` 16:9 › etiqueta › `core/post-title` com `.ipcn-card-title` › `core/post-date` com `.ipcn-card-date` — a casa única do cartão.
- [x] `patterns/ipcn-card-feature.php` — criar o destaque com a mesma composição, imagem 4:3, título em escala de headline e o modifier `ipcn-card-feature` no `wp:group`; a escala de destaque precisa de um hook próprio, que a tabela do AD-3 não nomeia.
- [x] `templates/front-page.html` — trocar os dois cartões `ipcn-card-v2` por `<!-- wp:pattern {"slug":"ipcn/card"} /-->` — uma só fonte para o cartão.
- [x] `templates/archive.html`, `templates/archive-acervo_ipcn.html`, `templates/index.html` — idem sobre o grupo de cartão de cada listagem.
- [x] `inc/listings.php` — reduzir `ipcn_query_posts` a wrapper fino: entregar `ipcn/card` ao API de blocos dentro do seu loop e apagar o markup de cartão escrito à mão; o `WP_Query` legado e a paginação manual ficam.
- [x] `style.css` — reconciliar `.ipcn-card*` com o markup novo (`wp:group` em vez de `<a>`, `h2` do `post-title`, `time` do `post-date`) e acrescentar a regra 4:3 e a escala de headline do destaque.
- [x] `AGENTS.md` — corrigir o bloco gerido que nega a existência de `patterns/`.

**Acceptance Criteria:**
- Given os dois ficheiros de `patterns/`, when o tema carrega, then `ipcn/card` e `ipcn/card-feature` estão registados com `Slug:` no cabeçalho e `Inserter: false`.
- Given o markup dos dois patterns, when o leio, then tem as cinco casas e mais nenhuma, e nenhum PHP dependente do loop, do post type ou do pedido.
- Given cada template de listagem, when procuro markup de cartão, then só encontro `<!-- wp:pattern … -->` dentro de `core/post-template`.
- Given `inc/` e `templates/`, when procuro markup de cartão em PHP, then não há nenhum (fora do `v2` da agenda, que a 2.2 fecha).
- Given uma publicação sem imagem de destaque, when a página renderiza, then o cartão mostra `IPCN` e não encolhe.

## Implementation Notes

- **Slug e registo.** `Slug: ipcn/card` e `Slug: ipcn/card-feature` no cabeçalho de cada ficheiro (AR3); o slug derivado do nome do ficheiro seria `ipcn-fse/ipcn-card` e o desalinhamento faz o `core/pattern` renderizar vazio e em silêncio. Nada de `register_block_pattern` em PHP.
- **Etiqueta por superfície sem PHP.** O pattern traz dois `core/post-terms` — um com `term:"category"`, outro com `term:"tema_acervo"`. Só sai o que tem termos: `core/post-terms` devolve string vazia quando o post não tem termos naquela taxonomia, `category` não está registada para `acervo_ipcn` e `tema_acervo` só existe para `acervo_ipcn`. Cobre as quatro superfícies com um pattern só. Sai como ligação, como hoje; tornar a etiqueta inerte exigiria PHP e fica fora.
- **Marcador sem PHP.** O pattern traz sempre o `span`; com imagem, o `core/post-featured-image` cobre-o. Pôr o `.ipcn-card-media` em `display: grid` com os dois filhos na mesma célula (`grid-area: 1/1`) resolve o empilhamento sem `:has()`, sem JS e sem depender da ordem de pintura. O `aspect-ratio` fica no contentor (16:9; 4:3 no destaque) para a altura existir mesmo sem imagem — é isso que «mantém a altura dos vizinhos».
- **Contexto do post.** *(Corrigido na implementação: a versão de planeamento dizia que o `the_post()` chegava — não chega.)* `core/post-title`, `core/post-date`, `core/post-terms` e `core/post-featured-image` devolvem string vazia quando o bloco não traz `postId` no contexto (verificado em `post-title.php` da core: `if ( ! isset( $block->context['postId'] ) ) { return ''; }`). No loop, quem o fornece é o `core/post-template`, pelo filtro `render_block_context` que adiciona à volta do render dos filhos; o `wp:pattern` herda-o por aí. Fora de um loop é preciso fixá-lo à mão — é o modo de falha silenciosa do AD-14, e o que a nota do wrapper teve de resolver.
- **Perda deliberada de casas.** Os cartões dos archives perdem o excerto («Ler mais»/«Ver item») e a seta `→` do `.ipcn-card-v2`; o `index.html` ganha etiqueta e data. É a tabela do AD-3 a mandar, não deriva.
- **Wrapper `ipcn_query_posts`.** *(Corrigido na implementação.)* O pattern é lido do `WP_Block_Patterns_Registry` e o seu `content` entregue ao API de blocos com `do_blocks()` (excepção 1 do AD-1), com um filtro `render_block_context` por post à volta da chamada, a espelhar o `core/post-template` — sem ele os blocos do cartão devolvem vazio. O `WP_Query` legado e a paginação manual ficam, tolerados pelo AD-2. O literal `<!-- wp:pattern … -->` não aparece em PHP: só o slug, na docblock, para não acordar o `check-php.sh`.
- **Fora de âmbito, registado.** A vitrine da Home (1 `ipcn-card-feature` + 2 `ipcn-card`, AD-15) é a história 1.5; o `ipcn_archive_hero` (comentários de bloco literais em PHP) e a página do archive são a 1.7.
- **Auditoria da matriz (I/O).** Nenhuma linha tem teste a cobri-la: o projecto não tem runner nem CI (NFR9) e a verificação sancionada é o `check-php.sh` mais o browser (AD-13) — não se acrescenta infraestrutura de teste sem renegociar aquela restrição. Cobertura por inspecção: as linhas «com imagem», «sem imagem» e «Item do Acervo» pelo markup dos patterns (marcador e as duas etiquetas); «Tema» pelo `wp:pattern` no template do archive; «listagem vazia» por o `query-no-results` ter ficado intacto nos quatro templates. As linhas de render (rotação de etiqueta, altura sem imagem, 4:3 do destaque) só o browser em `stagingredesign` as fecha; o destaque não tem sequer consumidor antes da 1.5.
- **Estado no fim da implementação.** Árvore suja com 8 ficheiros alterados + `patterns/` novo + esta spec; nada commitado, nada deployado (o passo 3 proíbe push e operações remotas). Falta a passagem no browser em `stagingredesign` (deploy + `litespeed-purge all` + `?nocache=1`), que fecha as linhas de render da matriz.

## Spec Change Log

Vazio: nenhum achado da revisão encaminhou para `bad_spec` nem para `intent_gap`, logo não houve loopback.

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo; **EC** = caça a casos-limite; **VG** = lacunas de verificação.
Veredictos: `high`/`medium`/`low` (defeito real), `false` (refutado). Agrupamento e encaminhamento no fim.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 1 | BH | O comentário do laço (`inc/listings.php:68-71`) dizia que o `the_post()` chegava para o contexto | `low` | Real: contradizia o código imediatamente abaixo. Remédio trivial: comentário reescrito. |
| 2 | BH | O docblock (`inc/listings.php:13-16`) diz que o cartão entra «pelo bloco `wp:pattern`» | `low` | Real: o bloco `wp:pattern` nunca é renderizado neste caminho; lê-se o `content` do registry e passa-se a `do_blocks()`. Docblock reescrito. |
| 3 | BH | Sem guarda para pattern não registado, o wrapper emite grelha vazia com paginação | `medium` | Real e demonstrado: `''` → `do_blocks('')` → `''`, e a paginação vem de `max_num_pages`. Guarda acrescentada. |
| 4 | BH | O mecanismo do wrapper desvia do AD-14 e muta estado partilhado | `false` | O wrapper entrega o pattern **registado** ao API de blocos (excepção 1 do AD-1) e o filtro é adicionado e removido à volta de uma chamada — é o que o `core/post-template` faz. O mecanismo ficou registado nas notas. |
| 5 | BH | O marcador reusa a classe do contentor (`ipcn-card-media ipcn-card-noimg`) | `low` | Real: as regras do contentor caiam sobre o marcador, que tinha de reagir com `display:flex` de igual especificidade. Classe redundante removida; a AD-3 pede só `ipcn-card-noimg`. |
| 6 | BH | O chrome do cartão está definido duas vezes; a regra genérica da grelha vencia por `!important` | `low` | Real: editar `.ipcn-card` não mudava nada dentro de uma grelha. Regra genérica passou a `:not(.ipcn-card)`. O resíduo (a regra genérica ainda servir grupos alheios) vai para `deferred-work`. |
| 7 | BH | `border-style: solid` morto; quatro donos da borda e do fundo | `low` | A declaração morta é real (removida). Os atributos do pattern sobrepostos ao CSS são o idioma da casa — os cartões antigos faziam o mesmo. |
| 8 | BH | Os overrides do destaque só vencem por ordem de ficheiro (especificidade igual) | `low` | Real: `.ipcn-card-feature .ipcn-card-media` vs `.ipcn-card .ipcn-card-media`. Subiram para `.ipcn-card.ipcn-card-feature`. |
| 9 | BH | `isLink:true` na imagem cria um segundo link para o mesmo destino, inerte ao ponteiro | `medium` | Real: a sobreposição do título (`::after` com `inset:0`) torna o link da imagem inalcançável pelo ponteiro, mas continua a ser uma paragem de tabulação em cada cartão. Passou a `isLink:false`. |
| 10 | BH | O cartão novo ficou fora da lista de `:focus-visible` e a sobreposição não está isolada | `low` | Real: a lista cobria `.ipcn-card-v2`, que é o que os cartões da home eram. Selector `.ipcn-card` acrescentado e `isolation: isolate` no cartão. |
| 11 | BH | As etiquetas são incondicionais; falta a regra para ambas as taxonomias e a dependência não está nos constrangimentos | `false` | `category` não está registada para `acervo_ipcn` e `tema_acervo` só existe para `acervo_ipcn` (`inc/content-model.php`), logo os dois termos não coexistem num post. A dependência está nas notas. |
| 12 | BH | A matriz não tem linha para a superfície `[ipcn_query_posts]` | `low` | Real. Rejeitado: o remédio edita esta spec; a substância ficou em `deferred-work` (resolução do slug). |
| 13 | BH | `Inserter: false` afirmado sem versão da core; o vocabulário documentado é `Inserter: no` | `false` | Verificado em `class-wp-theme.php:2001-2005`: qualquer valor fora de `yes`/`true` resolve em falso, logo `false` esconde do inseridor. |
| 14 | BH | O destaque não tem consumidor antes da 1.5, logo a linha da matriz não é exercitável | `low` | Real e por desenho: o AD-15 dá-lhe consumidor na 1.5. Diferido. |
| 15 | BH | `AGENTS.md` omite o contrato de classes, a armadilha do contexto e a dívida do `v2`, e contradiz o épico | `low` | Real; o remédio edita contexto de agente. Diferido. |
| 16 | BH | O ledger diz `in-progress` e a spec `in-review` | `low` | Real, criado pela sincronização do passo 3. Ledger sincronizado. |
| 17 | BH | O `check-php.sh` só polícia `inc/`, logo os patterns levam só `php -l` | `low` | A cobertura dos patterns é por desenho (o markup vive em `patterns/`); a excepção do AD-1 está escrita no AR1. Diferido com a resolução de slugs. |
| 18 | BH | Comportamento móvel diverge (grelhas a 900/600 vs 768) e a tipografia do cartão não tem ajuste estreito | `low` | As duas regras de colapso são pré-existentes; a passagem a 320px é a 1.9. Diferido. |
| 19 | BH | `#a85a32` fixo em dois sítios novos sem apontar a origem | `false` | É o idioma corrente do ficheiro (12 ocorrências) e o `AGENTS.md` proíbe-o como slug. Não há defeito novo. |
| 20 | EC | Pattern não registado → grelha vazia | `medium` | Duplicado do #3; mesma guarda. |
| 21 | EC | Slug não registado → todas as listagens com cartões vazios | `medium` | Mesmo defeito do #3 pela via dos templates, onde não há guarda possível. Diferido. |
| 22 | EC | Sem termos, o título fica a 10px da imagem e desalinha dos vizinhos com etiqueta | `low` | Real: o primeiro slot de texto não reservava o mesmo afastamento. Regra do irmão acrescentada. |
| 23 | EC | `isLink:true` → segundo link inerte | `medium` | Duplicado do #9. |
| 24 | EC | Uma imagem transparente deixa ver o marcador por baixo | `low` | Real: o marcador e a imagem partilham a célula da grelha. Regra `:has()` acrescentada. |
| 25 | EC | Ambos os termos → duas etiquetas | `false` | Duplicado do #11; impossível com o modelo de conteúdo actual. |
| 26 | EC | Ledger vs spec discordam | `low` | Duplicado do #16. |
| 27 | EC | O cartão do wrapper deixou de pedir `medium_large` | `low` | Real: o `post-featured-image` sem `sizeSlug` pede o tamanho por omissão do bloco. `sizeSlug` fixado nos dois patterns. |
| 28 | EC | Removida a classe `ipcn-card-v2` → perdeu-se o anel de foco documentado | `medium` | Duplicado do #10 (a lista de `:focus-visible` cobria o `v2`). |
| 29 | EC | `AGENTS.md` afirma que nenhum markup de cartão é escrito à mão em PHP | `medium` | Real: a agenda ainda escreve. O remédio edita contexto de agente. Diferido. |
| 30 | EC | A `Approach` diz «nem de desenho», mas os archives perdem o excerto | `low` | Real, mas o `Always` congelado ratifica «sem excerto» e as notas registam a perda. Rejeitado: o remédio edita esta spec. |
| 31 | VG | A superfície do wrapper pode emitir uma grelha sem cartões e nada o vê | `medium` | Pré-verificado; a demonstração confirma-se (slug trocado → `''` + paginação). O contexto só se observa com um runtime WordPress. Diferido. |
| 32 | VG | Os slugs `wp:pattern` dos templates não são resolvidos por verificação nenhuma | `medium` | Pré-verificado; é o mesmo modo de falha silenciosa que a spec já nomeia. Diferido com o remédio (verificação no `check-php.sh` + caso no `check-php.test.sh`). |
| 33 | VG | `AGENTS.md:42` é falso para o repo (a agenda escreve cartão em PHP) | `low` | Duplicado do #29. |

**Agrupamento e encaminhamento**

- **Grupo A — guarda ausente no wrapper** (#3, #20): `patch` — a guarda do wrapper e o `_doing_it_wrong` estão no código.
- **Grupo B — a resolução do slug não é verificada** (#21, #31, #32, #17): `defer` — o remédio é infraestrutura de verificação (resolver cada `Slug:` de `patterns/` contra os `wp:pattern` dos templates e contra o literal do wrapper, no `scripts/check-php.sh`, com um caso no `check-php.test.sh`), mais a passagem no browser dos ecrãs que chamam o shortcode.
- **Grupo C — comentários e docblock do wrapper** (#1, #2): `patch` — o texto passou a descrever o mecanismo real.
- **Grupo D — marcador de ausência** (#5, #24): `patch` — classe redundante fora, `:has()` acrescentado.
- **Grupo E — um só alvo de toque** (#9, #23): `patch` — `isLink:false`.
- **Grupo F — foco e empilhamento do cartão** (#10, #28): `patch` — selector e `isolation`.
- **Grupo G — primeiro slot de texto** (#22): `patch`.
- **Grupo H — tamanho da imagem** (#27): `patch`.
- **Grupo I — chrome em duplicado** (#6, #7): `patch`; o resíduo da regra genérica vai para `deferred-work`.
- **Grupo J — especificidade do destaque** (#8): `patch`.
- **Grupo K — ledger vs spec** (#16, #26): `patch`.
- **Grupo L — contexto de agente** (#15, #29, #33): `defer` — o remédio edita `AGENTS.md`.
- **Grupo M — comportamento responsivo do cartão** (#18): `defer` — a 1.9 é dona da passagem a 320px.
- **Grupo N — o destaque sem consumidor** (#14): `defer` — o AD-15 dá-lhe consumidor na 1.5.
- **Grupo O — registo da própria spec** (#12, #30): `rejeitar` — o remédio edita esta spec.
- **Refutados:** #4, #11, #13, #19, #25.

**Cascata:** nenhum grupo encaminha para `intent_gap` nem `bad_spec` — não há desvio da intenção congelada nem defeito de spec que o código devesse ter evitado. Não há loopback (`review_loop_iteration` fica 0). Os grupos A, C–K foram aplicados; B, L, M, N vão para `deferred-work.md`; O é rejeitado. Depois do *patch*, o `check-php.sh` mantém os mesmos 17 problemas aceites e os gates da spec continuam a passar.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: sintaxe limpa e o **mesmo** estado aceite da 1.2 — sai 1 com os 17 `block markup` de `inc/listings.php` (`ipcn_archive_hero`) e `inc/agenda-block.php` (`ipcn_home_agenda`). Linha vermelha a mais é defeito desta história.
- `grep -rn -- 'ipcn-card-v2' wp-content/themes/ipcn-fse/templates/ wp-content/themes/ipcn-fse/inc/` — esperado: só `inc/agenda-block.php` (2.2), nenhum template.
- `grep -rn -- 'ipcn-card' wp-content/themes/ipcn-fse/inc/ wp-content/themes/ipcn-fse/templates/` — esperado: nenhum markup de cartão escrito à mão.
- `grep -rn -- 'wp:pattern' wp-content/themes/ipcn-fse/templates/ wp-content/themes/ipcn-fse/inc/` — esperado: uma referência por listagem, dentro de `core/post-template`, mais a do wrapper em `inc/listings.php`.
- `ls wp-content/themes/ipcn-fse/patterns/` — esperado: `ipcn-card.php`, `ipcn-card-feature.php`.

**Manual checks (if no CLI):**
- Não há WordPress local: o registo dos patterns, o contexto do post e a etiqueta por superfície só se confirmam no browser em `stagingredesign`, após deploy e purga (`?nocache=1`); sem essa passagem a verificação está incompleta.
- Por inspecção: os patterns têm as cinco casas; nenhum PHP/template escreve markup de cartão; nenhum pattern tem PHP dependente do ambiente. No browser: um cartão sem destaque mostra `IPCN` à altura dos vizinhos; um Item do Acervo mostra a etiqueta de Tema e nunca a de categoria.
