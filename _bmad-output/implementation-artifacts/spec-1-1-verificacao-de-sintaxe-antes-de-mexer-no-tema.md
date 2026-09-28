---
title: 'Story 1.1 — Verificação antes de mexer no tema'
type: 'chore'
created: '2026-09-26'
status: 'done'
route: 'dispatch'
review_loop_iteration: 0
baseline_commit: 'f7258eb95520c9159c98c5283c185063556d5eb4'
context: []
---

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Não há rede de segurança nenhuma antes do deploy. O tema vive em `stagingredesign`, o deploy é manual por tar e scp, e um erro só se descobre ao abrir o site no browser — depois de já ter substituído o tema em revisão. O `php -l` já foi usado à mão antes, mas nada o torna repetível. E o `php -l` sozinho não teria apanhado o defeito que encontrámos hoje: o shortcode da agenda a imprimir block markup como texto, que fez os cartões renderizarem vazios.

**Approach:** Um script no repositório que verifica o PHP do tema e dos mu-plugins e falha quando encontra problemas. Três verificações: sintaxe por `php -l` em todo o PHP; block markup dentro de um literal PHP sob `inc/`; e `<style>` emitido de PHP sob `inc/`. Sem build step, sem dependências, sem CI.

As duas verificações estruturais limitam-se a `inc/` de propósito: é onde o código novo passa a viver a partir da história 1.2, e o `functions.php` actual contém `<!-- wp:` nas suas strings — o defeito por corrigir — pelo que as estender ao tema inteiro deixaria o script permanentemente vermelho num checkout limpo. As duas passam a valer para tudo o que nasça em `inc/`.

## Boundaries & Constraints

**Always:** O script não altera ficheiros e não faz deploy. Sai com código diferente de zero quando encontra um problema, e diz que ficheiros verificou, para não dar uma sensação falsa de cobertura. As verificações estruturais só olham para ficheiros `.php` sob `inc/`; os templates `.html` contêm block markup legitimamente e não são alvo.

**Never:** Não introduzir build step, gestor de pacotes, linter de terceiros nem framework. Não acrescentar dependências ao tema. Não tocar no `functions.php`, no `mu-plugin` nem no CSS nesta história — a 1.2 e a 1.12 tratam disso. Não policiar os defeitos já conhecidos do `functions.php`.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Sem argumentos | — | Verifica todo o PHP do tema e dos mu-plugins. Sai 0 quando todos compilam. | Sem PHP encontrado: diz isso e sai 0. |
| Com caminhos | Um ou mais caminhos | Verifica só esses ficheiros. | Caminho inexistente: nomeia-o e sai diferente de zero. |
| Erro de sintaxe | Um ficheiro que não compila | Nome do ficheiro e a linha, e sai diferente de zero. | Continua a verificar os restantes antes de sair. |
| Block markup em código | Um `.php` sob `inc/` com `<!-- wp:` numa string | Aponta o ficheiro e a linha, e sai diferente de zero. | — |
| `<style>` de PHP | Um `.php` sob `inc/` com `<style id=` ou `echo "<style` | Aponta o ficheiro e a linha, e sai diferente de zero. | — |
| `php` ausente | — | Diz que falta o `php`, indica como instalá-lo, e sai diferente de zero. | — |

</frozen-after-approval>

## Code Map

