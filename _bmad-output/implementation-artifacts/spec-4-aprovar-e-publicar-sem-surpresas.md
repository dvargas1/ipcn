---
title: 'Aprovar e publicar sem surpresas'
type: 'feature'
created: '2026-09-30'
baseline_revision: 'ad157b44905bc1b3ca745ae2ffbcabc517fe0d3f'
status: 'done'
review_loop_iteration: 0
followup_review_recommended: true
context:
  - '_bmad-output/implementation-artifacts/epic-4-context.md'
warnings:
  - multiple-goals
  - oversized
deferred:
  - summary: >-
      A medição Lighthouse ≥ 90 em mobile do ambiente de revisão continua por fazer; o critério e o sítio da medição ficam escritos, o resultado não.
    evidence: >-
      `docs/aceite-stagingredesign.md` apresenta a medição como pendente, de propósito. Nenhum valor Lighthouse está registado no repositório (procurei em `docs/`, nas specs e no ledger) e o repositório não tem WordPress local: a medição só existe depois de deploy e purga em `stagingredesign`, que são acção do dono. Fecha-se com a passagem no browser, com `?nocache=1`, sobre as superfícies da primeira entrega, e com o registo do resultado se ele tiver de ficar no repositório.
    location: >-
      docs/aceite-stagingredesign.md
    severity: medium (unverified)
  - summary: >-
      O `sprint-status.yaml` continua a dizer `epic-4: backlog` com as três histórias em `backlog`, e o Épico 4 fica implementado e documentado sem que a superfície de tracking saiba.
    evidence: >-
      Medido no ficheiro. Nenhum passo do `bmad-build-auto` escreve esse ficheiro — o `Finalize` escreve a spec e o commit — e o `Never` desta história exclui-o de propósito, como já ficou registado para os Épicos 2 e 3. A reconciliação pertence ao `bmad-sprint-planning`, não a esta passagem.
    location: >-
      _bmad-output/implementation-artifacts/sprint-status.yaml
    severity: low
  - summary: >-
      O procedimento documenta o caminho feliz e não declara as suas limitações operacionais: não há caminho de recuperação para o código de saída 1 (nem cópia do tema remoto anterior), e as chamadas remotas assumem `wp` no PATH de uma sessão `ssh` não interactiva, o plugin `litespeed-cache` activo e `delete_pattern_cache()` a existir.
    evidence: >-
      Achados BH1 e BH5. O `scripts/deploy-staging.sh` não guarda a revisão remota anterior nem verifica essas dependências — só procura `ssh`, `scp`, `tar` e `mktemp` localmente (`:59-63`). O AC de 4.1 enumera o que o procedimento leva (pacote, envio, extracção, purga, caminho remoto, regra do mu-plugin, prova só depois da purga) e não pede um plano de recuperação; a recuperação que o código permite — re-correr o script, que sobrescreve — ficou escrita no quadro dos códigos de saída. O que o fecharia: um `--help`/secção de recuperação no script e uma verificação de pré-condições remotas na mesma passagem em que se mexer no script.
    location: >-
      docs/deploy-stagingredesign.md
    severity: low
---

<intent-contract>

## Intent

**Problem:** O fecho da entrega não tem artefacto. O procedimento de publicação existe só como uma linha de prosa no `README.md` (`:61`): nomeia o script, mas não o caminho remoto confirmado, nem as variáveis de configuração, nem o passo da cache de patterns, nem a regra do mu-plugin. A fronteira de produção — que nenhum pipeline, script ou hook deste repositório publica em produção, que não há credenciais nem `wp-config` nem cópias de segurança versionadas, que as únicas superfícies versionadas são o tema e os mu-plugins, e que o veredicto da Contratante é falado, não deixa artefacto e é condição da publicação — vive apenas em artefactos de planeamento BMAD e num comentário do script, nunca numa superfície que quem revê o repositório leia. E não existe nenhum artefacto de revisão: o plano de referência nomeia `docs/aceite-stagingredesign.md` e nada o entrega, pelo que quem dá o veredicto não tem o que percorrer.

**Approach:** Dois documentos operacionais em `docs/`, escritos a partir do código e do plano, mais o ponteiro do `README`. O primeiro transcreve o procedimento de deploy tal como o `scripts/deploy-staging.sh` o executa — pacote do tema, envio, extracção com `--strip-components=3` no tema do redesign, purga — com o caminho remoto confirmado, a regra do mu-plugin e a fronteira de produção. O segundo é a checklist de aceite da Contratante: as superfícies a percorrer no telemóvel e no computador, a regra de que um bloqueio de leitura é motivo de devolução, a medição de desempenho depois da purga e a lista de endereços que não podem mudar. Nenhuma linha de código do tema muda.

