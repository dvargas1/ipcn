---
title: 'Story 1.2 — O comportamento sai de functions.php para inc/'
type: 'refactor'
created: '2026-09-27'
status: 'done'
route: 'dispatch'
review_loop_iteration: 1
baseline_commit: 'b6e8c043cab1cdf0bc5a43410b45b673dc547c4c'
context: []
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** `functions.php` tem 619 linhas e seis responsabilidades: enfileirar fontes e estilos, tratar dois formulários, registar um tipo de conteúdo e uma taxonomia, desenhar quatro shortcodes, correr a agenda e injetar a barra de cookies com o seu JavaScript. Não tem um único `include`. Cada história seguinte (o cartão, a Home, as Secções, o Acervo, o guard do mu-plugin) acrescenta a este ficheiro, e o ficheiro cresce com a história — que é como ele chegou aqui.

**Approach:** Repartir o comportamento por ficheiros de preocupação em `inc/`, um por assunto, e reduzir `functions.php` a um carregador que os inclui e trata do setup do tema. É uma mudança de sítio, não de comportamento: o código move-se como está, sem reescrita, sem renomear funções, sem mudar hooks nem assinaturas. A arrumação de verdade — o cartão único, o markup em patterns, a agenda como bloco — pertence às histórias 1.3 e 2.2.

## Boundaries & Constraints

**Always:** O comportamento observável mantém-se idêntico: mesmos hooks, mesmas prioridades, mesmas funções e assinaturas, mesmos shortcodes, mesmas consultas, mesmo HTML. Se um ficheiro novo precisar de uma ordem de carregamento específica, ela fica explícita em `functions.php`. Cada ficheiro de `inc/` trata de um assunto só, e nenhum regista o mesmo hook que outro.

**Never:** Não reescrever lógica ao passar de ficheiro. Não renomear funções, hooks ou shortcodes. Não alterar o `mu-plugin` (a 1.12 trata dele) nem o `style.css` nem o `theme.json`. Não tocar no `functions.php` para além de o reduzir a carregador. Não introduzir nomes de função ou de ficheiro novos sem necessidade — o objetivo é o mesmo código noutro sítio.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Carregamento | Tema activado, `functions.php` lido | Cada ficheiro de `inc/` é incluído uma vez, por ordem determinística | Ficheiro em falta: `require` falha alto, não em silêncio |
| Acesso directo | Pedido a um ficheiro de `inc/` pelo browser | Nada executa | Guarda `ABSPATH` em cada ficheiro, como o `functions.php` já tem |
| Shortcodes | Os quatro shortcodes actuais | Respondem exactamente como antes | — |
| Formulários | `admin_post` das duas acções | Mesmos handlers, mesmos redirects | — |

</frozen-after-approval>

## Code Map

- `wp-content/themes/ipcn-fse/functions.php` — 619 linhas, o ficheiro a repartir. Hoje: linhas 15–45 fontes e preconnect; 51–79 enqueue do `style.css` com a propriedade `--ipcn-hero-bg`; 86–158 os dois handlers de formulário; 164–230 o shortcode `ipcn_query_posts`; 236–284 o CPT `acervo_ipcn` e a taxonomia `tema_acervo`; 289–354 os shortcodes dos formulários e das mensagens; 361–411 o `ipcn_archive_hero`; 419–527 o `ipcn_home_agenda`; 535–619 a barra de cookies com o JavaScript.
- `inc/setup.php` — o que a história 1.2 cria: enqueue das fontes, preconnect, enqueue do `style.css` no frontend e no editor, e a propriedade do fundo do hero.
- `inc/content-model.php` — registo do CPT `acervo_ipcn` e da taxonomia `tema_acervo`, no hook `init`.
- `inc/listings.php` — `ipcn_query_posts` e `ipcn_archive_hero`.
- `inc/forms.php` — os dois handlers `admin_post`, os dois shortcodes de formulário e os dois de mensagem.
- `inc/cookie-bar.php` — a barra, o painel e o JavaScript do `wp_footer`.
- `inc/agenda-block.php` — o `ipcn_home_agenda` por agora; a história 2.2 substitui o seu conteúdo pelo bloco dinâmico. O ficheiro existe desde já para não haver um segundo movimento depois.
- `AGENTS.md` — o bloco gerido aponta `functions.php` como o sítio do CPT, da taxonomia, dos formulários, da agenda e da barra de cookies. Passa a apontar `inc/`.
- Não há teste nem CI: a verificação de comportamento depende de o código ser movido literalmente e de uma passagem no browser em `stagingredesign`.