- `wp-content/themes/ipcn-fse/functions.php` — 619 linhas, hoje o único PHP do tema. Contém as ocorrências de `<!-- wp:` que motivam a verificação, e é o ficheiro que a história 1.2 reparte. **Não é policiado** pelas verificações estruturais nesta história.
- `wp-content/mu-plugins/ipcn-optimizations.php` — 206 linhas, congelado, tocado pela história 1.12. Verificado quanto a sintaxe.
- `wp-content/mu-plugins/ipcn-mail-from.php` — 19 linhas. Verificado quanto a sintaxe.
- `wp-content/themes/ipcn-fse/templates/*.html` — contêm `<!-- wp:` legitimamente. Não são PHP e não entram em nenhuma verificação.
- `AGENTS.md` — o bloco gerido diz hoje, em *Running and verifying*: "Não há test runner, lint nem CI. Verificar com `php -l` em PHP alterado e no browser (`?nocache=1`)". É onde um agente procura o comando, e passa a nomear o script.
- `docs/redesign-gate4-paridade-2026-09-10.md` — regista um `php -l` manual feito outrora sobre o `functions.php`, com resultado ok.
- Não existe `scripts/`, `bin/`, `tools/` nem `Makefile`. A pasta é nova.
- **Comportamento verificado do `php -l` nesta máquina (PHP 8.5.4):** num ficheiro válido escreve `No syntax errors detected in <ficheiro>` no stdout e sai **0**. Num ficheiro inválido escreve `PHP Parse error: ... on line <N>` no **stderr** e `Errors parsing <ficheiro>` no stdout, e sai **255** — não 1. Um script que teste `= 1` ou que leia só o stdout não funciona.

## Tasks & Acceptance

**Execution:**
- [x] `scripts/check-php.sh` — criar o script, executável, com um cabeçalho a explicar a invocação — é o único entregável novo.
- [x] `AGENTS.md` — acrescentar a invocação ao bullet de verificação, dentro do bloco gerido — para um agente o encontrar sem adivinhar.
- [x] `scripts/check-php.test.sh` — testes do próprio script, em bash puro — acrescentado na revisão: sem eles, uma verificação que deixasse de verificar passaria despercebida, que é precisamente o defeito que o script existe para apanhar.

**Acceptance Criteria:**
- Given o PHP do tema e dos mu-plugins, when corro o script sem argumentos, then verifica os três e sai 0.
- Given um ficheiro PHP com erro de sintaxe, when corro o script, then nomeia o ficheiro e a linha e sai diferente de zero.
- Given um `.php` sob `inc/` com `<!-- wp:` numa string, when corro o script, then aponta o ficheiro e a linha e sai diferente de zero.
- Given um `.php` sob `inc/` com `<style id=` ou `echo "<style`, when corro o script, then aponta o ficheiro e a linha e sai diferente de zero.
- Given caminhos explícitos, when corro o script, then verifica só esses ficheiros.
- Given um caminho inexistente, when corro o script, then nomeia-o e sai diferente de zero.
- Given o script, when o inspecciono, then não altera ficheiros e não faz deploy.

## Implementation Notes

- Entregue `scripts/check-php.sh` (executável, `#!/usr/bin/env bash`, cabeçalho com a invocação e os códigos de saída). Sem argumentos descobre os `.php` em `wp-content/themes/ipcn-fse/` e `wp-content/mu-plugins/`; com argumentos verifica só esses caminhos, ficheiro ou pasta. Deduplica e ordena.
- As verificações estruturais correm apenas em `.php` sob `inc/`, medido **a partir da raiz do repositório**.
- `php -l` devolve **255** (não 1) num ficheiro inválido e escreve o parse error no **stderr**. O script testa `!= 0`, lê o stderr e extrai a linha de `... on line N`.
- Códigos de saída: `0` ok (ou nenhum PHP encontrado); `1` problemas (tem precedência); `2` ambiente (ferramenta ou `php` ausente, raiz por omissão ausente, caminho inexistente).
- Lista sempre os ficheiros verificados e diz quando as estruturais não correram. Só lê: não escreve nada e não faz deploy.

### Correcções da revisão

- `is_under_inc` mede a localização relativa à raiz em vez do caminho absoluto — `*/inc/*` sobre um caminho absoluto apanhava qualquer ancestral chamado `inc`, reproduzindo o checkout permanentemente vermelho que a história existe para evitar.
- Guard do `<style>` alargado de duas formas literais para `<style` em qualquer sítio: `echo '<style>'`, `printf("<style>")` e heredocs passavam verdes.
- Guard do markup passou a apanhar `<!--wp:` sem espaço, e a ignorar linhas iniciadas por comentário — documentar a regra num docblock não é violá-la.
- Raiz por omissão ausente deixou de sair 0: era indistinguível de "verificado e limpo".
- A linha final deixou de dizer "OK" quando há caminhos ausentes — o resumo contradizia o código de saída.
- Acrescentados: guarda de bash >= 4, guarda de presença de `find`/`grep`/`sed`/`sort`/`head`, `--help`, separador `--`, `-print0` na descoberta, e argumentos que não são PHP deixam de ser verificados e listados.
- **Rejeitados com prova:** `-d display_errors=stderr` (a mensagem já chega ao stderr mesmo com `display_errors=Off`, e a flag **duplica** a linha) e `LC_ALL=C` (o PHP não localiza mensagens de parse).
- `scripts/check-php.test.sh` — 14 casos, bash puro, sem framework.