## Boundaries & Constraints

**Always:** os comandos do procedimento são exactamente os que o `scripts/deploy-staging.sh` executa — `tar -czf` do tema, `scp` para o servidor, `tar -xzf … --strip-components=3` no tema do redesign, e a purga por `wp --path=… litespeed-purge all`; o caminho remoto escrito é `$HOME/domains/ipcnbrasil.org/public_html/stagingredesign`, o alvo é o alias SSH `ipcn` (`147.93.38.215:65002`) e a chave `~/.ssh/ipcn_staging_ed25519`; o destino do deploy é só o `stagingredesign`, e o staging Divi está congelado; o mu-plugin só se deploya quando ele próprio muda, nunca numa alteração só de tema, e nunca para o staging Divi; fica escrito que HTML em cache não é prova e que a verificação do site é feita depois da purga, com `?nocache=1`; os documentos são em português e seguem a convenção de `docs/` (H1 e uma linha de metadata em blockquote); a checklist nomeia as seis superfícies — Home, uma Notícia, a Agenda, o Acervo, Associe-se e Apoia-se — no telemóvel e no computador; um bloqueio de leitura no telemóvel é, por si, motivo de devolução; a verificação de desempenho é Lighthouse ≥ 90 em mobile, medida no ambiente de revisão depois da purga, e é apresentada como pendente, não como feita; a lista de endereços que se confere é `/`, `/noticias/`, `/acervo/`, `/temas/<slug>/`, `/apoia-se/`, `/associe-se/`, `/fale-conosco/` e `/politica-de-privacidade/`; fica escrito que o veredicto é falado, que não deixa artefacto e que é condição da publicação; fica escrito que nenhum pipeline, script ou hook deste repositório publica em produção — a publicação é manual, no hPanel, pelo Daniel — que não há credenciais, `wp-config` nem cópias de segurança versionadas, e que as superfícies versionadas são só o tema e os mu-plugins.

**Never:** alterar `scripts/deploy-staging.sh` ou qualquer PHP, CSS, HTML ou `theme.json` do tema (o script já executa o procedimento: o que falta é documentação, não código); fazer deploy, purgar cache ou escrever no servidor; editar a base de dados; escrever caminhos, credenciais, portas ou chaves de produção; introduzir pipeline, CI, runner ou hook; editar `AGENTS.md`, `STATUS.md`, `ROADMAP.md` ou os artefactos de planeamento; transcrever para os documentos a prosa desactualizada do `README` sobre `functions.php`, `term_id` ou o shortcode `ipcn_home_agenda`; apresentar a medição Lighthouse como já realizada.

</intent-contract>

## Code Map

- `docs/deploy-stagingredesign.md` — **novo**. O procedimento repetível (história 4.1) e a fronteira de produção (história 4.2) num só documento operacional. Fonte vinculativa dos comandos: `scripts/deploy-staging.sh` — `LOCAL_THEME_REL` (`:57`), `SSH_TARGET` (`:65`), `REMOTE_WP_ROOT` (`:69`), `REMOTE_THEME` (`:89`), portas e chave (`:94-100`), o `tar -czf` (`:107`), o `scp` para `/tmp/ipcn-fse-$$.tgz` (`:104`, `:110`), a extracção `--strip-components=3` (`:114-115`), a limpeza da cache de patterns (`:119-120`), a purga (`:124-125`), os códigos de saída (`:30-33`) e a recusa de tocar nos mu-plugins (`:35`). Nomes dos docs vizinhos para a convenção de cabeçalho: `docs/redesign-gate4-paridade-2026-09-10.md`, `docs/auditoria-redesign-fse-2026-09-17.md`.
- `docs/aceite-stagingredesign.md` — **novo**. A checklist da Contratante (história 4.3). O nome é o que o plano de referência fixa: `docs/plano-tema-custom-e-portal-associados.md:263-265` («T7.1 — Checklist de aceite», DON «planilha `docs/aceite-stagingredesign.md`») — usar o nome registado, não inventar outro. O formato de checkbox tem precedente no mesmo plano (`:59-62`) e em `docs/auditoria-redesign-fse-2026-09-17.md` (tarefas com `- [ ]`); a evidência por código HTTP tem precedente em `docs/redesign-gate4-paridade-2026-09-10.md`.
- `README.md` — **alterado em duas linhas**: `:61` (a prosa de deploy) passa a apontar para `docs/deploy-stagingredesign.md`, e `:60` (a lista de `docs/`) passa a nomear os dois documentos. O resto do `README` não é tocado, incluindo as secções desactualizadas que a 2.2 e a 3 já diferiram.
- `wp-content/themes/ipcn-fse/templates/` — **só leitura**, para a checklist nomear o que serve cada superfície: `front-page.html` (Home), `single.html` (uma Notícia), `page-agenda-ipcn.html` (Agenda, `page-<slug>`), `archive-acervo_ipcn.html` (Acervo), `page-apoia-se.html` (Apoia-se, `page-<slug>`) e `page.html` mais o conteúdo da base de dados (Associe-se, Fale conosco, Política de privacidade). `/temas/<slug>/` é servido por `templates/archive.html` — não existe template de taxonomia (AD-8).
- `scripts/check-php.sh` e `scripts/check-php.test.sh` — **só leitura** e é a verificação sancionada (AD-13, NFR9). Sem argumentos verificam todo o tema e os mu-plugins (`:145-155`); a série de 14 casos do `check-php.test.sh` é a linha de base verde.
- `.gitignore` — **só leitura**, e é a prova de que `wp-config*.php`, `.env*`, `*.key`, `*.pem` e as pastas de cópias de segurança e de uploads ficam fora do controlo de versões.
- `AGENTS.md:10,13,14` — **só leitura**: a política que os documentos transcrevem (produção manual pelo Daniel no hPanel; o checkout só leva o tema e os mu-plugins; o mu-plugin não se deploya no staging Divi). É bloco gerido e não se edita.
- `_bmad-output/planning-artifacts/epics.md:531-578` e `_bmad-output/implementation-artifacts/epic-4-context.md` — **só leitura**: os AC das histórias 4.1 a 4.3 e o contexto compilado do épico.

