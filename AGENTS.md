<!-- bmad:context -->
<!-- Verified 2026-09-29 against d44c485. Managed by bmad-project-context; edits inside this block are replaced on refresh. Keep anything you want preserved outside the markers. -->

## ipcn

Redesign do site do Instituto de Pesquisas das Culturas Negras. WordPress FSE, tema próprio `ipcn-fse` (PHP 8.1, WP >= 6.4). Trabalho activo: https://stagingredesign.ipcnbrasil.org. Planeamento em `docs/plano-tema-custom-e-portal-associados.md`; quando README e STATUS discordam, README manda. BMAD gere o contexto deste repo (`_bmad/`, skills em `.agents/skills/`).

## Policy

- Nunca fazer deploy nem editar https://ipcnbrasil.org — produção é manual pelo Daniel no hPanel.
- Nunca editar o staging Divi https://staging.ipcnbrasil.org — congelado. `ROADMAP.md`, `STATUS.md` e `docs/fase*` são histórico dessa linha, não a superfície a alterar.
- Nunca commitar `wp-config.php`, `.env`, `*.key`, `*.pem`, `wp-content/uploads/` ou backups. Não pedir nem guardar passwords de WP, DB, SSH ou FTP.
- Não versionar Divi nem plugins de terceiros. O checkout só leva `wp-content/themes/ipcn-fse/` e `wp-content/mu-plugins/`.
- Não alterar `wp-content/mu-plugins/ipcn-optimizations.php` (fixes Divi) salvo pedido explícito. Não fazer deploy desse ficheiro no staging Divi.
- Depois de alterar o tema: deploy para stagingredesign (SSH `ipcn`) e commit + push na mesma passagem.

## Where things are

- Home: `wp-content/themes/ipcn-fse/templates/front-page.html`. Single post: `templates/single.html`. Acervo: `templates/archive-acervo_ipcn.html`, `templates/single-acervo_ipcn.html`. Não existe template de taxonomia — `/temas/<slug>` cai no template do archive.
- Header e footer: `parts/header.html`, `parts/footer.html`. O menu é o navigation post `ref:5358` na BD do redesign — editar o HTML não muda os itens. `5358` no staging Divi é outro objecto.
- CPT, taxonomia, forms, agenda e cookie bar: `inc/` (`content-model.php`, `forms.php`, `agenda-block.php`, `cookie-bar.php`). `functions.php` é só o carregador que inclui `inc/` por ordem explícita. Tokens: `theme.json`. Overrides que vencem inline styles: `style.css`.
- From dos e-mails: `wp-content/mu-plugins/ipcn-mail-from.php`. Sem este ficheiro no servidor, o Gmail rejeita os forms.

## Running and verifying

- Não há test runner nem CI. Antes de mexer no tema, correr `bash scripts/check-php.sh` — verifica a sintaxe do PHP do tema `ipcn-fse` e dos mu-plugins e falha com block markup (`<!-- wp:`) ou `<style>` emitido de PHP sob `inc/`; com caminhos só verifica esses ficheiros. Verificar também no browser em stagingredesign depois do purge (`?nocache=1`).
- Deploy do tema para stagingredesign: `bash scripts/deploy-staging.sh` (tar + scp → `--strip-components=3` no tema remoto → `wp litespeed-purge all`). Não existe binário `litespeed-purge` no servidor. Sem purge o HCDN serve HTML velho — não tratar HTML sem purge como verdade.
- Tema exige PHP >= 8.1 e WordPress >= 6.4 (`style.css`). Não assumir a versão do host a partir do ROADMAP.

## Conventions that differ from defaults

- Block theme não carrega `style.css` sozinho — já está enqueued em `inc/setup.php`. Não remover esse enqueue.
- Fontes vêm do Google via `inc/setup.php`. Declarar família só no `theme.json` deixa o site no fallback.
- Usar slugs de `theme.json` (navy, ocre, ink, muted, subtle, body, oswald, serif). Terracota `#a85a32` e chumbo `#2d2418` não são tokens — não os inventar como slugs.
- Ocre `#c9a86a` é fundo de botão com texto chumbo, nunca texto claro sobre ocre.
- Queries filtram por slug de categoria (`categoryName` na home, `[ipcn_query_posts category="…"]` nas páginas internas). Nunca usar term_id — muda entre ambientes.
- Acervo é o CPT `acervo_ipcn`, separado de `post`. Rewrite da taxonomia `tema_acervo` fica em `/temas/`, nunca aninhado em `/acervo/`.
- Agenda não é CPT: posts da categoria `agenda-ipcn`, meta `data_evento`, shortcode `[ipcn_home_agenda]`.
- Forms não levam nonce (LiteSpeed serve HTML velho). Protecção é honeypot `ipcn_hp` + validação + referer. Destino: `contato@ipcnbrasil.org`.
- Se mudares padding inline da home, actualiza o selector `[style*="padding-top:…"]` em `style.css` — o inline vence media queries normais.
- Imagem do hero vem de `assets/hero-bg.jpg` via `--ipcn-hero-bg` em `inc/setup.php`. Não reintroduzir URL absoluto de ambiente.
- Portal de associados e paywall não existem. Não os tratar como código actual.
- O cartão vive em `patterns/` (`ipcn/card` 16:9 e `ipcn/card-feature` 4:3, ambos `Inserter: false`); as listagens usam-no dentro de `core/post-template` e o shortcode `ipcn_query_posts` entrega-o por `do_blocks()`. Nenhum markup de cartão é escrito à mão em PHP.

## Known pitfalls

- `capability_type => acervo` não tem roles neste tema. Não assumir que o paywall existe.
- CookieYes é opção na BD do servidor, não um ficheiro. A barra visível é `#ipcn-cookie-bar` no tema.

<!-- /bmad:context -->

## Preferências de trabalho (fora do bloco gerido — não é substituído num refresh)

- Nos fluxos BMAD (`bmad-build`, revisão), seguir sempre a opção **recomendada** e não parar para perguntar: resolver os checkpoints e as Open Questions por conta própria, escolhendo a recomendada, e registar a decisão na spec.
- Só parar quando a acção for irreversível ou destrutiva (apagar ficheiros ou dados, `push --force`, deploy, alterações na base de dados) ou quando nenhuma opção estiver recomendada.
- Ainda assim, mostrar as decisões tomadas por conta própria no resumo final, para poderem ser contestadas depois.
