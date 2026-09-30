# Deploy do tema para o ambiente de revisão (stagingredesign)

> Procedimento repetível do tema `ipcn-fse` para `stagingredesign.ipcnbrasil.org` — pacote, envio, extracção e purga — mais a fronteira de produção deste repositório. Os comandos são exactamente os que `scripts/deploy-staging.sh` executa; este documento transcreve-os e passa a ser contra ele que uma alteração futura do script se compara.

---

## O que este procedimento entrega

Entrega o tema `wp-content/themes/ipcn-fse/` ao ambiente de revisão `stagingredesign`, sem tocar em produção e sem tocar no staging Divi. É uma só invocação — funciona a partir de qualquer directório, porque o script resolve a raiz do repositório a partir do próprio caminho:

```bash
bash scripts/deploy-staging.sh
```

O script usa por omissão o alias SSH `ipcn` e o caminho remoto confirmado (abaixo). `bash scripts/deploy-staging.sh --help` mostra o próprio cabeçalho de ajuda.

## Caminho remoto confirmado

Estes são os caminhos verificados no servidor, não inferidos:

| O quê | Caminho |
|---|---|
| Raiz do WordPress no ambiente de revisão | `$HOME/domains/ipcnbrasil.org/public_html/stagingredesign` |
| Tema do redesign | `$HOME/domains/ipcnbrasil.org/public_html/stagingredesign/wp-content/themes/ipcn-fse` |

O tema fica em `<raiz>/wp-content/themes/ipcn-fse`, com a mesma estrutura do repositório. O script escreve o caminho remoto entre aspas duplas no comando remoto, onde `~` não expande mas `$HOME` expande.

## Parâmetros de configuração

O script aceita estes parâmetros por ambiente. Os valores por omissão foram verificados no servidor:

| Variável | Por omissão | Para que serve |
|---|---|---|
| `SSH_TARGET` | `ipcn` | Alvo de `ssh`/`scp` — o alias de `~/.ssh/config` que aponta para `147.93.38.215:65002`. Sem alias: `SSH_TARGET=u654777386@147.93.38.215` **e** `SSH_PORT=65002` (sem a porta, `ssh`/`scp` iriam para a 22). |
| `SSH_PORT` | *(vazio)* | Porta, quando o alvo não é um alias que já a defina. Só se usa se estiver definida (ex.: `65002`). |
| `SSH_KEY` | *(vazio)* | `IdentityFile` a usar. Chave do ambiente: `~/.ssh/ipcn_staging_ed25519`. |
| `REMOTE_WP_ROOT` | `$HOME/domains/ipcnbrasil.org/public_html/stagingredesign` | Raiz do WordPress remoto. Tem de ser absoluta ou começar por `$HOME/`; o resto do caminho só aceita `[A-Za-z0-9._/-]`. |

Exemplo com outra raiz e a chave explícita:

```bash
REMOTE_WP_ROOT='$HOME/outro/site' SSH_KEY=~/.ssh/ipcn_staging_ed25519 bash scripts/deploy-staging.sh
```

O script verifica primeiro que `ssh`, `scp`, `tar` e `mktemp` existem no `PATH` e que o tema local existe; se faltar algum, para com código de saída 2 sem enviar nada.

## O procedimento, passo a passo

O que o `scripts/deploy-staging.sh` corre, na ordem. `$tmp` é um ficheiro de `mktemp` local; `$$` é o PID do próprio script. As amostras abaixo usam os valores por omissão (alvo `ipcn`, sem porta nem chave explícitas); quando `SSH_PORT` ou `SSH_KEY` estão definidas, **todas** as chamadas `ssh` e `scp` levam `-p`/`-P` e `-i` com esses valores.

### 1. Pacote do tema

```bash
tar -czf "$tmp" -C "$REPO_ROOT" "wp-content/themes/ipcn-fse"
```

`$REPO_ROOT` é a raiz do repositório, resolvida pelo script. O ficheiro guarda os caminhos `wp-content/themes/ipcn-fse/...`.

### 2. Envio para o `/tmp` do servidor

```bash
scp "$tmp" "ipcn:/tmp/ipcn-fse-$$.tgz"
```

Se `SSH_PORT`/`SSH_KEY` estiverem definidas, os flags juntam-se: `scp -P 65002 -i ~/.ssh/ipcn_staging_ed25519 …`.

### 3. Extracção no tema do redesign, com `--strip-components=3`