## Tasks & Acceptance

**Execution:**
- `docs/deploy-stagingredesign.md` — escrever o procedimento repetível com os comandos exactos do `deploy-staging.sh` (pacote, envio, extracção com `--strip-components=3` no tema do redesign, limpeza da cache de patterns, purga), o caminho remoto confirmado, as variáveis de configuração e os códigos de saída; acrescentar as regras operacionais (destino só `stagingredesign`, mu-plugin só quando muda e nunca no staging Divi, HTML só é prova depois da purga) e a secção da fronteira de produção (nenhum pipeline/script/hook publica; publicação manual no hPanel pelo Daniel; sem credenciais, `wp-config` ou cópias de segurança versionadas; superfícies versionadas só tema e mu-plugins) — é o 4.1 e a parcela escrita do 4.2.
- `docs/aceite-stagingredesign.md` — escrever a checklist da Contratante: as seis superfícies a percorrer no telemóvel e no computador, a regra de que um bloqueio de leitura no telemóvel devolve a entrega, a medição Lighthouse ≥ 90 em mobile no ambiente de revisão depois da purga (marcada como pendente) e a conferência dos oito endereços existentes — é o 4.3, e o documento onde o veredicto falado e sem artefacto fica escrito como condição da publicação.
- `README.md` — `:61` passa a apontar para `docs/deploy-stagingredesign.md` e `:60` nomeia os dois documentos — o procedimento deixa de existir só em prosa e passa a ter uma porta de entrada no sítio onde quem faz deploy procura.
- `_bmad-output/implementation-artifacts/deferred-work.md` — registar o que esta passagem deixa aberto (a medição Lighthouse por fazer e a reconciliação do `sprint-status.yaml`) — o ledger mantém a dívida rastreável. Não editar entradas existentes.

**Acceptance Criteria:**
- Given `docs/deploy-stagingredesign.md`, when é lido, then contém o pacote do tema, o envio por `scp`, a extracção com `--strip-components=3` no tema do redesign e a purga `wp --path=… litespeed-purge all`, com o caminho remoto `$HOME/domains/ipcnbrasil.org/public_html/stagingredesign` escrito, e cada comando coincide com o que o `scripts/deploy-staging.sh` executa.
- Given o mesmo documento, when procuro as regras, then diz que o destino é só o `stagingredesign` (o staging Divi está congelado), que o mu-plugin só se deploya quando ele próprio muda e nunca para o staging Divi, e que o HTML só é prova depois da purga.
- Given `docs/aceite-stagingredesign.md`, when é lido, then lista as seis superfícies — Home, uma Notícia, a Agenda, o Acervo, Associe-se e Apoia-se — para o telemóvel e para o computador, diz que um bloqueio de leitura no telemóvel é motivo de devolução, exige Lighthouse ≥ 90 em mobile no ambiente de revisão depois da purga (sem o apresentar como feito) e confere os oito endereços `/`, `/noticias/`, `/acervo/`, `/temas/<slug>/`, `/apoia-se/`, `/associe-se/`, `/fale-conosco/` e `/politica-de-privacidade/`.
- Given o repositório versionado, when corro `git ls-files wp-content`, then só aparecem `wp-content/themes/ipcn-fse/` e `wp-content/mu-plugins/`; when corro `git ls-files` à procura de ficheiros de CI (`.github/`, `*.yml` na raiz, `Jenkinsfile`, `.gitlab-ci.yml`, `.circleci/`) e de segredos (`wp-config*.php`, `.env*`, `*.key`, `*.pem`), then nenhum está versionado; and `git ls-files scripts/` mostra só os três scripts e nenhum deles publica em produção.
- Given `docs/deploy-stagingredesign.md` e `docs/aceite-stagingredesign.md`, when procuro a fronteira de produção, then está escrito que nenhum pipeline, script ou hook deste repositório publica em produção, que a publicação é manual no hPanel pelo Daniel, e que o veredicto da Contratante é condição da publicação e é falado, sem artefacto.
- Given `README.md`, when é lido, then `:61` aponta para `docs/deploy-stagingredesign.md` e a lista de `docs/` nomeia os dois documentos novos.
- Given `bash scripts/check-php.sh`, when corro, then 0 problemas, 0 `sintaxe:` e saída 0; given `bash scripts/check-php.test.sh`, then 14/14; given `git diff --stat` da passagem, then nenhum ficheiro sob `wp-content/` nem sob `scripts/` foi alterado.

