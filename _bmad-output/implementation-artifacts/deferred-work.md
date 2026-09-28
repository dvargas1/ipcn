- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O `AGENTS.md` passa a nomear o script de verificação dentro do bloco gerido, que um refresh do `bmad-project-context` substitui.
  evidence: O próprio cabeçalho do bloco diz que edições dentro dos marcadores são substituídas num refresh. O remédio — repetir a invocação fora dos marcadores, ou no `README` — edita ficheiros de contexto de agente, pelo que foi diferido em vez de corrigido aqui. Fica por confirmar após o primeiro refresh: se a linha sobreviver, não há nada a fazer.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O `epic-1-context.md` continua a prescrever `php -l` e nunca adopta o script que declara pré-requisito.
  evidence: A linha 33 do contexto diz "sem build step, testes ou CI (`php -l` no PHP alterado mais browser)", enquanto o `AGENTS.md` e a história 1.1 passam a mandar correr `bash scripts/check-php.sh`. Um agente que comece a história 1.2 a partir desse contexto corre `php -l` e para, deixando as duas verificações estruturais por fazer. O contexto é gerado por `compile-epic-context` a partir dos docs de planeamento, que também dizem `php -l` — corrigir num só lado voltaria a divergir na próxima compilação. Fecha-se num refresh dos docs de planeamento, não numa história.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O mesmo artefacto da história 1.1 tem três grafias — ficheiro sem acento, chave do estado com acento, título mais curto.
  evidence: O ficheiro é `spec-1-1-verificacao-...` (sem acento, com prefixo `spec-`), a chave no `sprint-status.yaml` é `1-1-verificação-...` (com acento, sem prefixo) e o título no frontmatter é "Verificação antes de mexer no tema". O `sprint_plan.py` só reconhece um ficheiro de história com o nome exacto `{chave}.md`, pelo que nunca fará subir esta história a `ready-for-dev` pelo disco. O prefixo vem do passo 1 do `bmad-build` e a chave do `bmad-sprint-planning`: é uma incompatibilidade entre dois passos do método, não um defeito local. Reconciliação a montante.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-2-o-comportamento-sai-de-functions-php-para-inc.md`
  summary: Nada fixa o estado aceite do `check-php.sh` (17 problemas de block markup sob `inc/`) nem resolve os alvos dos `require_once` do carregador, e o comportamento movido não é executado por teste nenhum.
  evidence: Achados da rodada 2 da revisão da 1.2 (#43, #44, #47, #61, #72, #73, #74, #77). É real e introduzido por esta história: o carregador é o único código novo e o `check-php.sh` passa de verde a sempre-vermelho. Verificado: apagar `inc/cookie-bar.php` ou trocar um caminho por `inc/typo.php` deixa o `check-php.sh` a sair 1 com os mesmos 17 problemas, e o `php -l` não resolve includes. O remédio é infraestrutura de verificação (uma verificação no `scripts/check-php.sh` da 1.1 que resolva cada `require_once`, uma asserção do estado aceite, um runtime WordPress), fora do movimento literal. Fecha-se quando essas verificações existirem e a passagem no browser em `stagingredesign` confirmar o comportamento.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-2-o-comportamento-sai-de-functions-php-para-inc.md`
  summary: Defeitos pré-existentes moveram-se verbatim para `inc/`: paginação de `ipcn_query_posts` (ramo morto `$paged < 1`, `$paged` sem limite, base que já contém `/page/N/`, `strtok` sem guarda, `per_page` sem limite), guarda de origem ausente nos `admin_post_nopriv`, consentimento de cookies sem expiração, `meta_query` da agenda que esconde eventos só-por-data, `$thumb` recolhido e nunca impresso.
  evidence: Achados #5, #6, #7, #31–#36, #40, #63–#70 da triagem da 1.2. Todos existem no `functions.php` em `b6e8c04` e foram movidos sem alteração; a história 3.3 é dona da verificação de origem nos formulários. O `meta_query` da agenda (um `data_evento` presente esconde todos os eventos por `post_date`) é a parcela que a história 2.2 deve reconsiderar ao reconstruir a agenda.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-2-o-comportamento-sai-de-functions-php-para-inc.md`
  summary: Deriva de documentação e de contexto de agente depois da repartição: `AGENTS.md` (enumeração de `inc/` incompleta, selo «Verified 2026-09-24» falso, pré-voo sem a ressalva dos 17 problemas esperados), resíduo em `README.md`/`STATUS.md`, e o comentário de `scripts/check-php.sh:15-16`.
  evidence: Achados #18, #19, #20, #21, #28, #42, #51, #52, #56, #57, #58, #60, #71, #75 da triagem da 1.2. O `AGENTS.md` vive dentro do bloco gerido, que um refresh do `bmad-project-context` substitui; `README.md:21,22,58` e `STATUS.md` continuam a apontar `functions.php`; o comentário em `scripts/check-php.sh` justifica o âmbito `inc/` com um defeito de `functions.php` que já não existe. Fecha-se com um refresh dos docs de planeamento e uma passagem ao bloco gerido.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O campo `context:` do spec devia nomear o `epic-1-context.md` criado pela própria história.
  evidence: O `context:` está vazio, e o `epic-1-context.md` foi compilado no passo 1 exactamente para ser lido por quem implementa. O spec diz que o implementador deve carregar os ficheiros do `context:` antes de começar, e o documento que mais lhe interessa não está lá. Vale para as histórias 1.2 a 1.12, que devem listá-lo.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-3-um-cartao-so-com-as-casas-fixadas.md`
  summary: Nada resolve o slug `ipcn/card` que as quatro listagens, o wrapper `ipcn_query_posts` e os patterns declaram em sítios diferentes — um desalinhamento renderiza cartões vazios em silêncio e nada fica vermelho.
  evidence: Achados #21, #31, #32 e #17 da triagem da 1.3. O `scripts/check-php.sh` nunca lê um `.html` e as suas verificações estruturais limitam-se a `inc/`; o `scripts/check-php.test.sh` não carrega o tema. Demonstração reproduzida: trocar o slug em `inc/listings.php` ou o `Slug:` do cabeçalho de `patterns/ipcn-card.php` deixa os testes exactamente como estão e as páginas sem cartões (a grelha fica com a paginação e o título de secção). O contexto do post no wrapper tem a mesma forma: mudar o nome do hook `render_block_context` não faz falhar nada e esvazia cada cartão. Fecha-se com uma verificação no `scripts/check-php.sh` que resolva cada `Slug:` de `patterns/` contra os `<!-- wp:pattern {"slug":…} -->` dos templates e contra o literal do wrapper, com um caso no `scripts/check-php.test.sh`, mais a passagem no browser dos ecrãs que chamam `[ipcn_query_posts]`.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-3-um-cartao-so-com-as-casas-fixadas.md`
  summary: O cartão novo não tem ajuste de largura estreita, e as duas grelhas que o mostram colapsam em pontos diferentes — 900/600px no `.ipcn-grid` do shortcode e 768px no `is-layout-grid` dos blocos.
  evidence: Achado #18 da triagem da 1.3. As duas regras de colapso são pré-existentes, mas a 1.3 passa a mostrar o mesmo cartão por ambas, o que torna a divergência visível; a tipografia do cartão (título 19px, `clamp(26px,3vw,34px)` no destaque) também não tem ajuste abaixo dos 768px. A passagem de leitura no telemóvel até 320px é a história 1.9, que é onde isto se fecha.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-3-um-cartao-so-com-as-casas-fixadas.md`
  summary: O bullet novo do `AGENTS.md` sobre o cartão está incompleto e um dos seus juízos é falso para o repositório: afirma que nenhum markup de cartão é escrito à mão em PHP quando `inc/agenda-block.php` ainda o escreve, e não nomeia o contrato de classes, a armadilha do contexto do post fora de um loop, nem a dívida viva do `.ipcn-card-v2`.
  evidence: Achados #15, #29 e #33 da triagem da 1.3. O remédio edita o bloco gerido do `AGENTS.md`, que um refresh do `bmad-project-context` substitui — o mesmo bloco tem ainda o selo «Verified 2026-09-24» e a enumeração de `inc/` incompleta, já diferidos pela 1.2. Nota cruzada: o `epics.md` (AR3) diz que «`ipcn-card-v2` está retirada», mas o repo ainda o usa na agenda; a 2.2 fecha essa contradição ao migrar a agenda para o cartão.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-3-um-cartao-so-com-as-casas-fixadas.md`
  summary: A regra genérica `.wp-block-post-template.is-layout-grid .wp-block-group.has-background` continua a vestir qualquer grupo com fundo dentro de uma grelha (agora excepto `.ipcn-card`), sem se saber quem mais depende dela.
  evidence: Achado #6 da triagem da 1.3. A 1.3 tirou-lhe o cartão (`:not(.ipcn-card)`), pelo que deixa de haver dois donos do mesmo chrome, mas a regra continua a ser um segundo dono para grupos alheios; apagá-la exige auditar templates e conteúdo guardados na base de dados, que este repositório não vê. A história 1.4 (identidade e foco) é dona do `style.css` e é onde isto se fecha.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-3-um-cartao-so-com-as-casas-fixadas.md`
  summary: O pattern `ipcn/card-feature` nasce sem consumidor, logo a linha «Destaque» da matriz da 1.3 não é exercitável antes da 1.5.
  evidence: Achado #14 da triagem da 1.3. O AD-15 atribui-lhe a vitrine do Acervo na Home (dois `core/query` irmãos, um com `perPage: 1`), que é a história 1.5; até lá a linha só se cobre por inspecção do markup (4:3 e escala de headline declaradas). Fecha-se quando a 1.5 montar a vitrine e a linha for vista no browser em `stagingredesign`.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O `epic-1-context.md` tem imprecisões de conteúdo e unidades que a próxima compilação deve corrigir.
  evidence: A linha "Paleta (13 tokens)" afirma uma contagem sem enumerar os tokens, pelo que não é conferível contra o `theme.json`; unidades inconsistentes ("medida 720, grelhas 1100" sem `px`, contra "margem de 20px"); "reflow" sem glosa num documento em português corrente; o rótulo do rodapé aparece como "Contacto" e como "Contato" em bullets vizinhos, quando o markup do tema diz "Contato"; e o documento não tem `created`/`updated`, dono, nem ligações aos artefactos irmãos. É artefacto gerado — corrige-se na fonte de planeamento, não à mão.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: Pares de cor que carregam texto no `style.css` não têm razão de contraste declarada no `DESIGN.md` — estados de formulário e de cookies, `#991b1b`/`#166534`/`#94a3b8`/`#131736` e o ocre do marcador `.ipcn-card-noimg` sobre navy.
  evidence: O AC da história 1.4 no `epics.md` pede que «cada par que carrega texto tenha a razão de contraste declarada no `DESIGN.md`», e o `DESIGN.md` declara `error`/`success` com hex próprios (`#b3261e`, `#1b5e20`), diferentes dos que o `style.css` usa (`#991b1b`, `#166534`), e não declara o placeholder `#94a3b8` nem o ocre sobre navy. Decisão de âmbito da 1.4 (secção *Decisions* da spec): limitar a história aos tokens declarados e ao foco. Os pares de formulário pertencem à 3.x, o da barra de cookies à 3.6 e o do marcador `.ipcn-card-noimg` ao cartão, que é da 2.2.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: Documentação do regime de foco desactualizada depois da 1.4: `README.md:44` e `docs/auditoria-redesign-fse-2026-09-17.md:15` continuam a dizer «focus-visible terracota», e o comentário órfão de `style.css:753` fica a parecer o rótulo da regra global.
  evidence: Achados da camada de lacunas de verificação (#33) e da revisão cega (#20). A 1.4 trocou o anel terracota pelo anel duplo `base`+`navy`, mas o `README.md` — que o `AGENTS.md` nomeia como autoridade quando discorda do `STATUS.md` — e a auditoria datada descrevem o regime antigo. O comentário de `style.css:753` já precedia o bloco de foco em `b773cf9` (desarrumação pré-existente), mas a regra nova passa a viver debaixo dele. Fecha-se numa passagem de documentação e na decisão de mover ou apagar esse comentário.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: O nome de ficheiro `spec-1-4-a-identidade-e-o-foco.md` não coincide com a chave do ledger (`1-4-a-identidade-e-o-foco`), pelo que o `sprint_plan.py` não resolve a história a partir do disco.
  evidence: Achado da revisão cega, real mas não nascido desta história: é a mesma incompatibilidade entre o passo 1 do `bmad-build` (que escreve `spec-{slug}.md`) e o `bmad-sprint-planning` já registada para a 1-1 neste ficheiro. O prefixo é imposto pelo método e a 1.4 não o pode fechar nem agravar; repete-se aqui só para não se perder a contagem de ocorrências. Reconciliação a montante.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: `style.css:524-530` repete o chrome do CTA primário com declarações mortas (`background: #c9a86a !important` e `color: #131736 !important`), sobrepostas por `style.css:738-741`.
  evidence: Achado da revisão cega (#10), pré-existente: a 1.4 não tocou nessas linhas. O bloco `REFINAMENTO v2.1` reescreve o mesmo selector com chumbo `#2d2418` e vence por ordem de ficheiro, pelo que o `#131736` e o `#c9a86a` de `524-526` nunca pintam. Não é texto claro sobre ocre (é escuro), logo não toca o AC da 1.4. Fecha-se numa limpeza do `style.css`, que a 1.8 ou a 1.10 podem fazer.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: A regra global `:focus-visible` chega também ao editor do site, porque `inc/setup.php:69-78` enfileira o mesmo `style.css` em `enqueue_block_editor_assets`; se o efeito deixar o editor aceitável não se sabe sem browser.
  evidence: Achado da camada de casos-limite (#27), com severidade por confirmar — seria `medium`, porque o `:focus-visible` nu com `!important` em `outline` e `box-shadow` se sobrepõe ao foco nativo dos controlos do editor e o parágrafo editável casa `:focus-visible`. O que o fecha é a passagem no browser em `stagingredesign` pelo editor, a par da do site; se for preciso corrigir, o remédio é limitar a regra ao frontend, verificado para não perder a cobertura de lá.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: O par `ocre-hover` `#e2b878` com texto chumbo `#2d2418` (`.ipcn-cta-primario:hover`, `style.css:744-748`) não tem razão declarada no `DESIGN.md`; a razão (8.25:1) só aparece no `README.md:40`.
  evidence: Achado da camada de casos-limite (#30). O `DESIGN.md` declara `hoverBackgroundColor: {colors.ocre-hover}` em `button-primary` mas só dá a razão do repouso (6.75:1). Faz parte da mesma dívida dos pares de texto sem razão declarada registada acima; fecha-se quando o `DESIGN.md` ganhar a linha, ou quando a 3.x alinhar as cores dos CTAs com os tokens.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: O anel de foco não tem verificação executável — o `check-php.sh` nunca lê um `.css` e o `check-php.test.sh` não carrega o tema, pelo que trocar a regra global por uma lista fechada, que é o defeito que a 1.4 corrige, passa no mesmo verde.
  evidence: Achado da camada de lacunas de verificação (#31), com disposição `defer`: o projecto não tem runner nem CI (NFR9) e a verificação sancionada (AD-13) é o `check-php.sh` mais o browser, com a passagem em `stagingredesign` por fazer neste passo. Fecha-se com essa passagem; tornar o passo mecânico seria infraestrutura de verificação (uma verificação de CSS no `scripts/check-php.sh`), não esta história.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-4-a-identidade-e-o-foco.md`
  summary: A regra genérica `.wp-block-post-template.is-layout-grid .wp-block-group.has-background` continua sem dono — a 1-3 mandou-a fechar na 1.4 e a 1.4 não lhe tocou.
  evidence: Achado da revisão cega (#19). O registo da 1-3 neste ficheiro diz «A história 1.4 (identidade e foco) é dona do `style.css` e é onde isto se fecha», mas o âmbito congelado da 1.4 é o anel de foco; a regra segue a servir grupos com fundo alheios, já sem o cartão (`:not(.ipcn-card)`). Reatribui-se a 1.8 ou 1.10, que arrumam as superfícies públicas.

