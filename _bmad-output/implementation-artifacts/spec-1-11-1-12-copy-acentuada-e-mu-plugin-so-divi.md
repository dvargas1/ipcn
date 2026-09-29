---
title: 'Stories 1.11 e 1.12 — A copy acentuada e o mu-plugin só-Divi'
type: 'feature'
created: '2026-09-29'
status: 'done'
baseline_revision: '170b31ee7d77929fa36456a6e0180c44049f615e'
review_loop_iteration: 0
followup_review_recommended: false
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
warnings:
  - multiple-goals
  - oversized
deferred:
  - summary: >-
      O vocabulário dos estados vazios não é um só («conteúdo», «posts», «publicações», «peças») e nenhum AC o fixa.
    evidence: >-
      Achado BH3 da revisão da 1.11/1.12. É pré-existente: o diff só tocou nos acentos destas frases e a 1.10 congelou a frase dos archives de tag, autor e data como «a frase de sempre». «posts» é um anglicismo em copy visível ao lado de «conteúdo», «publicações» e «peças» nas mesmas superfícies (`inc/listings.php:89`, `:100`, `:290`, `:293`, `:309`). Fecha-se numa decisão de copy que unifique os quatro nomes — superfície da 2.3 (a agenda) ou dos formulários na 3.x, ou de uma história de copy própria.
    location: >-
      wp-content/themes/ipcn-fse/inc/listings.php:89
    severity: low
  - summary: >-
      O `@font-face` do bloco `#ipcn-etmodules-fix` (só-Divi) continua sem `font-display`, e o texto pode bloquear à espera da fonte de ícones.
    evidence: >-
      Achado BH11 da revisão da 1.11/1.12. Pré-existente e fora do âmbito desta história: o AC da 1.12 manda manter os três blocos com o CSS verbatim (só o guard muda). O bloco existe para restaurar o hambúrguer e as setas do Divi, pelo que um FOIT é exactamente onde dói. Fecha-se com `font-display: swap` no `@font-face` do mu-plugin, numa alteração autorizada a esse ficheiro.
    location: >-
      wp-content/mu-plugins/ipcn-optimizations.php:72
    severity: low
  - summary: >-
      A guarda nova do mu-plugin não é executada por verificação durável nenhuma: nenhum comando do repositório carrega o ficheiro.
    evidence: >-
      Achado VG1 da revisão da 1.11/1.12, confirmado na fonte: o `scripts/check-php.sh` só corre `php -l` no mu-plugin (as duas verificações estruturais ficam sob `inc/`) e o `scripts/check-php.test.sh` não carrega o tema nem o mu-plugin. É a mesma infraestrutura sem dono já diferida pela 1.3 a 1.10 (o NFR9 declara «sem build step, testes ou CI»). Aqui o comportamento foi provado por um harness de sessão (stubs de `wp_get_theme()`/`add_action`/`esc_url`) que por construção não entra no repositório. Fecha-se com um harness durável em `scripts/`, no estilo do `check-php.test.sh`, que carregue o mu-plugin com stubs e afirme os dois ramos do guard.
    location: >-
      wp-content/mu-plugins/ipcn-optimizations.php:54-56
    severity: low
  - summary: >-
      A passagem renderizada que fecha os AC da 1.11 e da 1.12 fica pendente de deploy e purga em `stagingredesign`, que são acção do dono.
    evidence: >-
      Achado IA1 da revisão da 1.11/1.12. Os AC observam as superfícies renderizada e servida — o `h1` do Acervo, os vazios, as mensagens dos formulários, e o HTML do FSE sem as três etiquetas `ipcn-etmodules-fix`/`ipcn-form-style`/`ipcn-footer-logo-fix` e sem pedido a `/core/admin/fonts/ETmodules`. O repositório não as mede e não há WordPress local; a prova aqui é de código (strings e valor de retorno dos callbacks). A lista do que verificar está na secção `## Verification` desta spec.
    severity: low
---

<intent-contract>

## Intent

**Problem:** Duas superfícies fecham o Épico 1. (1) O texto de interface ainda não passou pela revisão de copy: quatro ficheiros do tema têm 13 strings em português sem acento ("Memoria e historia", "A agenda esta sendo montada", "Nenhum conteudo publicado nesta secao ainda.", "Conteudo IPCN", "Selecao de conteudo...", "Ainda nao ha posts nesta secao.", "Ainda nao ha pecas publicadas neste Tema.", "possivel", "nao conseguimos"), mais duas mensagens de sucesso com pontos de exclamação ("Mensagem enviada!", "Recebemos seu cadastro com sucesso!"), que a tabela de voz do `EXPERIENCE.md` proíbe. A 1.6 congelou a decisão de que a passagem global de acento é desta história. (2) O mu-plugin `ipcn-optimizations.php` serve as duas instalações e emite três blocos `<style>` só-Divi no `wp_head` (`#ipcn-etmodules-fix`, `#ipcn-form-style`, `#ipcn-footer-logo-fix`) também no site FSE, onde não há Divi: sobra CSS morto e um pedido a `/core/admin/fonts/ETmodules.*`, ficheiro que só existe no pacote do Divi.