## Spec Change Log

## Review Triage Log

### 2026-09-30 — Review pass

- verdicts: 37 findings — high 0, medium 3, low 30, false 4, maybe-false 0
- findings:
  - `[low]` `[defer]` **BH1** o documento não tem caminho de recuperação para o código de saída 1 — Verdadeiro, e o `scripts/deploy-staging.sh` não guarda a revisão remota anterior nem faz rollback, pelo que documentar um seria inventar capacidade que o código não tem. O AC de 4.1 enumera o que o procedimento leva. Vai no `deferred` das limitações operacionais; a recuperação que existe — re-correr o script, que sobrescreve — ficou escrita no quadro dos códigos de saída.
  - `[low]` `[patch]` **BH2** ficheiros removidos no repositório continuam no servidor — Verdadeiro: o `tar -xzf` sobrescreve sem apagar (`scripts/deploy-staging.sh:115`) e o documento lia-se como se a árvore remota ficasse igual ao repositório. Corrigido: regra operacional nova («O `tar -xzf` sobrescreve mas não apaga…»).
  - `[medium]` `[patch]` **BH3** o contrato dos códigos de saída estava errado no caso que mais importa — Verdadeiro: o script sai 1 depois de um deploy feito com a purga falhada (`:126`) e também antes de escrever no servidor (`:107`, `:111`), e o documento dizia só «pode ter ficado a meio». Corrigido: o quadro separa os dois casos, diz que re-correr é seguro e isola o `mktemp`. Agrupa o EC2.
  - `[low]` `[reject]` **BH4** a regra do mu-plugin não tem procedimento por trás — Verdadeiro e fora do AC: o de 4.1 pede que o procedimento *diga* a regra (diz), não que crie a rota de deploy do mu-plugin, que nenhuma história possui.
  - `[low]` `[defer]` **BH5** as pré-condições das chamadas remotas não estão declaradas — Verdadeiro: `wp` no PATH de uma sessão `ssh` não interactiva, o plugin `litespeed-cache` activo e `delete_pattern_cache()` a existir são assumidos e não verificados. Mesma entrada de limitações operacionais do BH1.
  - `[medium]` `[patch]` **BH6** os comandos eram paráfrase, não transcrição — Verdadeiro: as três chamadas `ssh` do documento usavam `ipcn` sem `-p`/`-i`, e o script passa `ssh_flags`/`key_flags` a todas (`:114`, `:119`, `:124`); com `SSH_KEY` ou alvo não-alias as linhas falhavam. Corrigido: nota no passo-a-passo (todas as chamadas `ssh`/`scp` levam `-p`/`-P` e `-i`) e `$REPO_ROOT` explicitado. Agrupa o EC6 e o VGo1.
  - `[low]` `[patch]` **BH7** exigência de directório inventada — Verdadeiro: o script resolve `REPO_ROOT` a partir de `${BASH_SOURCE[0]}` (`:56`) e corre de qualquer directório. Corrigido nas duas frases.
  - `[false]` `[reject]` **BH8** o `?nocache=1` não teria fonte — Refutado: a convenção de cache-busting está registada no repositório e é o critério de pronto do plano de referência (`docs/plano-tema-custom-e-portal-associados.md:66,138,254,264`).
  - `[low]` `[reject]` **BH9** a verificação mede presença de palavras e não fidelidade ao script — Verdadeiro quanto ao facto e fora deste fluxo: o remédio seria editar a secção `## Verification` desta spec (rejeitado por regra) ou acrescentar infraestrutura de verificação que o NFR9 exclui; a camada de lacunas de verificação não achou lacuna nenhuma num diff só de prosa.
  - `[low]` `[reject]` **BH10** o cabeçalho do script aponta para o README — Verdadeiro, e o remédio é editar `scripts/deploy-staging.sh`, que o `Never` desta spec exclui; o cabeçalho enumera os passos que ele próprio descreve, pelo que ninguém fica sem a informação.
  - `[false]` `[reject]` **BH11** «Não há credenciais versionadas» contradito pelo próprio documento — Refutado: o documento publica host, utilizador, porta e o *caminho* da chave (sem material de chave nem password), tudo já publicado no `README.md:65` e no cabeçalho do script desde a 1.12; o `.gitignore` mantém `wp-config`, `.env`, `*.key` e `*.pem` fora.
  - `[false]` `[reject]` **BH12** publicador único e contradição com o `STATUS.md` — Refutado quanto ao documento: segue a superfície autoritativa (`README.md:67`, `AGENTS.md:10` — «manual pelo Daniel»); a divergência com `STATUS.md:12` («pela contratante») é anterior e não é desta passagem, e o texto diz que a regra é declarada, não que não exista em lado nenhum.
  - `[low]` `[reject]` **BH13** a spec exagera o que faltava — Verdadeiro quanto ao facto (`README.md:67` e `AGENTS.md:10` já declaram a publicação manual) e o remédio é editar o `<intent-contract>` desta spec, que a triagem rejeita; os documentos entregues não fazem essa afirmação.
  - `[low]` `[reject]` **BH14** a checklist não tem passagem de acessibilidade — O AC de 4.3 enumera o que a checklist leva (seis superfícies, bloqueio de leitura no telemóvel, Lighthouse, oito endereços) e a acessibilidade é dona do Épico 1; acrescentar itens seria âmbito que o intento não pede.
  - `[low]` `[reject]` **BH15** buracos na cobertura de superfícies (Item de Acervo, `/noticias/`, 404) — O AC fixa as seis superfícies e o documento entrega exactamente essas; as outras são adjacentes e não são o que o veredicto percorre.
  - `[low]` `[reject]` **BH16** «responde como antes» não tem linha de base — A formulação é a do próprio AC de 4.3 («respondem como antes») e o documento acrescenta o que o veredicto pode observar (200 e o conteúdo esperado).
  - `[low]` `[patch]` **BH17** a medida Lighthouse não fixa as páginas — Verdadeiro: «as superfícies da primeira entrega» não está enumerado em sítio nenhum. Corrigido: a secção 3 nomeia as seis páginas medidas. Agrupa o EC5.
  - `[low]` `[reject]` **BH18** a medição não é reproduzível (ferramenta, versão, throttle, viewport) — O AC fixa o critério («Lighthouse ≥ 90 em mobile»), o sítio (ambiente de revisão) e a ordem (depois da purga) e deixa a execução ao dono; prescrever preset e versão seria over-specification que o intento não pede.
  - `[low]` `[reject]` **BH19** os documentos novos não têm data nem revisão — O intento não pede âncora e o `git log` do ficheiro data-o; a doc de auditoria datada segue a convenção dos documentos de evento, que estes não são.
  - `[low]` `[reject]` **BH20** a devolução não tem canal nem regra de independência — O AC de 4.3 fixa o bloqueio de leitura como motivo de devolução (está escrito) e diz que o veredicto é falado; canal, independência e ciclo de re-revisão são processo que nenhum AC nomeia.
  - `[low]` `[reject]` **BH21** higiene da spec (avisos, registos vazios, estado) — O remédio edita esta spec (rejeitado por regra); os registos são append-only e são preenchidos por esta passagem, e o `status` e o `## Auto Run Result` são reescritos no `Finalize`.
  - `[low]` `[reject]` **BH22** entradas duplicadas no ledger e `STATUS.md`/`ROADMAP.md` alheios — A duplicação (spec + ledger) é a convenção das specs anteriores; `STATUS.md` e `ROADMAP.md` estão no `Never` desta spec e a reconciliação do tracking já vai no `deferred`.
  - `[low]` `[patch]` **BH23** enumeração inconsistente e mistura de registos — Verdadeiro dentro do documento de deploy, que usava «arquivo» para o ficheiro do `tar` ao lado de «ficheiro» dois parágrafos antes. Corrigido: «arquivo» → «ficheiro» nas duas ocorrências. O resto fica como está: «telemóvel» é o registo das capacidades e do contexto do épico.
  - `[low]` `[patch]` **EC1** `SSH_TARGET` sem alias e sem porta — Verdadeiro: a célula dava `SSH_TARGET=…` sem a porta, e o cabeçalho do script emparelha-o com `SSH_PORT=65002` (`scripts/deploy-staging.sh:22-23`). Corrigido: a célula passou a exigir `SSH_PORT=65002`.
  - `[medium]` `[patch]` **EC2** a purga a falhar depois da extracção é lida como deploy falhado — Mesmo achado do BH3; corrigido no quadro dos códigos de saída.
  - `[low]` `[patch]` **EC3** a pré-condição da purga na checklist não tem `ssh` nem `--path` — Verdadeiro: o script corre sempre `wp --path=… litespeed-purge all` (`:125`) e a forma nua não resolve instalação nenhuma a partir do `$HOME`. Corrigido: aponta ao passo 5 do procedimento. Agrupa o VGo2.
  - `[low]` `[reject]` **EC4** a superfície «Uma Notícia» não tem endereço — A superfície está nomeada como o AC pede, e fixar um slug seria escrever conteúdo da base de dados na checklist; quem percorre escolhe a notícia a partir de `/noticias/`.
  - `[low]` `[patch]` **EC5** o conjunto medido no Lighthouse não está enumerado — Mesmo achado do BH17; corrigido.
  - `[low]` `[patch]` **EC6** o documento fixa `ipcn` onde o script usa `$SSH_TARGET` — Mesmo achado do BH6; corrigido com a nota das flags.
  - `[false]` `[reject]` **EC7** a fronteira de produção viveria só em artefactos BMAD — Refutado: `README.md:67` e `AGENTS.md:10` já a declaram. Mesmo achado do BH13.
  - `[low]` `[patch]` **VGo1** as chamadas `ssh` do documento omitem `-p`/`-i` — Mesmo achado do BH6; corrigido.
  - `[low]` `[patch]` **VGo2** a purga da checklist sem `--path` — Mesmo achado do EC3; corrigido.
  - `[low]` `[reject]` **IA1** o intento aponta à superfície de ambiente e o diff fica na superfície de prosa — Descritivo e verdadeiro; o AC de 4.3 é um entregável escrito («Quando a checklist é escrita, Então lista…»), a medição e a passagem estão declaradas como pendentes (spec `## Verification` e `deferred`) e nenhum AC pede que esta passagem faça deploy.
  - `[low]` `[defer]` **IA2** a superfície de tracking não sabe do Épico 4 — Verdadeiro; já vai no `deferred` do `sprint-status.yaml`. Mesmo achado do BH26 da spec-3, agora para o Épico 4.
  - `[low]` `[reject]` **IA3** a verificação é presença de tokens — Mesmo achado do BH9; a camada de lacunas de verificação não achou lacuna em prosa.
  - `[low]` `[reject]` **IA4** a premissa «finalizamos o épico 3» não é confirmada no diff — Confirmada no passo 1: a `spec-2` e a `spec-3` estão `done`.
  - `[low]` `[reject]` **IA5** o último passo (push) não é coberto — O fluxo fecha com commit e não faz push (proibido no `Finalize`); o `push` é hábito do dono no `README.md:61`, não um AC do épico.