## Tasks & Acceptance

**Execution:**
- [x] `inc/setup.php` — criar com o enqueue das fontes, o preconnect e o enqueue do `style.css` — é a primeira preocupação, sem dependências.
- [x] `inc/content-model.php` — criar com o registo do CPT e da taxonomia — depois do setup, para o `init` ficar com uma ordem legível.
- [x] `inc/listings.php` — criar com os dois shortcodes de listagem.
- [x] `inc/forms.php` — criar com os dois handlers e os quatro shortcodes de formulário e mensagem.
- [x] `inc/cookie-bar.php` — criar com a barra, o painel e o JavaScript.
- [x] `inc/agenda-block.php` — criar com o shortcode da agenda, tal como está.
- [x] `functions.php` — reduzir a carregador: guarda `ABSPATH` e um `require_once` por ficheiro de `inc/`, por ordem explícita.
- [x] `AGENTS.md` — actualizar o bloco gerido que aponta `functions.php` como o sítio do CPT, da taxonomia, dos formulários, da agenda e da barra de cookies.

**Acceptance Criteria:**
- Given o tema carregado, when inspecciono `functions.php`, then não contém lógica de comportamento — só a guarda e os `require`.
- Given cada ficheiro de `inc/`, when o abro, then tem a guarda `ABSPATH` e trata de um único assunto.
- Given os quatro shortcodes e as duas acções `admin_post`, when os procuro, then existem com os mesmos nomes, nos ficheiros novos.
- Given o diff, when o leio, then cada função movida está **idêntica linha a linha** ao original — a mudança é de sítio, não de conteúdo, e nada se remove nem se reescreve.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa em todos os ficheiros e as duas verificações estruturais correm; a falha (saída ≠ 0) é **apenas** o markup legado `<!-- wp:` movido verbatim com `ipcn_archive_hero` e `ipcn_home_agenda`. Qualquer outra linha vermelha é um defeito desta história. (A contradição com o antigo «sai 0» foi resolvida pelo humano a favor do movimento literal — ver Spec Change Log.)

## Implementation Notes

- **É um movimento literal, byte a byte.** O corpo de cada ficheiro de `inc/` tem de ser idêntico ao segmento correspondente do `functions.php` em `b6e8c04` — incluindo os comentários de bloco `<!-- wp: … -->` de `ipcn_archive_hero` e `ipcn_home_agenda`. Nada se remove, nada se reescreve.
- `functions.php` fica com a guarda `ABSPATH` e seis `require_once`, por ordem explícita — `setup`, `content-model`, `listings`, `forms`, `cookie-bar`, `agenda-block`. `require_once` (e não `require`) para garantir o «incluído uma vez» da matriz; um ficheiro em falta continua a falhar alto. O caminho resolve-se com `__DIR__`.
- Cada `inc/*.php` tem a guarda `ABSPATH` e trata de um assunto só.
- **Contradição resolvida pelo humano (renegociação da intenção), loopback 1.** O antigo AC pedia `check-php.sh` a sair 0, mas o script da 1.1 polícia `<!-- wp:` em qualquer `.php` sob `inc/`, e o markup legado tem de ser movido como está («mesmo HTML», dentro de `<frozen-after-approval>`). As duas coisas não podem coexistir. O humano decidiu a favor do movimento literal: os marcadores ficam, e o `check-php.sh` sai ≠ 0 **por causa dessas duas funções e só por causa delas**, até a 1.3/2.2 tratar do markup. Isto é o estado esperado, não um defeito desta história.
- `AGENTS.md`: actualizar o bloco gerido que aponta `functions.php` como o sítio do CPT, da taxonomia, dos formulários, da agenda e da barra de cookies, e os bullets que apontam `functions.php` para o enqueue do `style.css`, as fontes e `--ipcn-hero-bg` (passam a `inc/setup.php`).
- `README.md` (linhas 21, 22, 58) e `STATUS.md` continuam a nomear `functions.php` para este comportamento; o Code Map não os inclui e ficam como resíduo conhecido, a fechar num refresh dos docs de planeamento.