**Approach:** (1) Passagem global pelo texto de interface do tema: acentuação correcta em português do Brasil, sem mudar o sentido nem a estrutura de nenhuma frase, e as duas mensagens de sucesso alinhadas pela tabela de voz (`EXPERIENCE.md` → *Voice and Tone*), que manda frases completas sem pontos de exclamação. (2) Guardar os três `wp_head` do mu-plugin com uma condição única — o tema activo é o Divi — sem tocar em mais nada do ficheiro.

## Boundaries & Constraints

**Always:** o diff é `templates/archive-acervo_ipcn.html`, `inc/agenda-block.php`, `inc/forms.php`, `inc/listings.php`, `theme.json`, `wp-content/mu-plugins/ipcn-optimizations.php` e esta spec; a copy nova é português do Brasil acentuado e mantém a forma da frase que substitui (a mesma voz, o mesmo significado, a mesma pontuação excepto onde a tabela de voz manda); a microcopy segue as colunas «Faça» de `EXPERIENCE.md:69-75`; o guard do mu-plugin é uma condição só sobre o tema activo, dentro dos três callbacks de `wp_head`, e os três blocos mantêm os ids e o CSS verbatim; a detecção do Divi é `wp_get_theme()->get_template()`; `bash scripts/check-php.sh` mantém os 17 problemas aceites e `bash scripts/check-php.test.sh` mantém 14/14; o ficheiro do mu-plugin continua a ser PHP válido (`php -l` limpo).

**Never:** acentuar comentários, docblocks ou prosa de `inc/`, `functions.php`, `style.css` ou dos `.html` que não seja texto renderizado — o contrato é o texto de interface, não a documentação do código; mexer nos slugs, cores, tamanhos ou `templateParts` do `theme.json` (só os dois `name` de fontes levam acento); mudar a ordem, a prioridade ou a assinatura dos `add_action`/`add_filter` do mu-plugin; tocar nos filtros que valem para todas as instalações (`the_generator`, `xmlrpc_enabled`, `wp_headers`/`X-Pingback`) nem nos dois filtros `et_builder_*` de cache; introduzir `function_exists`/`class_exists` para adivinhar o Divi; acrescentar ficheiros ao tema; deploy (acção do dono, e proibida no staging Divi); mexer em `sprint-status.yaml`, `README.md`, `STATUS.md` ou `ROADMAP.md`; tocar em `theme.json`, `functions.php`, `inc/setup.php`, `inc/cookie-bar.php`, `inc/content-model.php` ou nos patterns fora do que os `name` de fontes exigem; qualquer superfície banida.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Superfície do Acervo | `/acervo/` | o `h1` diz "Memória e história do IPCN" | — |
| Agenda sem encontros | bloco da agenda sem eventos futuros | "A agenda está sendo montada"; o parágrafo diz "próximos encontros" | — |
| Envio aceite (Fale conosco) | `?contato=ok` | "Recebemos sua mensagem. Vamos responder pelo e-mail informado." | — |
| Envio aceite (Associe-se) | `?cadastro=ok` | "Recebemos seu pedido. Vamos responder pelo e-mail informado." | — |
| Envio falhado | `?contato=erro` ou `?cadastro=erro` | "Ops, não conseguimos enviar sua mensagem/seu cadastro…" | — |
| Listagem vazia do shortcode | `[ipcn_query_posts]` sem posts, ou `ipcn/card` não registado | "Nenhum conteúdo publicado nesta seção ainda." | aviso `_doing_it_wrong` acentuado |
| Hero de arquivo sem termo | termo sem nome nem descrição | "Conteúdo IPCN" e "Seleção de conteúdo publicado pelo IPCN." | — |
| Secção sem publicações | `/category/<seccao>/` vazia | "Ainda não há posts nesta seção." | — |
| Tema sem peças | `/temas/<slug>/` vazia | "Ainda não há peças publicadas neste Tema." | — |
| Nomes de fontes no editor | `theme.json` | o seletor de fontes mostra "Título" e "Serif (memória)" | — |
| Tema activo Divi | qualquer página do site Divi | os três `<style>` no `wp_head`, e o `@font-face` pede `ETmodules.woff` etc. | — |
| Tema activo FSE | qualquer página de `ipcn-fse` | nenhum dos três `<style>` no HTML, nenhum pedido a `/core/admin/fonts/` | — |
| Outro tema | qualquer | idem FSE: os três blocos não saem | — |
| Filtros globais | qualquer tema | `generator` vazio, `xmlrpc_enabled` falso e `X-Pingback` removido continuam a valer | — |

