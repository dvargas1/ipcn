---
title: 'Story 1.2 — O comportamento sai de functions.php para inc/'
type: 'refactor'
created: '2026-09-27'
status: 'ready-for-dev'
route: 'dispatch'
review_loop_iteration: 0
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
- [ ] `inc/setup.php` — criar com o enqueue das fontes, o preconnect e o enqueue do `style.css` — é a primeira preocupação, sem dependências.
- [ ] `inc/content-model.php` — criar com o registo do CPT e da taxonomia — depois do setup, para o `init` ficar com uma ordem legível.
- [ ] `inc/listings.php` — criar com os dois shortcodes de listagem.
- [ ] `inc/forms.php` — criar com os dois handlers e os quatro shortcodes de formulário e mensagem.
- [ ] `inc/cookie-bar.php` — criar com a barra, o painel e o JavaScript.
- [ ] `inc/agenda-block.php` — criar com o shortcode da agenda, tal como está.
- [ ] `functions.php` — reduzir a carregador: guarda `ABSPATH`, e um `require` por ficheiro de `inc/`, por ordem explícita.
- [ ] `AGENTS.md` — actualizar o bullet que aponta `functions.php` como o sítio do CPT, dos formulários e dos cookies.

**Acceptance Criteria:**
- Given o tema carregado, when inspecciono `functions.php`, then não contém lógica de comportamento — só a guarda e os `require`.
- Given cada ficheiro de `inc/`, when o abro, then tem a guarda `ABSPATH` e trata de um único assunto.
- Given os quatro shortcodes e as duas acções `admin_post`, when os procuro, then existem com os mesmos nomes, nos ficheiros novos.
- Given o diff, when o leio, then cada função movida está idêntica — a mudança é de sítio, não de conteúdo.
- Given `bash scripts/check-php.sh`, when corro, then sai 0.

## Implementation Notes

## Spec Change Log

## Review Triage Log

## Verification

**Commands:**
- `bash scripts/check-php.sh` -- esperado: sai 0, e o `functions.php` deixa de ser o ficheiro grande.
- `grep -c 'function ' inc/*.php` -- esperado: nenhuma função duplicada entre ficheiros.
- `git diff --stat` -- esperado: `functions.php` encolhe, `inc/` aparece.

**Manual checks (if no CLI):**
- Não há WordPress local, por isso "comporta-se como antes" não é verificável aqui. O que se verifica por inspeção é que cada bloco movido está literalmente igual ao original; a confirmação de comportamento faz-se no browser em `stagingredesign`, depois do deploy e da purga. Sem isso, a verificação está incompleta — e é bom que se saiba.