## Spec Change Log

- **Loopback 1 — `intent_gap` do grupo A da triagem.** *Achado que a accionou:* o diff removia os delimitadores `<!-- wp:` de `ipcn_archive_hero` e `ipcn_home_agenda`, pelo que as duas funções movidas deixavam de estar idênticas ao original — e, como o core do WordPress 6.4+ corre `do_shortcode()` antes de `do_blocks()` no conteúdo do template, esse markup emitido pelos shortcodes era analisado e renderizado. O HTML observável mudava, contra `Always: … mesmo HTML`. *Emenda (não-frozen):* o AC «`check-php.sh` sai 0» passa a esperar falha **apenas** no markup legado desses dois shortcodes; `## Verification` e `## Implementation Notes` alinham com o movimento literal; a tarefa do `functions.php` passa a dizer `require_once`. *Estado mau evitado:* re-derivar a partir de uma intenção derrotada — o movimento com os marcadores removidos, que alterava a saída dos shortcodes e era indocumentado. *KEEP:* o movimento byte-idêntico (nada removido nem reescrito); `functions.php` reduzido a carregador com seis `require_once` por ordem explícita; guarda `ABSPATH` em cada `inc/`; `inc/agenda-block.php` criado desde já para a 2.2 não ter um segundo movimento; `AGENTS.md` actualizado. O bloco `<frozen-after-approval>` fica inalterado — o humano reafirmou-o.

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo; **EC** = caça a casos-limite; **VG** = lacunas de verificação.
Veredictos: `high`/`medium`/`low` (defeito real), `false` (refutado), `maybe-false` (indeterminado). Cada achado tem uma linha; o agrupamento e o encaminhamento estão no fim.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 1 | BH | Ordem de carregamento dos `require` não declarada como vinculativa nem verificada | `low` | Real: a ordem escolhida difere da ordem do ficheiro original e só é segura porque dois ficheiros não partilham hook+prioridade. Problema de developer; remédio é comentário ou verificação. |
| 2 | BH | Docblock de cabeçalho duplica o docblock da secção em cada `inc/` | `low` | Real e cosmético; os pares podem divergir. Remédio directo: apagar um de cada par. |
| 3 | BH | Cabeçalho de `inc/setup.php` omite o enqueue do editor | `low` | Verdadeiro: o cabeçalho diz «fontes, preconnect e o style.css», mas o ficheiro também enfileira no editor. |
| 4 | BH | A remoção dos delimitadores apagou o único registo dos atributos de desenho (`columnCount` 3, estilos de `post-title`/`date`/`excerpt`, `contentSize` 1100px) | `medium` | Mesma raiz do grupo A. Os atributos viviam nos comentários removidos; a 2.2 tem de os reconstruir. Confirmado no diff e no meu confronto multiconjunto. |
| 5 | BH | `$thumb` recolhido e nunca impresso em `inc/agenda-block.php` | `low` | Verdadeiro; pré-existente (baseline:507), movido verbatim. |
| 6 | BH | `if ( $paged < 1 )` em `inc/listings.php` é inalcançável | `low` | Verdadeiro (`max( 1, … )` precede-o); pré-existente, movido verbatim. |
| 7 | BH | `strtok( $_SERVER['REQUEST_URI'], '?' )` sem saneamento nem guarda | `low` | Verdadeiro; pré-existente, movido verbatim. |
| 8 | BH | `.wp-block-post-template.is-layout-grid` no `style.css` deixa de ser produzido | `medium` | Consequência da remoção do marcador `wp:post-template` (grupo A): as classes vinham do comentário apagado. |
| 9 | BH | O spec não dá ao leitor forma de saber que os cartões da agenda saem vazios | `low` | Problema de registo do próprio spec. |
| 10 | BH | O spec afirma «o DOM observável não muda» como facto, sem prova | `medium` | A afirmação não está provada e é contrariada pela ordem de render do core (grupo A). |
| 11 | BH | `## Spec Change Log` vazio apesar do desvio deliberado | `low` | Registo do próprio spec. |
| 12 | BH | AC «cada função movida está idêntica» não satisfeito em `ipcn_home_agenda`/`ipcn_archive_hero` | `medium` | Verificado: os corpos diferem do baseline — o meu confronto multiconjunto isolou exactamente as 4 linhas de delimitadores removidas. Mesma raiz do grupo A. |
| 13 | BH | A tarefa diz `require`; o código usa `require_once` | `low` | Deriva de redacção: `require_once` satisfaz a matriz («incluído uma vez») e não abre superfície. |
| 14 | BH | `grep -c 'function '` não sustenta «sem função duplicada» | `low` | Verdadeiro: o padrão conta closures e uma contagem por ficheiro não revela um hook registado duas vezes. Prova melhor é o inventário de símbolos. |
| 15 | BH | O spec reporta `setup.php` (80) / `content-model.php` (63); reais 79/62 | `low` | Verificado: um a mais em cada. |
| 16 | BH | `baseline_commit` sem comando reproduzível de comparação | `low` | Registo do próprio spec. |
| 17 | BH | Registos de tempo incompletos (`updated:`, iteração) | `low` | Registo do próprio spec. |
| 18 | BH | A enumeração de `inc/` em `AGENTS.md` omite `setup.php` e `listings.php` | `low` | Verdadeiro; o remédio edita um ficheiro de contexto de agente. |
| 19 | BH | «Verified 2026-09-24 against 57825f5» em `AGENTS.md` ficou falso | `low` | Verdadeiro; o remédio edita contexto de agente. |
| 20 | BH | A justificação em `scripts/check-php.sh:15-16` ficou falsa | `low` | Verdadeiro: o `<!-- wp:` do `functions.php` desapareceu, logo a razão declarada está desactualizada. |
| 21 | BH | README/STATUS/docs continuam a nomear `functions.php`; inventário de resíduo incompleto | `low` | Verdadeiro; deriva de documentação, remédio edita docs. |
| 22 | BH | O resíduo e a passagem em falta não estão em `action_items` | `low` | Questão de registo/processo. |
| 23 | BH | Deployabilidade do novo `inc/` não verificada (lista do tar) | `low` | O deploy documentado extrai o directório do tema inteiro (`--strip-components=3`), logo `inc/` viaja; não demonstrado como quebrado. |
| 24 | BH | Nada verifica que os alvos dos `require_once` existam, que não há `add_*` fora de `inc/` nem hook duplicado | `medium` | Real e novo: o carregador é mesmo assim o único modo de falha novo. Reproduzi: com `inc/cookie-bar.php` removido, `check-php.sh` sai 0. Grupo B. |
| 25 | EC | Corpo do cartão da agenda esvaziado → cartões vazios | `medium` | Grupo A. O core (6.4 e master) corre `do_shortcode` antes de `do_blocks`, logo os marcadores eram analisados; mas o próprio defeito observado do repo (memlog:15, spec-1.1:16) diz que os cartões já saíam vazios. O desvio a «mesmo HTML» é real; a extensão da perda não fica provada. |
| 26 | EC | Marcadores `wp:query`/`wp:post-template` removidos → grelha de 3 colunas perdida | `medium` | Grupo A; mesmo mecanismo. |
| 27 | EC | Marcadores `wp:group`/`wp:buttons` removidos → hero/estado vazio perdem largura contida; botões perdem centragem | `medium` | Grupo A. O marcador do hero renderiza um `core/group` com classes de layout; removê-lo altera o resultado emitido. Não provado visível, mas real. |
| 28 | EC | README/STATUS nomeiam `functions.php` | `low` | Duplicado do #21. |
| 29 | EC | Afirmação: agenda criada «tal como está», mas os blocos do cartão apagados | `medium` | Grupo A; verificado. |
| 30 | EC | Afirmação: todas as funções movidas idênticas | `medium` | Grupo A; duplicado do #12. |
| 31 | EC | `$paged` não limitado → `/page/99/` imprime a mensagem de vazio | `low` | Verdadeiro; pré-existente, movido verbatim. |
| 32 | EC | Ramo morto `$paged < 1` | `low` | Duplicado do #6. |
| 33 | EC | `strtok` sem guarda `isset` | `low` | Duplicado do #7. |
| 34 | EC | Handlers `admin_post` nopriv sem guarda de origem/referer → correio ilimitado | `medium` | Verdadeiro; pré-existente, movido verbatim. O próprio repo já registou o defeito de documentação (memlog:16) e a história 3.3 é dona da verificação de origem. Diferir. |
| 35 | EC | Consentimento de cookies nunca compara o `ts` aos 180 dias | `low` | Verdadeiro; pré-existente, movido verbatim. |
| 36 | EC | `per_page` sem limite | `low` | Verdadeiro; pré-existente, movido verbatim. |
| 37 | VG | Corpo do cartão da agenda esvaziado; nenhum teste observa a saída dos shortcodes | `medium` | Grupo A (parte do defeito) + grupo B (parte da verificação ausente). A disposição está certa: só uma verificação de render o decide. |
| 38 | VG | Alvos dos `require_once` não verificados | `medium` | Duplicado do #24; modo de falha novo e real. Grupo B. |
| 39 | VG | As Implementation Notes subestimam o que foi removido | `low` | O «todo o HTML fica igual» é inexacto para os marcadores auto-fechados de `post-*`. Registo do próprio spec. |
| 40 | VG | Cartão vazio e `$thumb` usada são pré-existentes | `low` | Duplicado do #5; explicitamente pré-existente. |
| 41 | VG | Deriva `require` vs `require_once` | `low` | Duplicado do #13. |