</intent-contract>

## Code Map

- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — o arquivo do Acervo. `:12` o `h1` do hero: `Memoria e historia do IPCN` → `Memória e história do IPCN` (as duas palavras; `IPCN` fica). O resto da página já está acentuado e o vazio de `:33` já foi fechado pela 1.10.
- `wp-content/themes/ipcn-fse/inc/agenda-block.php` — o bloco `ipcn/agenda` e o seu estado vazio. `:78` o `h3` do vazio (`A agenda esta sendo montada` → `A agenda está sendo montada`) e `:81` o parágrafo (`proximos` → `próximos`). A tabela de voz (`EXPERIENCE.md:69`) pede textualmente «A agenda está sendo montada»; a história 2.3 reconstrói este bloco e assume a frase, pelo que aqui só se acentua — a estrutura do vazio não muda. As 12 ocorrências `block markup` aceites do `check-php.sh` vivem neste ficheiro: não acrescentar nem tirar comentários de bloco.
- `wp-content/themes/ipcn-fse/inc/forms.php` — handlers `admin_post` e os shortcodes das mensagens. `:133` sucesso do Fale conosco e `:150` sucesso do Associe-se (as duas com `!`), `:136` e `:153` os erros (`nao`). A coluna «Evite» de `EXPERIENCE.md:70-71` rejeita «Cadastro realizado com sucesso! Bem-vindo(a)!» e a coluna «Faça» dá a frase exacta do pedido de associação; a `UJ-3` (`EXPERIENCE.md:180`) diz que «a resposta vem por e-mail», o que confirma a frase da tabela para o `:150`. As etiquetas dos campos (`Nome completo *`, `E-mail *`, `Telefone`, `Mensagem *`) e os botões já estão correctos — não mexer.
- `wp-content/themes/ipcn-fse/inc/listings.php` — `:89` e `:100` o vazio de `[ipcn_query_posts]` (a mesma frase, nos dois ramos), `:99` o aviso de `_doing_it_wrong` do pattern ausente, `:171` e `:173` os fallbacks do hero de arquivo, `:290` o ramo não-categoria de `[ipcn_archive_vazio]` (arquivos de tag, autor e data — a 1.10 decidiu mantê-lo verbatim) e `:309` o Tema sem peças com a ligação ao Acervo. Só as strings mudam: as guardas, as ligações por slug (AD-7) e os cinco problemas `block markup` aceites deste ficheiro ficam como estão (o bloco de markup do hero em `:177-191` não se toca).
- `wp-content/themes/ipcn-fse/theme.json` — `:19` e `:20`, os `name` das famílias `oswald` e `serif`, visíveis no seletor de fontes do editor. Os `slug` e os `fontFamily` não mudam (mudar um `slug` partiria os `var:preset|font-family|…` dos templates).
- `wp-content/mu-plugins/ipcn-optimizations.php` — serve as duas instalações. `:54-57` `#ipcn-etmodules-fix` (o `@font-face` que aponta a `get_template_directory_uri() . '/core/admin/fonts/ETmodules'`), `:63-169` `#ipcn-form-style` e `:176-206` `#ipcn-footer-logo-fix`. Os filtros que valem para todas as instalações estão em `:20-43` (`et_builder_*`, `the_generator`, `xmlrpc_enabled`, `wp_headers`) — não se tocam. O guard entra dentro dos três callbacks de `wp_head`, onde o tema activo já está resolvido, e não no carregamento do ficheiro (mu-plugins carregam a meio do `wp-settings.php`).
- `scripts/check-php.sh` — corre `php -l` sobre o tema e os mu-plugins e as verificações estruturais só sob `inc/`; o mu-plugin não é `inc/`, logo o `<style>` que ele emite nunca foi problema. Estado aceite: 17 problemas (12 em `agenda-block.php`, 5 em `listings.php`), saída 1.
- `_bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/EXPERIENCE.md` — `:65` «Português do Brasil na interface», `:69-75` a tabela de voz, `:180` a `UJ-3`. É a autoridade da copy e não se edita.