```bash
ssh ipcn "mkdir -p \"\$HOME/domains/ipcnbrasil.org/public_html/stagingredesign/wp-content/themes/ipcn-fse\" \
  && tar -xzf \"/tmp/ipcn-fse-$$.tgz\" --strip-components=3 -C \"\$HOME/domains/ipcnbrasil.org/public_html/stagingredesign/wp-content/themes/ipcn-fse\" \
  && rm -f \"/tmp/ipcn-fse-$$.tgz\""
```

O `--strip-components=3` retira os três componentes `wp-content/`, `themes/` e `ipcn-fse/` do caminho guardado no ficheiro, pelo que o conteúdo do tema (os directórios e ficheiros) fica directamente dentro do tema remoto. O ficheiro temporário remoto é apagado no mesmo comando.

### 4. Limpeza da cache de patterns do tema

```bash
ssh ipcn "wp --path=\"\$HOME/domains/ipcnbrasil.org/public_html/stagingredesign\" eval 'wp_get_theme()->delete_pattern_cache();'"
```

Sem este passo, as listagens ficam sem cartões até o transient expirar (cerca de 30 minutos), porque a versão do tema não muda no deploy.

### 5. Purga da cache

```bash
ssh ipcn "wp --path=\"\$HOME/domains/ipcnbrasil.org/public_html/stagingredesign\" litespeed-purge all"
```

A purga é `wp litespeed-purge all` (WP-CLI + plugin `litespeed-cache`). **Não existe nenhum binário `litespeed-purge` no servidor** — a invocação é sempre pelo `wp`. O HCDN é teimoso: sem esta purga, pode continuar a servir HTML velho.

## Códigos de saída

| Código | Significado |
|---|---|
| `0` | Deploy feito, com a purga. |
| `1` | Um passo falhou. Pode ter ficado a meio — o `tar` e o `scp` falham antes de escrever no servidor, e uma extracção falhada deixa o tema incompleto — **ou** o tema pode estar entregue e só a purga (ou a limpeza da cache de patterns) ter falhado, caso em que o HCDN pode continuar a servir HTML velho. Re-correr o script é seguro: o `tar -xzf` sobrescreve. |
| `2` | Erro de ambiente ou de configuração (ferramenta em falta, raiz inválida, opção desconhecida). |

Um `mktemp` local em falta não cabe nesta tabela: o script sai com o código do próprio `mktemp`, sem mensagem.

## Regras operacionais

- **Destino único: `stagingredesign`.** O staging Divi (`staging.ipcnbrasil.org`) está congelado e não é destino deste trabalho.
- **O `tar -xzf` sobrescreve mas não apaga.** Um ficheiro ou template removido no repositório continua no servidor até ser apagado à mão; o deploy não sincroniza remoções.
- **O mu-plugin só se deploya quando ele próprio muda**, nunca numa alteração só de tema — e nunca para o staging Divi, onde o comportamento se mantém idêntico sem ele. O `scripts/deploy-staging.sh` empacota só o tema e não toca nos mu-plugins.
- **HTML em cache não é prova.** A verificação do site — desempenho incluído — é feita depois da purga, no ambiente de revisão, com `?nocache=1`. Um HTML servido antes da purga pode já não corresponder ao código entregue.
- **O script não commita.** O commit e o `push` são passos separados, na mesma passagem do trabalho.

## Fronteira de produção

Este repositório não publica em produção. É a garantia da história 4.2:

- **Nenhum pipeline, script ou hook deste repositório publica em produção.** O `scripts/deploy-staging.sh` só escreve no `stagingredesign`; não há `.github/`, `Jenkinsfile`, `.gitlab-ci.yml` nem `.circleci/` versionados.
- **A publicação é manual, no hPanel, pelo Daniel.** Não é inferida do `README` nem de outro documento: é a regra declarada deste repositório.
- **Não há credenciais, `wp-config` nem cópias de segurança versionadas.** O `.gitignore` mantém `wp-config*.php`, `.env*`, `*.key`, `*.pem` e as pastas de cópias de segurança e de uploads fora do controlo de versões.
- **As superfícies versionadas são só o tema e os mu-plugins** — `wp-content/themes/ipcn-fse/` e `wp-content/mu-plugins/`. É o que o checkout leva.
- **O veredicto da Contratante é condição da publicação.** É falado, não gera e-mail, checklist assinada nem artefacto próprio; o registo da aprovação é o próprio deploy, feito depois dele. A checklist do que percorrer está em [`docs/aceite-stagingredesign.md`](./aceite-stagingredesign.md).

## O que este documento não faz

Não faz deploy, não purga cache e não escreve no servidor — descreve o procedimento e os seus parâmetros. A execução é de quem faz o deploy. O deploy e a purga são acção do dono no ambiente de revisão.