## Spec Change Log

## Review Triage Log

Camadas: **BH** = revisor cego de conteúdo; **EC** = caça a casos-limite; **VG** = lacunas de verificação.

| # | Camada | Achado | Veredicto | Evidência |
|---|--------|--------|-----------|-----------|
| 1 | BH, EC | `is_under_inc` testa o caminho absoluto; `*/inc/*` apanha qualquer ancestral `inc` | `medium` — corrigido | Clone em `.../inc/fakerepo/` ficava vermelho no `functions.php`. Medido relativo à raiz: passa. |
| 2 | BH, EC, VG | Guard do `<style>` só apanha `<style id=` e `echo "<style` | `medium` — corrigido | `echo '<style>';` e `printf("%s","<style>")` sob `inc/` davam OK. Agora falham. |
| 3 | BH, EC, VG | Raiz por omissão ausente sai 0, indistinguível de limpo | `medium` — corrigido | Repo falso sem tema: saía 0. Agora avisa e sai 2. |
| 4 | BH | A linha "OK" contradizia a saída 2 quando havia caminhos ausentes | `medium` — corrigido | `bom.php` + caminho inexistente: dizia "sem problemas" e saía 2. |
| 5 | BH, VG | Nada corre as verificações do próprio guard; a prova era um transcript | `medium` — corrigido | Grep por `check-php` só encontrava `AGENTS.md` e o spec. |
| 6 | BH, VG | A linha OK não distinguia "correram e nada acharam" de "não correram" | `medium` — corrigido | Sem `inc/`, as duas estruturais nunca corriam. |
| 7 | BH | Guard do markup não apanhava `<!--wp:` sem espaço | `low` — corrigido | Variante válida do comentário de bloco. |
| 8 | BH, EC, VG | Menção em comentário dispara falso positivo | `medium` — corrigido | Um docblock em `inc/` não podia documentar a regra. |
| 9 | BH, EC | `--help` tratado como caminho inexistente; sem separador `--` | `low` — corrigido | |
| 10 | BH, EC | `declare -A` exige bash >= 4, sem guarda | `low` — corrigido | |
| 11 | EC | Argumento explícito que não é PHP é verificado e listado | `low` — corrigido | Dava cobertura falsa. |
| 12 | BH, EC | `find` sem `-print0` parte em nomes com nova linha | `low` — corrigido | |
| 13 | BH, EC | Ferramentas ausentes faziam a verificação não correr em silêncio | `low` — corrigido | Guarda de presença. |
| 14 | BH, EC | `php -l` mascarado sob `display_errors=Off` | **`false`** | Corri: a `PHP Parse error:` chega ao stderr mesmo com `display_errors=Off`, e o remédio proposto **duplica** a linha. |
| 15 | BH | Mensagem do `php -l` sem `LC_ALL=C` | **`false`** | O PHP não localiza mensagens de parse. Verificado. |
| 16 | BH | `AGENTS.md` não diz que as estruturais são só `inc/` | **`false`** | O bullet diz "sob `inc/`". |
| 17 | BH | `AGENTS.md` não nomeia os códigos de saída | `low` — não corrigido | O cabeçalho do script documenta-os e imprime-os; alongar uma linha do bloco gerido, paga em cada sessão, custa mais do que rende. |
| 18 | BH | `AGENTS.md` deixa PHP fora das duas raízes sem via documentada | **`false`** | Não existe outro PHP no checkout: os plugins não são versionados, por política. |
| 19 | BH | Deriva pt-PT/pt-BR em `AGENTS.md` | **`false`** | O mandato pt-BR é para a copy de interface; o bloco é narração para agentes, em pt-PT do princípio ao fim. |
| 20 | BH | Código 2 sobrecarregado | **`false`** | Ambos são erros de ambiente, documentados e impressos. Não há chamador a ramificar. |
| 21 | BH, EC | `find` sem `-L` ignora symlinks | `low` — não corrigido | Não há symlinks no tema nem nos mu-plugins. |
| 22 | BH | Deduplicação não normaliza `./x` e `x` | `low` — não corrigido | Duplicar uma linha num relatório não muda o veredicto. |
| 23 | EC | `php -l` não-zero por ficheiro ilegível é rotulado "sintaxe" | `low` — não corrigido | Falha de qualquer forma com saída 1; só o rótulo é impreciso. |
| 24 | BH | `REPO_ROOT` por `dirname` não resolve symlinks | `low` — não corrigido | O script vive no repositório. |
| 25 | BH | `docs/redesign-gate4-...md` não aponta ao script | **`false`** | É o registo histórico de uma corrida de 10/09; anotá-lo falsearia o registo. |
| 26 | BH | Duas convenções de data entre `sprint-status.yaml` e o spec | **`false`** | Cada uma é imposta pela sua ferramenta. |
| 27 | BH | `sprint-status.yaml` muda de estado sem evidência no próprio ficheiro | **`false`** | O ficheiro de estado regista estado; a evidência vive no spec. |
| 28 | BH | `epic-1-context.md`: contagem sem enumeração, unidades, "reflow", origem do rodapé, sem fontes nem dono, `muted`/`text-muted` | `low` — diferido | Artefacto regenerado por `compile-epic-context`; corrigir aqui divergiria da próxima compilação. |
| 29 | VG | `epic-1-context.md` continua a prescrever `php -l` e nunca adopta o script | `low` — diferido | Mesma razão: vem dos docs de planeamento, que também dizem `php -l`. |
| 30 | BH | Três grafias do mesmo título (ficheiro sem acento, chave com acento, título curto) | `low` — diferido | O prefixo `spec-` vem do passo 1 do `bmad-build`; a chave vem do `bmad-sprint-planning`. Reconciliação é a montante. |
| 31 | BH | `context: []` vazio apesar do `epic-1-context.md` criado nesta mudança | `low` — diferido | Observação para as histórias seguintes: 1.2 a 1.12 devem listá-lo. |
| 32 | BH, EC, VG | `AGENTS.md` vive dentro do bloco gerido, que um refresh substitui | `low` — diferido | Por regra do passo 4, achados cujo remédio edita ficheiros de contexto de agente vão para diferido. |