## Tasks & Acceptance

**Execution:**
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — `Memoria e historia do IPCN` → `Memória e história do IPCN` — o `h1` do arquivo do Acervo.
- `wp-content/themes/ipcn-fse/inc/agenda-block.php` — `A agenda esta sendo montada` → `A agenda está sendo montada` e `proximos` → `próximos` — a copy do vazio, na forma que a história 2.3 assume.
- `wp-content/themes/ipcn-fse/inc/forms.php` — `Mensagem enviada! Vamos responder no e-mail informado o mais breve possivel.` → `Recebemos sua mensagem. Vamos responder pelo e-mail informado.`; `Recebemos seu cadastro com sucesso! Nossa equipe vai entrar em contato em breve no e-mail ou telefone informado.` → `Recebemos seu pedido. Vamos responder pelo e-mail informado.`; e `nao conseguimos` → `não conseguimos` nas duas mensagens de erro — alinhar as duas confirmações pela tabela de voz e acentuar as duas falhas.
- `wp-content/themes/ipcn-fse/inc/listings.php` — `Nenhum conteudo publicado nesta secao ainda.` → `Nenhum conteúdo publicado nesta seção ainda.` (nos dois ramos, `:89` e `:100`); `Conteudo IPCN` → `Conteúdo IPCN`; `Selecao de conteudo publicado pelo IPCN.` → `Seleção de conteúdo publicado pelo IPCN.`; `O pattern ipcn/card nao esta registado; o cartao nao pode ser renderizado.` → `O pattern ipcn/card não está registado; o cartão não pode ser renderizado.`; `Ainda nao ha posts nesta secao.` → `Ainda não há posts nesta seção.`; `Ainda nao ha pecas publicadas neste Tema.` → `Ainda não há peças publicadas neste Tema.`.
- `wp-content/themes/ipcn-fse/theme.json` — `"Titulo"` → `"Título"` e `"Serif (memoria)"` → `"Serif (memória)"` — os rótulos das fontes no editor, sem tocar em `slug` nem em `fontFamily`.
- `wp-content/mu-plugins/ipcn-optimizations.php` — acrescentar `ipcn_is_divi_active()`, que devolve `'Divi' === wp_get_theme()->get_template()`, e um `if ( ! ipcn_is_divi_active() ) { return; }` como primeira instrução de cada um dos três callbacks de `wp_head` — os três blocos só-Divi deixam de sair em qualquer instalação que não corra o Divi, e nada mais no ficheiro muda.
- Harness de sessão (na scratchpad, fora do deliverable) — provar por execução as duas metades: as strings novas presentes e as velhas ausentes no tema, e os três callbacks invocados com `wp_get_theme()` stubbado (Divi → três blocos; `ipcn-fse` → nenhum).

**Acceptance Criteria:**
- Given as superfícies do tema, when o texto de interface é inspeccionado, then não há nenhuma palavra em português sem o acento correcto e nenhuma frase em português europeu onde a forma brasileira está fixada.
- Given `/acervo/`, `/temas/<slug>/` vazio, uma Secção vazia e as mensagens de formulário, when cada uma é renderizada, then as frases são as da matriz de I/O, com a acentuação correcta.
- Given a tabela de voz do `EXPERIENCE.md`, when a microcopy das confirmações e dos vazios é lida, then não há pontos de exclamação nem entusiasmo de marketing.
- Given o site FSE com o mu-plugin activo, when o `wp_head` corre, then o HTML servido não contém `ipcn-etmodules-fix`, `ipcn-form-style` nem `ipcn-footer-logo-fix`, e nenhum pedido a `/core/admin/fonts/ETmodules` sai.
- Given o site Divi com o mu-plugin activo, when o `wp_head` corre, then os três blocos saem com os ids e o CSS de hoje.
- Given qualquer tema, when o mu-plugin carrega, then `generator` continua vazio, o XML-RPC desligado e o `X-Pingback` removido.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa (incluindo o mu-plugin) e os 17 problemas aceites não crescem.
- Given `bash scripts/check-php.test.sh`, when corro, then 14/14.

## Spec Change Log

Sem alterações ao contrato.

## Review Triage Log

### 2026-09-29 — Review pass