## Design Notes

**Porquê dois documentos e não um.** A 4.3 é a checklist de quem **dá o veredicto**, não de quem faz deploy: percorre superfícies e mede desempenho. O 4.1/4.2 é o procedimento de quem **executa** o deploy e o garante que nada publica sozinho. Juntá-los faria um documento com duas audiências e dois momentos de leitura; separados, cada um é entregável. O nome do segundo não é escolha livre — o plano de referência já o fixou em `docs/plano-tema-custom-e-portal-associados.md:265`, e inventar outro nome criaria dois artefactos para o mesmo compromisso.

**Porquê o script de deploy não muda.** O AC de 4.1 é sobre o **procedimento escrito** — «os comandos estão registados no repositório» — e o `scripts/deploy-staging.sh` já executa exactamente esses comandos desde a 1.12, incluindo a recusa de tocar nos mu-plugins (`:35`). O que falta é a documentação, e editar o script seria código sem história que o peça. O documento transcreve-o, e passa a ser contra ele que uma alteração futura do script se compara.

**Porquê a checklist apresenta a medição como pendente.** Nenhum valor Lighthouse está registado no repositório (procurei em `docs/`, nas specs e no ledger) e o ambiente não tem WordPress local: a medição só existe depois de deploy e purga em `stagingredesign`, que são acção do dono. O documento fixa o critério e o sítio da medição; não afirma um resultado que ninguém apurou.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: 0 problemas, 0 `sintaxe:`, saída 0 (nenhum PHP muda nesta passagem).
- `bash scripts/check-php.test.sh` — esperado: 14/14.
- `git diff --name-only` e `git status --porcelain` — esperado: só `README.md`, `docs/deploy-stagingredesign.md`, `docs/aceite-stagingredesign.md` e os artefactos BMAD; nada sob `wp-content/` nem `scripts/`.
- `grep -c 'strip-components=3' docs/deploy-stagingredesign.md` e `grep -c 'litespeed-purge all' docs/deploy-stagingredesign.md` — esperado: >= 1 cada.
- `grep -c 'domains/ipcnbrasil.org/public_html/stagingredesign' docs/deploy-stagingredesign.md` — esperado: >= 1.
- `grep -rn 'ipcn_home_agenda\|term_id\|functions.php' docs/deploy-stagingredesign.md docs/aceite-stagingredesign.md` — esperado: vazio (nenhuma prosa desactualizada transcrita).
- Para cada superfície e endereço: `grep -c '<termo>' docs/aceite-stagingredesign.md` — esperado: >= 1 para Home, Notícia, Agenda, Acervo, Associe-se, Apoia-se, e para os oito endereços.
- `grep -c 'Lighthouse' docs/aceite-stagingredesign.md` — esperado: >= 1.
- `git ls-files wp-content` — esperado: apenas `wp-content/themes/ipcn-fse/**` e `wp-content/mu-plugins/**`.
- `git ls-files | grep -E '^\.github/|^(Jenkinsfile|\.gitlab-ci\.yml|\.travis\.yml)$|^\.circleci/'` — esperado: vazio.
- `git ls-files | grep -E 'wp-config.*\.php$|\.env|\.key$|\.pem$'` — esperado: vazio.
- `grep -rn 'deploy-stagingredesign' README.md` — esperado: a ligação nova.

