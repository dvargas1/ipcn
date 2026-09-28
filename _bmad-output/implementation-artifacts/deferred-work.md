- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O `AGENTS.md` passa a nomear o script de verificação dentro do bloco gerido, que um refresh do `bmad-project-context` substitui.
  evidence: O próprio cabeçalho do bloco diz que edições dentro dos marcadores são substituídas num refresh. O remédio — repetir a invocação fora dos marcadores, ou no `README` — edita ficheiros de contexto de agente, pelo que foi diferido em vez de corrigido aqui. Fica por confirmar após o primeiro refresh: se a linha sobreviver, não há nada a fazer.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O `epic-1-context.md` continua a prescrever `php -l` e nunca adopta o script que declara pré-requisito.
  evidence: A linha 33 do contexto diz "sem build step, testes ou CI (`php -l` no PHP alterado mais browser)", enquanto o `AGENTS.md` e a história 1.1 passam a mandar correr `bash scripts/check-php.sh`. Um agente que comece a história 1.2 a partir desse contexto corre `php -l` e para, deixando as duas verificações estruturais por fazer. O contexto é gerado por `compile-epic-context` a partir dos docs de planeamento, que também dizem `php -l` — corrigir num só lado voltaria a divergir na próxima compilação. Fecha-se num refresh dos docs de planeamento, não numa história.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O mesmo artefacto da história 1.1 tem três grafias — ficheiro sem acento, chave do estado com acento, título mais curto.
  evidence: O ficheiro é `spec-1-1-verificacao-...` (sem acento, com prefixo `spec-`), a chave no `sprint-status.yaml` é `1-1-verificação-...` (com acento, sem prefixo) e o título no frontmatter é "Verificação antes de mexer no tema". O `sprint_plan.py` só reconhece um ficheiro de história com o nome exacto `{chave}.md`, pelo que nunca fará subir esta história a `ready-for-dev` pelo disco. O prefixo vem do passo 1 do `bmad-build` e a chave do `bmad-sprint-planning`: é uma incompatibilidade entre dois passos do método, não um defeito local. Reconciliação a montante.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O campo `context:` do spec devia nomear o `epic-1-context.md` criado pela própria história.
  evidence: O `context:` está vazio, e o `epic-1-context.md` foi compilado no passo 1 exactamente para ser lido por quem implementa. O spec diz que o implementador deve carregar os ficheiros do `context:` antes de começar, e o documento que mais lhe interessa não está lá. Vale para as histórias 1.2 a 1.12, que devem listá-lo.

- source_spec: `_bmad-output/implementation-artifacts/spec-1-1-verificacao-de-sintaxe-antes-de-mexer-no-tema.md`
  summary: O `epic-1-context.md` tem imprecisões de conteúdo e unidades que a próxima compilação deve corrigir.
  evidence: A linha "Paleta (13 tokens)" afirma uma contagem sem enumerar os tokens, pelo que não é conferível contra o `theme.json`; unidades inconsistentes ("medida 720, grelhas 1100" sem `px`, contra "margem de 20px"); "reflow" sem glosa num documento em português corrente; o rótulo do rodapé aparece como "Contacto" e como "Contato" em bullets vizinhos, quando o markup do tema diz "Contato"; e o documento não tem `created`/`updated`, dono, nem ligações aos artefactos irmãos. É artefacto gerado — corrige-se na fonte de planeamento, não à mão.