- verdicts: 17 findings — high 0, medium 0, low 8, false 9, maybe-false 0
- findings:
  - `[low]` `[patch]` **BH1** o aviso de `_doing_it_wrong` dizia «não está registado» (particípio europeu, herdado do texto antigo) — corrigido para «registrado» (pt-BR) em `inc/listings.php:99`; o resto da frase não mudou.
  - `[low]` `[patch]` **BH2** o botão do Associe-se dizia «Enviar cadastro» e o erro «enviar seu cadastro», enquanto a confirmação nova dizia «Recebemos seu pedido» — corrigido para uma só palavra, «pedido», em `inc/forms.php:102` e `:153`; é a que o `EXPERIENCE.md:40` (IA, «pedido de associação») e a `UJ-3` (`:180`) usam, e a confirmação mantém a frase verbatim da tabela de voz.
  - `[low]` `[defer]` **BH3** o vocabulário dos vazios não é um só («conteúdo», «posts», «publicações», «peças») — pré-existente: o diff só lhes tocou nos acentos, e a 1.10 congelou a frase dos archives de tag, autor e data como «a frase de sempre». Unificar quatro nomes é uma decisão de copy que nenhum AC nomeia; fica no `deferred`.
  - `[false]` `[reject]` **BH4** o vazio da agenda («A agenda está sendo montada») não refere encontros passados nem dá caminho para eles — é exactamente o tratamento mandado pelo par de UX (`EXPERIENCE.md:113`, «Nunca lista encontros passados no lugar dos próximos») e pela história 2.3, que reconstrói o bloco; a mudança só acentuou a frase. Sem defeito no sítio citado.
  - `[false]` `[reject]` **BH5** «os dois vazios do Acervo dizem coisas diferentes» — são superfícies distintas com copy distinta, mandada pelo par de UX: `/acervo/` diz «Em breve, novos itens do acervo.» (`:111`), o Tema sem peças explica e oferece o Acervo inteiro (`:112`). A divergência é o desenho, não um defeito.
  - `[false]` `[reject]` **BH6 + EC1** a comparação literal `'Divi' === get_template()` falharia em silêncio se o directório do tema Divi tivesse outro nome, e o docblock não o declara — o estado (uma instalação Divi com o directório renomeado) nunca foi demonstrado alcançável, `Divi` é o nome do pacote, o pressuposto e a direcção da falha estão registados nas Design Notes desta spec, e o remédio proposto (`apply_filters`/`class_exists`) acrescenta superfície e adivinhação. Rejeitado.
  - `[false]` `[reject]` **BH7** o docblock da guarda não explica porque os dois filtros `et_builder_*` ficam sem guarda — o texto diz «os três blocos abaixo» e não afirma nada sobre os filtros; os filtros são inertes fora do Divi e o AC manda que nada mais mude. Não há texto falso nem dano nomeado.
  - `[false]` `[reject]` **BH8** o aviso `_doing_it_wrong` foi acentuado e os comentários ao lado não — a fronteira é texto emitido (o aviso é uma mensagem que o tema produz, sob `WP_DEBUG`) contra prosa de código (nunca emitida), que é a linha que a spec fixa. Não há regra inconsistente no sítio citado.
  - `[false]` `[reject]` **BH9** os rótulos de fonte do `theme.json` não nomeiam o tipo de letra — já não o faziam («Titulo», «Serif (memoria)»); o diff só corrigiu o acento. Passá-los a nomes de famílias é outra decisão, que nenhum AC pede.
  - `[false]` `[reject]` **BH10** as duas confirmações perderam a indicação de prazo («em breve», «o mais breve possível») — a frase é a da tabela de voz e cumpre «Confirmação visível, com o que acontece a seguir» (`EXPERIENCE.md:118`) ao dizer «Vamos responder pelo e-mail informado.»; o prazo vago era o que a coluna «Evite» desaconselha.
  - `[low]` `[defer]` **BH11** o `@font-face` do bloco só-Divi continua sem `font-display` — pré-existente e fora do âmbito: o AC manda manter os três blocos com o CSS verbatim. Melhoria real, mas de outra história; no `deferred`.
  - `[false]` `[reject]` **BH12** o registo das mensagens de erro é desigual («Ops», «fale com a gente» ao lado de «Confira os campos») — sem dano nomeado: são frases completas, sem pontos de exclamação nem entusiasmo de marketing, que é o que a tabela de voz proíbe. A frase-modelo da coluna «Faça» para a falha promete retenção dos dados («Seus dados continuam aqui») que só a 3.2 entrega, pelo que aplicá-la agora seria confirmar o que não acontece.
  - `[false]` `[reject]` **EC1** — ver BH6 (mesma causa, mesmo veredicto).
  - `[low]` `[reject]` **EC2** a matriz de I/O desta spec põe «Ainda não há posts nesta seção.» na linha «Secção sem publicações», mas a categoria vazia devolve «Ainda não há publicações nesta Seção.» e a frase acentuada vive no ramo não-categoria (`inc/listings.php:290`). O código é o que a 1.10 deixou (só lhe mudou o acento) e o remédio seria prosa da matriz — que está dentro do `<intent-contract>`, imutável nesta passagem. Registado nos riscos residuais do `## Auto Run Result`.
  - `[low]` `[patch]` **EC3** o docblock e o comentário de `[ipcn_archive_vazio]` diziam que os archives de tag, autor e data guardavam a frase «verbatim», e a passagem de acento tornou a palavra falsa — corrigido para «a frase de sempre (acentuada na 1.11)» em `inc/listings.php:274` e `:288`.
  - `[low]` `[defer]` **VG1** o ramo novo da guarda não é executado por verificação durável nenhuma: o `check-php.sh` só lhe corre `php -l` e o `check-php.test.sh` não carrega o mu-plugin — é a mesma infraestrutura sem dono já diferida pela 1.3 a 1.10 (`AGENTS.md:26`, «Não há test runner nem CI»). Aqui provado por harness de sessão, fora do deliverable; no `deferred`.
  - `[low]` `[defer]` **IA1** as expectativas dos AC vivem na superfície renderizada e servida (HTML do FSE sem as três etiquetas, sem pedido ao ficheiro de fonte, deploy no `stagingredesign`) e o diff com os seus testes vive na superfície do código (strings e valor de retorno dos callbacks) — a passagem renderizada e o deploy são acção do dono, como nas histórias 1.5 a 1.10; no `deferred`.

  **Encaminhamento.** **patch** — A (BH1, EC3: a palavra e os dois comentários de `inc/listings.php`), B (BH2: a palavra «pedido» em `inc/forms.php`). **defer** — C (BH3: o vocabulário dos vazios), D (BH11: o `font-display` do bloco Divi), E (VG1: a verificação durável da guarda) e F (IA1: a passagem renderizada e o deploy). **reject** — 9 `false` (BH4, BH5, BH6+EC1, BH7, BH8, BH9, BH10, BH12) e um `low` cujo remédio é prosa da matriz imutável (EC2). Sem `intent_gap` nem `bad_spec`: sem loopback.