**Manual checks (if no CLI):**
- Deploy e purga são acção do dono. Depois deles, no `stagingredesign` com `?nocache=1`: correr a checklist de `docs/aceite-stagingredesign.md` superfície a superfície, no telemóvel e no computador, e medir o Lighthouse mobile; confirmar que os oito endereços respondem como antes. Esta passagem não faz deploy nem purga.

## Auto Run Result

**Implementado.** Os três entregáveis documentais do Épico 4 numa passagem, sem tocar em código do tema nem no script de deploy: o procedimento de deploy escrito e repetível, a fronteira de produção escrita, e a checklist de aceite da Contratante.

**Ficheiros alterados.**
- `docs/deploy-stagingredesign.md` — **novo**: o procedimento repetível (4.1) e a fronteira de produção (4.2). Transcreve os comandos que `scripts/deploy-staging.sh` executa — `tar -czf` do tema, `scp` para `/tmp/ipcn-fse-$$.tgz`, extracção com `--strip-components=3` no tema do redesign, limpeza da cache de patterns e purga `wp --path=… litespeed-purge all` — com o caminho remoto confirmado (`$HOME/domains/ipcnbrasil.org/public_html/stagingredesign`), as variáveis de configuração, os códigos de saída, as regras operacionais e a fronteira de produção.
- `docs/aceite-stagingredesign.md` — **novo**: a checklist da Contratante (4.3), com o nome que o plano de referência fixou (`:265`). As seis superfícies (Home, uma Notícia, a Agenda, o Acervo, Associe-se, Apoia-se), no telemóvel e no computador; o bloqueio de leitura no telemóvel como motivo de devolução; o Lighthouse ≥ 90 em mobile apresentado como pendente; e a conferência dos oito endereços.
- `README.md` — `:61` aponta para `docs/deploy-stagingredesign.md` e `:60` nomeia os dois documentos; o resto do `README` não foi tocado.
- `_bmad-output/implementation-artifacts/epic-4-context.md` — **novo**: contexto compilado do épico.
- `_bmad-output/implementation-artifacts/deferred-work.md` — duas entradas novas (a medição Lighthouse por fazer e a reconciliação do `sprint-status.yaml`), sem editar entradas existentes.
- `_bmad-output/implementation-artifacts/spec-4-aprovar-e-publicar-sem-surpresas.md` — esta spec.