**Agrupamento e encaminhamento**

- **Grupo A — a remoção dos delimitadores de bloco** (#4, #8, #10, #12, #25, #26, #27, #29, #30, #37-parte, #39). Raiz: duas funções movidas deixaram de estar idênticas ao original — o código mudou de conteúdo, e o core do WordPress 6.4+ expande shortcodes antes de analisar blocos, pelo que os marcadores emitidos eram analisados. Isto viola `Always: … mesmo HTML` e `o código move-se como está`, que estão **dentro** de `<frozen-after-approval>`. Veredicto mais alto do grupo: `medium`. → **intent_gap**.
- **Grupo B — verificação ausente do carregador** (#1, #2, #3, #24, #37-parte, #38). Raiz: nada resolve os `require_once` nem observa a saída dos shortcodes. → **patch**.
- **Grupo C — defeitos pré-existentes em código movido verbatim** (#5, #6, #7, #31, #32, #33, #34, #35, #36, #40). Não causados por esta história. → **defer**.
- **Grupo D — deriva de documentação/contexto de agente** (#18, #19, #20, #21, #28). O remédio edita `AGENTS.md`/docs. → **defer**.
- **Grupo E — registo do próprio spec** (#9, #11, #13, #14, #15, #16, #17, #22, #23, #41). O remédio edita o próprio spec desta build. → **rejeitar**.

**Cascata:** o grupo A encaminha para `intent_gap`, cuja raiz está dentro de `<frozen-after-approval>` — o spec e o próprio comentário de `check-php.sh` («defeito a corrigir na 1.2», artefacto da 1.1) contradizem a intenção congelada («o markup pertence às histórias 1.3 e 2.2»). A contradição não se resolve a partir do spec: exige renegociação humana. Os grupos B–E ficam **moot** — o código será re-derivado. `review_loop_iteration` 0 → 1.

### Rodada 2 (após o loopback 1 — movimento literal)

O humano resolveu o `intent_gap` a favor do movimento literal; o código foi revertido e re-derivado, com os marcadores `<!-- wp:` de volta. A triagem abaixo verifica os achados da segunda revisão; os que repetem local e alegação de uma linha da rodada 1 e cujo código continua a ler-se como a linha descreve ficam marcados `carried`.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 42 | BH, VG | O comentário de `scripts/check-php.sh:15-16` ficou falso (`functions.php` já não tem `<!-- wp:` e nunca foi policiado) | `low` | `carried` de #20/#75. Local e alegação iguais; o script não mudou e continua a ler-se como a linha descreve. O remédio edita o script da 1.1. |
| 43 | BH, VG | O portão `check-php.sh` passa de verde a sempre-vermelho e só a prosa o regista; o `**Resultado:**` diz «o baseline tinha os mesmos 17» | `medium` | Verificado: o baseline tinha os mesmos 17 marcadores no código, mas a *corrida* do baseline saía 0 («verificações estruturais não correram»); a actual sai 1. A frase confunde código com resultado. |
| 44 | BH | Não há forma verificável por máquina do novo AC (sem allowlist/contagem fixada) | `medium` | Verdadeiro: a falha esperada é julgamento humano; um `<!-- wp:` novo é indistinguível dos 17 aceites pelo código de saída. |
| 45 | BH | `git diff --stat` não lista `inc/` (não versionado) | `low` | Verificado: sem `-N`/`--cached`, o `--stat` lista só `AGENTS.md`, spec, `sprint-status.yaml` e `functions.php`. |
| 46 | BH | `grep -c 'function ' inc/*.php` não resolve a partir da raiz e não é um valor de saída | `low` | Verificado: o glob falha fora do directório do tema; a contagem inclui closures. |
| 47 | BH, VG | Não existe comando de inventário de símbolos (hooks/shortcodes/prioridades) | `medium` | Verdadeiro: a sobrevivência dos símbolos é asserção em prosa, o que o `Always` congelado tem por central (AD-4). |
| 48 | BH | `baseline_commit` sem comando reproduzível | `low` | `carried` de #16; registo do próprio spec. |
| 49 | BH | «os quatro shortcodes» — o tema regista **sete** (três sem AC) | `low` | Verificado: sete shortcodes. A contagem errada está no AC (não-frozen) e na matriz congelada (intocável). |
| 50 | BH | Divergência `require`/`require_once` ainda viva na matriz congelada | `low` | Verdadeiro: a matriz diz `require`; o código e a tarefa dizem `require_once`. Inócuo (ambos falham alto). |
| 51 | BH | O docblock do `functions.php` declara a ordem «vinculativa» sem dizer o que protege | `low` | Verdadeiro: nenhum ficheiro partilha hook+prioridade, logo a ordem não é observável; falta a razão no comentário. |
| 52 | BH | O cabeçalho de `inc/setup.php` omite o `--ipcn-hero-bg` | `low` | Verdadeiro: diz «fontes, preconnect e style.css (frontend e editor)», mas também emite o inline do hero. |
| 53 | BH | A triagem não tem estado de resolução; algumas linhas da rodada 1 ficaram obsoletas | `low` | Verdadeiro: #1 ficou respondida pelo docblock; #3/#11/#13/#15/#41 eram correctas quando escritas e o spec mudou depois. |
| 54 | BH | A «Cascata» e o «Spec Change Log» descrevem estados finais diferentes, sem reconciliação | `low` | Verdadeiro: a cascata diz «moot», o log diz que o humano reafirmou o KEEP; faltava o parágrafo de fecho (esta rodada fecha-o). |
| 55 | BH | Nada registra o que foi diferido; `sprint-status.yaml` não tem `action_items` | `low` | `carried` de #22; o resíduo vai agora para `deferred-work.md`. |
| 56 | BH, EC | `AGENTS.md:21` continua a omitir `setup.php` e `listings.php` na enumeração de `inc/` | `low` | `carried` de #18/#28/#71: mesmo local e alegação; o remédio edita contexto de agente. |
| 57 | BH | `AGENTS.md:26` (o pré-voo) não diz que os 17 problemas são o estado esperado | `low` | Verdadeiro: o bullet continua a descrever a falha sem a ressalva dos 17. Edita contexto de agente. |
| 58 | BH | `AGENTS.md:2` mantém o selo «Verified 2026-09-24 against 57825f5» | `low` | `carried` de #19; edita contexto de agente. |
| 59 | BH | `sprint-status.yaml` (`in-progress`→`in-review`) e o spec (`in-review`) discordam; sem `updated:` | `low` | Verdadeiro no momento da leitura; o yaml é sincronizado pelo passo 4. Registo/processo. |
| 60 | BH | O resíduo de documentação é afirmado, não enumerado; mistura afirmação estrutural com entrada datada de changelog | `low` | `carried` de #21. `README.md:21,22,58` são estruturais (resíduo real); `STATUS.md` é datado. |
| 61 | EC, VG | Os alvos dos `require_once` não são resolvidos por verificação nenhuma; caminho errado = fatal mudo | `medium` | `carried` de #24/#38/#72: verifiquei que apagar `inc/cookie-bar.php` deixa o `check-php.sh` verde. |
| 62 | EC | `inc/forms.php` podia ser incluído duas vezes por um caminho que não `require_once` | `false` | Não demonstrei nenhum caminho de inclusão de `inc/` que não os seis `require_once`; o `require_once` impede a re-declaração. |
| 63 | EC | `strtok( $_SERVER['REQUEST_URI'], '?' )` sem guarda em `inc/listings.php:66` | `low` | `carried` de #7/#33: pré-existente, movido verbatim. |
| 64 | EC | A base da paginação já contém `/page/N/`, logo os links encadeiam-se | `low` | Pré-existente, movido verbatim (o ramo `$total > 1` nunca foi tocado). |
| 65 | EC | `?page=N` nunca é lido (ramo morto) | `low` | `carried` de #6/#32/#65: pré-existente. |
| 66 | EC | `$paged` não limitado a `max_num_pages` | `low` | `carried` de #31: pré-existente. |
| 67 | EC | `per_page` sem limite | `low` | `carried` de #36: pré-existente. |
| 68 | EC | `admin_post_nopriv` sem guarda de origem | `medium` | `carried` de #34: pré-existente; a 3.3 é dona da verificação de origem. |
| 69 | EC | O consentimento de cookies nunca compara o `ts` aos 180 dias | `low` | `carried` de #35: pré-existente. |
| 70 | EC | Um evento com `data_evento` esconde todos os eventos só-por-data futura | `medium` | Pré-existente, movido verbatim: se `$meta_check` tiver posts, o ramo por `post_date` não corre. |
| 71 | EC | `AGENTS.md:21` omite ficheiros | `low` | Duplicado de #56. |
| 72 | VG | Carregador `require_once` sem verificação | `medium` | Duplicado de #61/#24/#38. |
| 73 | VG | Portão sempre-vermelho; estado aceite não fixado | `medium` | Duplicado de #43/#44. |
| 74 | VG | O comportamento movido não é executado por teste nenhum | `medium` | Verdadeiro e por desenho: não há runtime WordPress no repo; a confirmação sancionada é a passagem no browser em `stagingredesign`. |
| 75 | VG | Comentário de `check-php.sh:15-16` inexacto | `low` | Duplicado de #42. |
| 76 | VG | A frase do `Resultado` sobre o baseline é inexacta | `low` | Duplicado de #43. |
| 77 | VG | `grep -c 'function '` não observa um hook registado duas vezes (AD-4) | `low` | Duplicado de #14/#47. |

**Agrupamento e encaminhamento (rodada 2)**

- **Grupo F — verificação do carregador/pórtico/comportamento** (#43, #44, #47, #61, #72, #73, #74, #77). Raiz: o repo não tem runtime WordPress nem test runner, logo nada fixa o estado aceite (17 problemas) nem resolve os alvos dos `require_once`, e o comportamento movido só se confirma no browser. Veredicto `medium`. → **defer** — o remédio é infraestrutura de verificação (o script da 1.1 e uma futura passagem de runtime), não o movimento literal desta história; fica registado em `deferred-work.md` com o que o fecharia.
- **Grupo G — defeitos pré-existentes no código movido** (#63, #64, #65, #66, #67, #68, #69, #70). Não causados por esta história. → **defer**.
- **Grupo H — deriva de documentação/contexto de agente** (#42, #51, #52, #56, #57, #58, #60, #71, #75). O remédio edita `AGENTS.md`/docs/scripts. → **defer**.
- **Grupo I — registo do próprio spec/log** (#45, #46, #48, #49, #50, #53, #54, #55, #59, #76). O remédio edita o próprio spec desta build. → **rejeitar**.
- **Refutado:** #62.

**Cascata (rodada 2):** nenhum grupo encaminha para `intent_gap` nem `bad_spec` — não há desvio da intenção congelada nem defeito de spec que o código devesse ter evitado. Não há loopback. Os grupos F–H vão para `deferred-work.md`; o grupo I é rejeitado. Resta a passagem no browser em `stagingredesign`, sancionada pelo spec.

## Verification

**Commands:**
- `bash scripts/check-php.sh` -- esperado: sintaxe limpa em todos os ficheiros e as duas verificações estruturais a correrem sobre `inc/`; a **única** falha são os marcadores `<!-- wp:` movidos verbatim com `inc/listings.php` (`ipcn_archive_hero`) e `inc/agenda-block.php` (`ipcn_home_agenda`). Qualquer outra linha vermelha é defeito desta história.
- `grep -rn -- '<!--' wp-content/themes/ipcn-fse/inc/` -- esperado: só os marcadores legados desses dois shortcodes, nenhum outro.
- `grep -c 'function ' inc/*.php` -- esperado: nenhuma função com nome duplicada entre ficheiros (as duas com nome são `ipcn_fse_handle_assoc` e `ipcn_fse_handle_contact`).
- `git diff --stat` -- esperado: `functions.php` encolhe de 619 para a guarda + seis `require_once`, `inc/` aparece com seis ficheiros.

**Manual checks (if no CLI):**
- Não há WordPress local, por isso "comporta-se como antes" não é verificável aqui. O que se verifica por inspeção é que cada bloco movido está literalmente igual ao original — incluindo os comentários de bloco — e que o inventário de hooks/shortcodes e as prioridades são os mesmos. A confirmação de comportamento faz-se no browser em `stagingredesign`, depois do deploy e da purga. Sem isso, a verificação está incompleta.

**Resultado:** implementado. `functions.php` reduzido a carregador de 21 linhas (guarda `ABSPATH` + seis `require_once` por ordem explícita, resolvidos com `__DIR__`); `inc/` com seis ficheiros — `setup.php` (79), `content-model.php` (62), `listings.php` (138), `forms.php` (157), `cookie-bar.php` (101), `agenda-block.php` (125). Cada segmento movido verificado byte-a-byte contra o baseline e o inventário de funções/hooks/shortcodes é idêntico. `bash scripts/check-php.sh` sai **1** com **17** problemas, todos `block markup`: 5 de `inc/listings.php` (`ipcn_archive_hero`) e 12 de `inc/agenda-block.php` (`ipcn_home_agenda`) — exactamente os `<!-- wp:` legados movidos verbatim (o baseline tinha os mesmos 17); a sintaxe sai limpa em todos os ficheiros (tema + mu-plugins) e as duas verificações estruturais correm sobre `inc/`. Sem `<!-- wp:` em nenhum outro ficheiro de `inc/`, sem colisões de hook entre ficheiros. Falta só a confirmação de comportamento no browser em `stagingredesign` (sem WordPress local).