## Design Notes

**Porquê um só spec para as duas histórias.** As duas são independentes e o `multiple-goals` fica registado, mas o passo de planeamento trata um só `spec_file` por invocação e o pedido nomeou as duas juntas. A copy (1.11) e o guard (1.12) não partilham um único ficheiro, pelo que a revisão consegue separá-las.

**Porquê os `name` de fontes entram e os comentários não.** A tabela de voz do par de UX diz «Português do Brasil na interface» e o AC da 1.11 nomeia «os rótulos». Os `name` do `theme.json` são rótulos que uma pessoa lê no seletor de fontes do editor; os docblocks de `inc/` e os comentários de bloco dos `.html` não são lidos por ninguém fora do código. Se os comentários entrassem, o diff deixava de ser verificável por uma asserção simples e passava a ser uma reescrita de documentação — o que nenhum AC pede.

**Porquê as duas confirmações foram reescritas e não só acentuadas.** A coluna «Evite» de `EXPERIENCE.md:70` rejeita literalmente «Cadastro realizado com sucesso! Bem-vindo(a)!» e a linha final da tabela (`:75`) proíbe pontos de exclamação; a coluna «Faça» (`:70`) dá a frase de substituição. As duas mensagens de erro não levam «!» e ficam com a redacção de hoje, só acentuadas — reescrevê-las seria a superfície das histórias 3.2 e 3.3.

**Porquê o guard é sobre o tema e não sobre o Divi Builder.** O AC da 1.12 diz «apenas quando o tema activo é o Divi», e o mu-plugin é a peça que serve as duas instalações (o site Divi e o FSE) — não o plugin do builder. `get_template()` (e não `get_stylesheet()`) porque um tema-filho do Divi continua a precisar destes estilos; é o nome do directório do tema-pai, `Divi`.

**Porquê o guard fica dentro dos callbacks.** Os mu-plugins carregam a meio do `wp-settings.php`; deixar a decisão para o momento em que o `wp_head` corre evita depender da ordem de carregamento e mantém as três chamadas `add_action` com a assinatura e a prioridade de hoje.