**Triagem da revisão.** 37 achados de quatro camadas — `high` 0, `medium` 3, `low` 30, `false` 4, `maybe-false` 0 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **14 entradas patchadas em 8 correcções:** A) o contrato dos códigos de saída separa o deploy que ficou a meio da purga que falhou depois de o tema estar entregue, e isola o `mktemp` (BH3, EC2); B) o passo-a-passo passa a dizer que **todas** as chamadas `ssh`/`scp` levam `-p`/`-P` e `-i` quando `SSH_PORT`/`SSH_KEY` estão definidas, e `$REPO_ROOT` fica explicitado (BH6, EC6, VGo1); C) a exigência inventada de correr a partir da raiz do repositório sai das duas frases (BH7); D) a célula do `SSH_TARGET` sem alias passa a exigir `SSH_PORT=65002` (EC1); E) a pré-condição da purga na checklist deixa de ser a forma nua e aponta ao passo 5 do procedimento (EC3, VGo2); F) a secção do desempenho nomeia as seis páginas medidas (BH17, EC5); G) entra a regra de que o `tar -xzf` sobrescreve mas não apaga (BH2); H) «arquivo» → «ficheiro» no documento de deploy (BH23). **3 entradas diferidas:** a medição Lighthouse por fazer; a reconciliação do `sprint-status.yaml` (com o IA2); e as limitações operacionais do procedimento que o documento não declara — sem caminho de recuperação, com as pré-condições remotas assumidas (BH1, BH5). **20 rejeitadas:** 4 `false` refutadas na fonte — o `?nocache` é convenção registada do repositório (BH8), host/utilizador/porta/caminho-de-chave não são segredos e já estavam publicados desde a 1.12 (BH11), o documento segue a superfície autoritativa `README`/`AGENTS` na publicação manual pelo Daniel (BH12, com o EC7) — e 16 `low` fora do que o intento pede ou cujo remédio editaria esta spec: a rota de deploy do mu-plugin (BH4), a fidelidade executável do documento ao script (BH9, IA3 — a camada de lacunas de verificação não achou lacuna em prosa), o cabeçalho do script que aponta ao `README` (BH10 — editá-lo está no `Never`), o exagero do enunciado da spec (BH13), passagem de acessibilidade (BH14), superfícies adjacentes como o Item de Acervo e o 404 (BH15), linha de base do «como antes» (BH16), prescrição de ferramenta e throttle do Lighthouse (BH18), âncora de data/revisão (BH19), canal e independência da devolução (BH20), higiene do ficheiro desta spec (BH21), duplicação spec/ledger e `STATUS`/`ROADMAP` (BH22 — fora do `Never`), endereço fixo para a Notícia (EC4), a superfície de ambiente que o intento não manda esta passagem entrar (IA1), e a premissa e o push (IA4, IA5).