## Verification

**Commands:**
- `bash scripts/check-php.test.sh` -- esperado: 14 casos, todos ok, saída 0.
- `bash scripts/check-php.sh` -- esperado: lista os três ficheiros, diz que as estruturais não correram, sai 0.
- `bash scripts/check-php.sh --help` -- esperado: mostra a invocação, sai 0.
- `bash scripts/check-php.sh caminho/que/nao/existe.php` -- esperado: nomeia o caminho e sai 2.

**Resultado (verificado por mim, PHP 8.5.4):**
- Suite `check-php.test.sh`: **14 passaram, 0 falharam**, saída 0.
- Sem argumentos: 3 ficheiros listados, "verificações estruturais não correram — nenhum ficheiro sob inc/ (ainda)", saída 0.
- Só `functions.php`: saída 0. Caminho inexistente: saída 2. `--help`: saída 0.
- Fixtures sob `inc/` — sintaxe, `<!-- wp:`, `<!--wp:`, `<style id=`, `echo '<style>'`, `printf("<style>")`: todas apanhadas com ficheiro e linha, saída 1.
- `inc/` limpo: saída 0. Markup fora de `inc/`: não policiado, saída 0.
- Repo falso sem tema: avisa "raiz por omissão ausente" e sai 2.
- **Cenário do revisor:** clone em `.../inc/fakerepo/` com `<!-- wp:` no `functions.php` — não policiado, como deve ser.
- Nenhum resíduo: a pasta `inc/` usada nos testes foi removida.