**Risco assumido, registado.** Se a instalação Divi tiver o directório do tema com outro nome, o guard devolve falso e o Divi perde os três blocos em silêncio. O AC prevê que o ficheiro não seja deployado no staging Divi, pelo que o risco é de uma instalação futura; fica nos riscos residuais.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: 17 problemas (12 `block markup` em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1, nenhuma entrada `sintaxe:`.
- `bash scripts/check-php.test.sh` — esperado: 14/14.
- `php -l wp-content/mu-plugins/ipcn-optimizations.php` — esperado: `No syntax errors detected`.
- `grep -rn 'Memoria e historia\|esta sendo montada\|proximos encontros\|nao conseguimos\|possivel\.\|Nenhum conteudo\|Conteudo IPCN\|Selecao de conteudo\|nao esta registado\|Ainda nao ha\|"Titulo"\|Serif (memoria)' wp-content/themes/ipcn-fse/` — esperado: vazio.
- `grep -c 'Mensagem enviada!\|com sucesso!' wp-content/themes/ipcn-fse/inc/forms.php` — esperado: 0 (o corpo do e-mail interno em `:76` diz «Mensagem enviada pelo Fale Conosco» sem ponto de exclamação e não é copy de interface).
- `grep -n 'ipcn_is_divi_active' wp-content/mu-plugins/ipcn-optimizations.php` — esperado: 4 (uma definição, três guardas).
- `git diff --stat` — esperado: só os cinco ficheiros do tema, o mu-plugin e esta spec.
- Harness de sessão na scratchpad: as 13 strings novas no tema com as antigas ausentes; `ipcn_is_divi_active()` com `wp_get_theme()` a devolver `Divi` e a devolver `ipcn-fse`; e a captura dos três callbacks de `wp_head` a provar que, sem Divi, nenhum escreve `<style>`.

**Manual checks (if no CLI):**
- Sem WordPress local, a prova renderizada fica para o dono: em `stagingredesign`, com o tema FSE, ver o HTML servido sem os três ids e a consola de rede sem pedido a `/core/admin/fonts/ETmodules`; no staging Divi, com o ficheiro **antigo**, confirmar que o comportamento de lá não muda. O deploy é acção do dono, como nas histórias 1.5 a 1.10.

## Auto Run Result

**O que mudou.** As duas últimas superfícies do Épico 1 fecham. (1) **1.11 — a copy.** Passagem de acento pelo texto de interface do tema, em português do Brasil: o `h1` do Acervo («Memória e história do IPCN»), o vazio da agenda («A agenda está sendo montada», «próximos encontros»), as seis strings de `inc/listings.php` (o vazio de `[ipcn_query_posts]` nos dois ramos, o aviso de `_doing_it_wrong`, os dois fallbacks do hero de arquivo, o ramo não-categoria de `[ipcn_archive_vazio]` e o Tema sem peças), as duas falhas dos formulários («não conseguimos») e os dois rótulos de fonte do `theme.json`. As duas confirmações foram alinhadas pela tabela de voz do `EXPERIENCE.md` (`:70`): «Recebemos sua mensagem. Vamos responder pelo e-mail informado.» e «Recebemos seu pedido. Vamos responder pelo e-mail informado.» — sem pontos de exclamação, que a linha final da tabela (`:75`) proíbe. (2) **1.12 — o mu-plugin.** `ipcn_is_divi_active()` (`'Divi' === wp_get_theme()->get_template()`) e um `return` antecipado como primeira instrução de cada um dos três callbacks de `wp_head`, pelo que `#ipcn-etmodules-fix`, `#ipcn-form-style` e `#ipcn-footer-logo-fix` só saem com o tema Divi activo.

**Ficheiros alterados** (`git diff --stat` desde a base `170b31ee7d77929fa36456a6e0180c44049f615e`):
- `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — o `h1` do hero, acentuado.
- `wp-content/themes/ipcn-fse/inc/agenda-block.php` — a copy do vazio da agenda, acentuada.
- `wp-content/themes/ipcn-fse/inc/forms.php` — as duas confirmações alinhadas pela tabela de voz; as duas falhas acentuadas e o pedido alinhado com a confirmação («Enviar pedido» e «enviar seu pedido», em vez de «cadastro»).
- `wp-content/themes/ipcn-fse/inc/listings.php` — seis strings acentuadas e o aviso de `_doing_it_wrong`; os dois comentários que diziam «verbatim» passam a «a frase de sempre (acentuada na 1.11)».
- `wp-content/themes/ipcn-fse/theme.json` — os dois `name` de fontes («Título», «Serif (memória)»); `slug` e `fontFamily` intactos.
- `wp-content/mu-plugins/ipcn-optimizations.php` — `ipcn_is_divi_active()` e as três guardas; os três blocos, os ids, o CSS e os filtros globais ficam verbatim.
- `_bmad-output/implementation-artifacts/spec-1-11-1-12-copy-acentuada-e-mu-plugin-so-divi.md` — esta spec.

**Triagem da revisão.** 17 achados de quatro camadas — `high` 0, `medium` 0, `low` 8, `false` 9 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **3 achados corrigidos por patch em 2 correcções:** a palavra «registrado» e os dois comentários que a passagem de acento tornou falsos (`inc/listings.php`, BH1 e EC3); e a palavra «pedido» no botão e na falha do Associe-se, alinhada com a confirmação e com a IA e a `UJ-3` do par de UX (`inc/forms.php`, BH2). **4 achados diferidos:** o vocabulário dos vazios (BH3), o `font-display` do bloco Divi (BH11), a verificação durável da guarda (VG1) e a passagem renderizada com o deploy (IA1). **10 rejeitados:** 9 `false` — o vazio da agenda (BH4: é o tratamento mandado por `EXPERIENCE.md:113`), os dois vazios do Acervo (BH5: são duas superfícies com copy distinta mandada pelo par), a robustez da comparação literal e o docblock da guarda (BH6+EC1: o estado nunca foi demonstrado alcançável e o remédio acrescenta adivinhação), os filtros `et_builder_*` sem guarda (BH7), a fronteira entre texto emitido e prosa de código (BH8), os rótulos de fonte (BH9: já não nomeavam o tipo de letra), o prazo das confirmações (BH10: a frase-modelo da tabela não tem prazo), e o registo das falhas (BH12: sem pontos de exclamação nem entusiasmo) — e 1 `low` cujo remédio seria prosa da matriz de I/O, dentro do `<intent-contract>` imutável (EC2).

**Recomendação de passagem seguinte:** `followup_review_recommended: false` — nesta primeira passagem nenhum patch foi `high` e nenhum foi `medium` (3 `low`, em 2 correcções). Contagem por veredicto: `low` 3, `medium` 0, `high` 0.

**Verificação.** `bash scripts/check-php.sh` → 17 problemas, todos `block markup` (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1 e 0 entradas `sintaxe:` — idêntico ao baseline; `bash scripts/check-php.test.sh` → 14/14; `php -l` limpo no mu-plugin e nos dois ficheiros PHP do tema alterados; o grep das 12 strings antigas (`Memoria e historia`, `esta sendo montada`, `proximos encontros`, `nao conseguimos`, `possivel.`, `Nenhum conteudo`, `Conteudo IPCN`, `Selecao de conteudo`, `nao esta registado`, `Ainda nao ha`, `"Titulo"`, `Serif (memoria)`) → vazio; `grep -c 'Mensagem enviada!\|com sucesso!'` → 0; `grep -c ipcn_is_divi_active` → 4. Harness de sessão na scratchpad (fora do deliverable, como os das 1.5 a 1.10): a metade da copy em 39/39 (as 14 strings novas presentes e as 12 antigas ausentes) e a metade da guarda em 26/26 — prioridades 1/2/3 intactas, `Divi` → os três ids mais o pedido a `ETmodules.{eot,woff,ttf,svg}`, `ipcn-fse` e `twentytwentyfour` → 0 bytes, e os filtros `the_generator`/`xmlrpc_enabled`/`wp_headers` a continuar a fazer o que faziam. Sem WordPress local, a passagem no browser fica pendente de deploy e purga.

**Riscos residuais.** (1) A passagem renderizada continua por fazer: o repositório não mede o HTML servido nem a rede. A verificar em `stagingredesign` depois da purga (`?nocache=1`): o `h1` do Acervo, os vazios de `/acervo/`, de uma Secção e de um Tema, as mensagens dos dois formulários, e o `<head>` sem os três ids e sem pedido a `/core/admin/fonts/ETmodules`. (2) A guarda depende de o directório do tema Divi se chamar `Divi`; o ficheiro não é deployado no staging Divi, pelo que o risco é de uma instalação futura com o directório renomeado — o Divi perderia os três blocos em silêncio. (3) A linha «Secção sem publicações» da matriz de I/O nomeia «Ainda não há posts nesta seção.», mas essa é a frase do ramo não-categoria (archives de tag, autor e data); a categoria vazia devolve «Ainda não há publicações nesta Seção.» e a ligação ao hub — comportamento que vem da 1.10 e que esta história só acentuou. O código está correcto; a linha da matriz (dentro do `<intent-contract>`, imutável) é que está imprecisa. (4) O mu-plugin **não** foi deployado em instalação nenhuma: o deploy no `stagingredesign` e a confirmação de que o staging Divi fica como está são acção do dono.