**Recomendação de passagem seguinte:** `followup_review_recommended: true` — numa primeira passagem, três entradas patched são `medium`: os códigos de saída (A), a fidelidade das chamadas `ssh` (B) e a purga falhada lida como deploy falhado (parte de A). Risco por verificar: as correcções ao documento de deploy foram verificadas por leitura do `scripts/deploy-staging.sh`, não por uma execução do script — esta passagem não fez deploy nem purga, pelo que a fidelidade do documento ao script (flags e códigos) continua assente em inspecção. Contagem por veredicto: `medium` 3, `low` 11.

**Verificação executada.**
- `bash scripts/check-php.sh` → **0 problemas**, **0 `sintaxe:`**, saída **0** (14 ficheiros).
- `bash scripts/check-php.test.sh` → **14/14**.
- `grep -c 'strip-components=3'` → 3; `grep -c 'litespeed-purge all'` → 2; `grep -c 'domains/ipcnbrasil.org/public_html/stagingredesign'` → 7 (todos ≥ 1).
- `grep -rn 'ipcn_home_agenda\|term_id\|functions.php' docs/deploy-stagingredesign.md docs/aceite-stagingredesign.md` → **vazio** (nenhuma prosa desactualizada transcrita).
- Para as seis superfícies e os oito endereços: `grep -c` ≥ 1 em todos; `grep -c 'Lighthouse'` → 2.
- `git ls-files wp-content` → apenas `wp-content/themes/ipcn-fse/**` e `wp-content/mu-plugins/**`; `git ls-files scripts/` → os três scripts.
- `git ls-files` para CI (`.github/`, `Jenkinsfile`, `.gitlab-ci.yml`, `.travis.yml`, `.circleci/`) e segredos (`wp-config*.php`, `.env*`, `.key`, `.pem`) → **vazio**.
- `git status --porcelain` → nada sob `wp-content/` nem sob `scripts/`.
- A verificação da spec foi re-corrida depois das correcções da revisão; os dez `grep` continuam conforme o esperado.

**Riscos residuais.** O procedimento transcrito passa a ser a referência contra a qual uma alteração futura do script se compara; se o script mudar, o documento tem de o acompanhar, e essa fidelidade é hoje verificada por leitura, não por execução. A medição Lighthouse ≥ 90 em mobile e a passagem pelas seis superfícies e oito endereços ficam para o dono, depois de deploy e purga em `stagingredesign`, que esta passagem não fez. Ficam diferidas as limitações operacionais do procedimento (sem caminho de recuperação, pré-condições remotas assumidas) e a reconciliação do `sprint-status.yaml`, que pertence ao `bmad-sprint-planning`.
