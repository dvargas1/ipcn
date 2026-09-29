---
title: 'Story 1.8 — Menu de primeiro nível curto e rodapé completo'
type: 'feature'
created: '2026-09-29'
status: 'done'
review_loop_iteration: 0
followup_review_recommended: false
baseline_commit: 'f9f633cbc65fd58ef54532c06727a693fb6bf6d8'
context:
  - '_bmad-output/implementation-artifacts/epic-1-context.md'
warnings:
  - multiple-goals
  - oversized
deferred:
  - summary: >-
      O primeiro nível do menu (Home · Notícias · Acervo · Agenda) não se corrige no tema: os itens vivem no navigation post `5358` da base de dados, e hoje não renderizam nada.
    evidence: >-
      `parts/header.html:13` é um `wp:navigation {"ref":5358}`; a AD-7 (`ARCHITECTURE-SPINE.md:107`) declara a referência da navegação conteúdo da BD que "se muda na base de dados, não no tema", e o `EXPERIENCE.md:23` diz o mesmo ao implementador. Recolha anónima em `stagingredesign` (2026-09-28): `/wp-json/wp/v2/navigation/5358` devolve quatro itens planos — Quem Somos, Projetos, Editorial, Notícias — sem submenus, ou seja, nenhum dos quatro que o AC pede; e o HTML servido da home e de uma 404 gerada no momento não traz `<nav class="wp-block-navigation">` nenhum (o bloco é parseado — o CSS inline dele é enfileirado — e não produz markup; causa por confirmar no editor do site). É acção do dono, com a lista exacta do que falta escrever.
    location: >-
      base de dados do redesign (wp_navigation 5358), parts/header.html:13
    severity: medium
  - summary: >-
      O skip link "Pular para o conteúdo" — primeiro elemento focável, exigido pelo UX-DR20 — não existe em nenhum ficheiro do tema, e nenhuma história o reclamou.
    evidence: >-
      `epics.md:108` (UX-DR20), `EXPERIENCE.md:131` e `:140`, e o achado **F-17 (HIGH)** de `review-accessibility.md:147-151`, que pede o link, um landmark `main` com `id` e um `h1` por página. O `EXPERIENCE.md:83` dá ao `header` o papel de primeiro destino do skip link. Nenhum dos quatro AC da 1.8 o nomeia, pelo que implementá-lo aqui seria âmbito inventado; quem fechar as superfícies (1.9 ou 1.10) deve tomá-lo.
    location: >-
      wp-content/themes/ipcn-fse/parts/header.html
    severity: medium
  - summary: >-
      As pílulas do cabeçalho deviam manter-se visíveis ao rolar (`EXPERIENCE.md:88`) e o cabeçalho não é fixo nem pegajoso em ficheiro nenhum.
    evidence: >-
      `style.css` não tem um único `position: sticky` (as duas únicas `position: fixed`, linhas 739 e 795, são a barra e o painel de cookies), e o `parts/header.html` não traz estilo de posição. Não está em nenhum AC da 1.8; a decisão de o fazer (e o seu efeito no reflow a 320px) pertence à 1.9.
    location: >-
      wp-content/themes/ipcn-fse/parts/header.html, style.css
    severity: low
  - summary: >-
      Os `href` do rodapé estão sem barra final, um `data-id` aponta para outra página, um link é `data-type="custom"` entre irmãos `data-type="page"` e outro não tem `data-id` nenhum — a passagem de identidade da AD-7 continua por fazer no rodapé.
    evidence: >-
      `parts/footer.html:11` — `<a href="/associe-se" data-type="page" data-id="3902">`, e na BD a página `3902` é `grupo-de-associados-do-ipcn`, não `associe-se` (2188); `data-id="4387"` ao lado de `/projetos` coincide com a página certa, `data-type="custom"` em `/apoia-se` destoa dos irmãos `page` e `/noticias` não tem `data-id`. O achado **F13** de `review-adversarial.md:40,262` nomeia o par de identidades. A AD-7 comprometida (`ARCHITECTURE-SPINE.md:107`) mantém os ids de páginas do rodapé como conteúdo da BD, pelo que esta história **não** lhes toca: acrescenta as ligações novas com o endereço canónico e deixa a normalização dos existentes registada. O custo de um `href` sem barra (um 301 por clique) é inferência ainda não medida por HTTP.
    location: >-
      wp-content/themes/ipcn-fse/parts/footer.html:11
    severity: low
  - summary: >-
      As três ligações novas do rodapé não são observadas por verificação nenhuma do repositório: o `scripts/check-php.sh` constrói os alvos com `find -name '*.php'` e nunca lê um `.html`.
    evidence: >-
      É a mesma lacuna de infraestrutura já diferida pela 1.3 (slug do cartão), 1.5 (`templates/front-page.html`), 1.6 (slugs dos patterns) e 1.7 (guarda do filtro): o NFR9 declara «sem build step, testes ou CI» e a verificação sancionada pela AD-13 é o `check-php.sh` mais o browser. Fecha-se estendendo o verificador para resolver os `href`/slugs do markup, com casos no `check-php.test.sh`.
    location: >-
      scripts/check-php.sh, wp-content/themes/ipcn-fse/parts/footer.html
    severity: low
  - summary: >-
      A passagem no browser que fecha os AC fica pendente de deploy e purga em `stagingredesign`, que são acção do dono.
    evidence: >-
      O ambiente não tem WordPress local e as preferências do `AGENTS.md` declaram o deploy paragem. Verificado por HTTP em 2026-09-28: `/quem-somos/`, `/fale-conosco/` e `/politica-de-privacidade/` respondem 200 e a política tem texto, mas o HTML em revisão ainda é anterior às histórias 1.5/1.6/1.7. A lista do que verificar fica na secção `## Verification`.
    location: >-
      stagingredesign.ipcnbrasil.org (qualquer página), após deploy e purga
    severity: low
  - summary: >-
      O bloco do rodapé do `style.css` guarda regras sem markup correspondente (marca em linha e imagem do rodapé), que o mapa de código desta história identificou e não arrumou.
    evidence: >-
      Achado 19 da revisão cega. O `parts/footer.html` não tem `.ipcn-footer-brand`, nem `img.ipcn-footer-mark`, nem `footer .wp-block-image`, nem `footer .wp-block-list`; as regras de `style.css:198-202` e `:217-235` são vestigiais desde que a marca do rodapé saiu no Gate 4. Não é código morto por inteiro — o rodapé é um template part editável e as duas últimas regras podem servir conteúdo que um editor lá acrescente. Fecha-se numa arrumação do `style.css` com essa decisão tomada.
    location: >-
      wp-content/themes/ipcn-fse/style.css, parts/footer.html
    severity: low
  - summary: >-
      Nenhum link do rodapé se distingue do texto que o rodeia: o `text-decoration: none` de `.ipcn-footer a` vence o sublinhado da regra do navy, e a cor das ligações é a mesma do texto simples.
    evidence: >-
      Achado 1 da camada de casos-limite. `style.css:236-238` e `:154-157` têm a mesma especificidade 0,1,1 e a primeira vem depois, pelo que o sublinhado das colunas não pinta; as ligações da coluna Contato e as linhas de morada partilham o mesmo `rgba(255,255,255,0.85)`, logo só o hover (ocre) e o cursor os distinguem — contra `EXPERIENCE.md:85` e o FR-12. É pré-existente (o rodapé do Gate 4 é este) e a história só lhe acrescentou instâncias, pelo que o remédio é uma decisão sobre a affordance de todo o rodapé, não uma linha na 1.8.
    location: >-
      wp-content/themes/ipcn-fse/style.css:154-157,236-241, parts/footer.html
    severity: low
---

<intent-contract>

## Intent

**Problem:** O primeiro nível do cabeçalho devia ser `Home · Notícias · Acervo · Agenda` mais as pílulas `Associe-se` e `Apoia-se` (`EXPERIENCE.md:45`, PRD §12), e não é: os quatro itens que o navigation post da base de dados guarda são `Quem Somos · Projetos · Editorial · Notícias`, e o bloco não produz markup nenhum no frontend. O rodapé, que é por onde se alcançam as Páginas institucionais (`prd.md:367`), não traz a Política de privacidade (`FR-11`, `EXPERIENCE.md:61`), não tem ligação directa a Fale conosco, e a linha onde Quem somos devia estar é texto morto ("Sobre o instituto").

**Approach:** Fecha-se no tema a metade que é do tema — o rodapé: três ligações institucionais acrescentadas ou convertidas em `parts/footer.html`, com endereço por slug, e a regra mínima em `style.css` que faz a ligação legal distinguir-se do texto que a rodeia (`FR-12`). A metade do menu **não** é do tema: a referência é conteúdo da base de dados (AD-7, `EXPERIENCE.md:23`), pelo que a lista de itens que falta escrever fica registada como acção do dono, com a prova recolhida no ambiente.

## Boundaries & Constraints

**Always:** ligações por endereço portátil (`/quem-somos/`, `/fale-conosco/`, `/politica-de-privacidade/`) escrito à mão, sem `data-id` — o endereço canónico com barra final, como o `home_url()` do resto do tema; as três colunas e a linha final mantêm-se, com o `DESIGN.md` `footer` (label `rgba(255,255,255,0.5)`, corpo `rgba(255,255,255,0.85)`, `paddingBlock` 56px/24px) e o piso de contraste 4.74:1/11.4:1; a ligação nova distingue-se do texto vizinho por mais do que cor (`FR-12`); o rodapé continua presente em todas as páginas (os 9 templates invocam o part); o anel de foco da 1.4 é o único regime de foco.

**Never:** tocar na base de dados (o navigation post `5358`) nem em `parts/header.html` — a AD-7 diz que a referência da navegação é conteúdo da BD e muda-se lá; um `WP_Query`, um shortcode novo ou markup de cartão; `theme.json` e os tokens; os 44px de alvo de toque do rodapé (são AC da 1.9) e o cabeçalho pegajoso; a acentuação global (1.11); o skip link e o `main` com `id` (ver diferido); `inc/`, `templates/`, `patterns/` e o mu-plugin.

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| Salto para institucional | rodapé, página qualquer, sem sessão | `Quem somos`, `Projetos`, `Fale conosco` e `Política de privacidade` presentes, cada uma com `href` por slug | — |
| Página institucional aberta | `/quem-somos/`, `/fale-conosco/`, `/politica-de-privacidade/` | 200 (verificado por HTTP em 2026-09-28) e sem login | 404 se o slug mudar na BD: a rota é conteúdo |
| Ligação legal junto do copyright | linha final do rodapé, ao lado de texto simples `rgba(255,255,255,0.5)` | `text-decoration: underline`, hover ocre como as restantes; o texto © fica sem sublinhado | — |
| Teclado | `Tab` até à ligação nova | anel duplo `base`+`navy` da 1.4, 2px de afastamento | — |
| Colunas do rodapé | 781px ou menos | continuam a empilhar como hoje; a linha final quebra por `flex-wrap` | — |

</intent-contract>

## Code Map

- `wp-content/themes/ipcn-fse/parts/footer.html` — **onde tudo entra.** l.11 a coluna Instituto (o texto morto `Sobre o instituto` precede as ligações `Associe-se`, `Apoie o IPCN`, `Projetos`, `Notícias`); l.21 a coluna Contato (e-mail, telefone, morada, na linha a seguir ao rótulo da l.18); l.40-47 o grupo da linha final, um `wp-block-group is-layout-flex` **sem classe**, com `justify-content:space-between;flex-wrap:wrap;gap:8px`, que já acomoda um terceiro filho — é preciso um hook para o CSS.
- `wp-content/themes/ipcn-fse/style.css` — l.236-241 `.ipcn-footer a`/`:hover`: o `text-decoration: none` da l.237 vence o sublinhado de `.has-navy-background-color a` (l.154-157), mesma especificidade 0,1,1 mas mais tarde — é a razão pela qual uma ligação na linha final ficaria idêntica ao texto ©. l.898-905 o anel de foco global (não tocar). l.217-235 regras vestigiais do rodapé sem markup correspondente.
- `wp-content/themes/ipcn-fse/parts/header.html` — l.13 o `wp:navigation {"ref":5358,…}` (BD) e l.16-21 as pílulas `associe`/`apoie`, que já cumprem a parte das pílulas do AC. **Fora do diff.**
- `wp-content/themes/ipcn-fse/theme.json` — l.8,11,12 tokens `navy`/`ocre`/`base`; l.45-58 `elements.link.color` = navy, razão pela qual uma ligação nua dentro do rodapé só é legível pela regra do fundo navy; l.64 o part `footer`.
- Prova recolhida no ambiente (REST anónimo e HTTP, 2026-09-28): `navigation/5358` = 4 itens planos, sem submenus; `pages` = 44 páginas, com `quem-somos` (2045), `fale-conosco` (96), `politica-de-privacidade` (3), `projetos` (4387), `noticias` (2617); nenhuma página servida traz markup de navegação.
- `wp-content/themes/ipcn-fse/patterns/ipcn-seccoes.php:32-50` — as 7 Secções por slug, dentro do hub `templates/page-noticias.html:19`: o AC "as Secções alcançam-se a partir de Notícias" já está cumprido pela 1.6 e não se toca.
- `scripts/check-php.sh` — a única verificação executável; não lê `.html`; conta 17 problemas aceites sob `inc/`.

## Tasks & Acceptance

**Execution:**
- `wp-content/themes/ipcn-fse/parts/footer.html` — na coluna Instituto, trocar o texto morto `Sobre o instituto` pela ligação `Quem somos` → `/quem-somos/`, mantendo as restantes linhas — o PRD §12 dá o rodapé como entrada das institucionais e a página existe (2045).
- `wp-content/themes/ipcn-fse/parts/footer.html` — na coluna Contato, acrescentar `Fale conosco` → `/fale-conosco/` como primeira linha, antes do e-mail — o `EXPERIENCE.md:61` nomeia a ausência e a coluna é a casa do contacto.
- `wp-content/themes/ipcn-fse/parts/footer.html` — no grupo da linha final, acrescentar `Política de privacidade` → `/politica-de-privacidade/` e dar ao grupo a classe `ipcn-footer-base` — o `FR-11` torna a página obrigatória no rodapé e a linha final dá-lhe a posição estável que o `EXPERIENCE.md:84` pede.
- `wp-content/themes/ipcn-fse/style.css` — acrescentar `.ipcn-footer-base a { text-decoration: underline }` junto do bloco do rodapé (l.236-241) — sem ele a ligação legal fica visualmente igual ao texto ©, contra o `FR-12`.

**Acceptance Criteria:**
- Given o site aberto sem sessão, when abro qualquer página, then o rodapé traz Quem somos, Projetos, Fale conosco e Política de privacidade, cada uma com `href` por slug portátil, e o contacto (e-mail e telefone) continua alcançável.
- Given `/politica-de-privacidade/`, when a sigo a partir do rodapé sem sessão, then abre o texto da política; e `/quem-somos/` e `/fale-conosco/` respondem 200.
- Given o rodapé no browser, when comparo a ligação legal com o texto © ao lado, then distingo-as sem me fiar só da cor (a ligação está sublinhada).
- Given o teclado, when tabulo até à ligação legal, then o foco é o anel duplo da 1.4 e não há `outline: none` novo.
- Given `bash scripts/check-php.sh`, when corro, then a sintaxe sai limpa e os 17 problemas aceites não crescem.

## Spec Change Log

Sem alterações ao contrato: o AC do primeiro nível não é alcançável a partir do repositório (é base de dados, por AD-7) e o AC das Secções já estava cumprido pela 1.6. As decisões abaixo são do implementador e ficam registadas para poderem ser contestadas.

- **Onde vai cada ligação.** `Fale conosco` na coluna **Contato** (é o assunto da coluna; o `EXPERIENCE.md:39` só diz "Home, rodapé"); `Política de privacidade` na **linha final** (o `epic-1-context.md:57` fala da "linha final e a ligação à Política de privacidade", e dá-lhe posição estável); `Quem somos` passa a **ligação** onde hoje está o texto morto, com o rótulo da tabela de IA do `EXPERIENCE.md:37`.
- **O sublinhado é escopado.** Só a linha final o ganha (classe `ipcn-footer-base`); as ligações das colunas mantêm o aspecto actual, que é decisão de desenho anterior a esta história e não é do seu contrato.
- **Os `href` existentes não se normalizam.** Ficam sem barra final e com os `data-id` actuais, porque a AD-7 comprometida mantém os ids de páginas do rodapé como conteúdo da BD; a normalização vai para o diferido em vez de entrar no diff.
- **`multiple-goals`.** A história junta dois objectivos independentemente entregáveis — retocar os itens do menu (base de dados) e completar o rodapé (tema) — e só o segundo é deste repositório.

## Review Triage Log

### 2026-09-29 — Review pass

- verdicts: 35 findings — high 0, medium 3, low 18, false 14, maybe-false 0
- findings:
  - `[false]` `[reject]` **BH1** o registo de triagem está vazio — o `spec-template.md` deste render manda deixá-lo vazio até à primeira passagem e é esta passagem que o escreve.
  - `[low]` `[reject]` **BH2** `oversized` declarado sem remediação — o aviso é mecânico (o template manda acrescentá-lo acima de 1600 tokens e continuar; a spec mede ~3,2k); o remédio é texto da spec.
  - `[medium]` `[defer]` **BH3** o título promete "menu curto" e o diff não toca em `parts/header.html` — agrupado com BH5 e IA1; a metade do menu é conteúdo da base de dados (AD-7, `EXPERIENCE.md:23`) e o próprio AC a pressupõe ("**Dado** o menu actual, guardado na base de dados"), logo o remédio é do dono e ficou registado com a lista exacta dos itens.
  - `[low]` `[reject]` **BH4** o Change Log e o Design Notes discutem AC que não estão na lista de AC — o AC do menu está no diferido e o das Secções só se cita; acrescentá-los à lista é editar a spec.
  - `[medium]` `[defer]` **BH5** a matriz não tem linha para o estado do menu — mesma causa de BH3, mesma rota.
  - `[low]` `[reject]` **BH6** `git diff --stat` espera dois ficheiros e imprime três — verdadeiro: a spec vem em `add -N` a somar aos dois ficheiros do tema; a expectativa refere-se à alteração do tema e o resultado real fica no `## Auto Run Result`.
  - `[low]` `[defer]` **BH7** nenhum comando exercita o comportamento novo — agrupado com VG1 e VG2.
  - `[false]` `[reject]` **BH8** "a sintaxe sai limpa" contra "saída 1" — refutado: "sintaxe limpa" é a ausência de entradas `sintaxe:`, que o verificador separa dos 17 `block markup` aceites desde o baseline; a saída 1 é dos 17.
  - `[false]` `[reject]` **BH9** provas externas sem trecho citado e ids inconsistentes — os ids (`F13`, `F-17`) vêm verbatim dos dois documentos citados e as referências são `ficheiro:linha`, a convenção da casa.
  - `[false]` `[reject]` **BH10** o `context:` nomeia um ficheiro e o corpo apoia-se em nove — o template manda manter o `context:` curto, só com o que não está destilado no corpo, e os restantes estão referenciados no próprio corpo.
  - `[low]` `[patch]` **BH11** os diferidos não têm dono nem nota no ledger das histórias seguintes — o campo dono não existe na forma do template; a nota em falta é real e foi aplicada (oito entradas acrescentadas ao `deferred-work.md`).
  - `[false]` `[reject]` **BH12** a barra final contradiz os href vizinhos — o `Always` justifica-a pelo `home_url()` do tema, que de facto a usa (`inc/forms.php:19,58`, `inc/cookie-bar.php:24`); a ausência de barra nos vizinhos é a dívida que o diferido registou.
  - `[low]` `[defer]` **BH13** o 301 por clique não foi medido — agrupado com BH14 e IA2; o texto do diferido passou a dizer que é inferência por medir.
  - `[low]` `[defer]` **BH14** a auditoria dos `href` esquece duas anomalias (`data-type="custom"` em `/apoia-se`, `/noticias` sem `data-id`) — agrupado com BH13 e IA2; o resumo do item passou a nomear as quatro anomalias do parágrafo.
  - `[false]` `[reject]` **BH15** o inventário de páginas omite `associe-se` e `apoia-se` — o inventário do mapa de código é o das páginas que a alteração passa a tratar; as duas omissas estão citadas no diferido dos `href`.
  - `[false]` `[reject]` **BH16** o hook `ipcn-footer-base` não está no `Always`/`Never` — os limites não são um registo exaustivo de identificadores; o hook resulta da regra do `Always` sobre distinção e está nomeado nas tarefas e no mapa de código.
  - `[false]` `[reject]` **BH17** `.ipcn-footer-base a` é a única regra do rodapé sem o prefixo `.ipcn-footer` e sem `!important` — a classe só existe dentro do rodapé (`parts/footer.html:40`, conferido por busca no repositório) e nenhum estilo inline concorre com `text-decoration`, ao contrário da cor do hover.
  - `[low]` `[reject]` **BH18** o `h1` por página caiu do `Never` — já está registado na evidência do diferido do skip link, que é a mesma decisão de âmbito.
  - `[low]` `[defer]` **BH19** as regras vestigiais do rodapé não têm destino — verdadeiro: entra no diferido como arrumação do `style.css`, com a decisão de saber se o rodapé editável ainda as pode usar.
  - `[false]` `[reject]` **BH20** a linha final com três filhos pode quebrar fora da ordem a 320px — refutado: `flex-wrap` preserva a ordem da fonte e nenhuma regra reordena os filhos; o defeito real da linha final é o de EC2, registado à parte.
  - `[low]` `[reject]` **BH21** o triplete inline de cor e tamanho repete-se em vez de reusar o hook — é o padrão do próprio ficheiro (os dois parágrafos irmãos fazem o mesmo); trocar por classe obrigaria a mexer em linhas não tocadas, mais do que a correcção directa.
  - `[false]` `[reject]` **BH22** razões de contraste sem par nem norma — são as do componente `footer` do `DESIGN.md` (4.74:1 rótulos, 11.4:1 texto e ligações, citadas como tal) e a cor da ligação nova é o par navy/base que o mesmo documento declara a 15.6:1.
  - `[false]` `[reject]` **BH23** a string removida não fica registada — a tarefa do `parts/footer.html` regista a substituição do texto morto e o `## Auto Run Result` volta a registá-la.
  - `[false]` `[reject]` **BH24** estado de revisão sem justificação — o registo fica vazio até esta passagem, que o escreve; a forma do template não tem campo de data de revisão.
  - `[low]` `[reject]` **BH25** "os 9 templates" sem os enumerar — a contagem não gera tarefa e a camada de lacunas enumerou os nove e confirma-os.
  - `[false]` `[reject]` **BH26** provas de 2026-09-28 num documento de 2026-09-29 — a recolha no ambiente foi feita a 2026-09-28 e a spec diz a data; não há requisito de prova do mesmo dia.
  - `[low]` `[defer]` **EC1** as ligações novas das colunas ficam fora do `ipcn-footer-base` e continuam sem sublinhado — verdadeiro e mais fundo do que o achado diz: a cor das ligações da coluna Contato é a mesma do texto simples (`rgba(255,255,255,0.85)`), logo só o hover as distingue. É o regime pré-existente do rodapé do Gate 4, que a história deliberadamente não reviu; entrou no diferido.
  - `[low]` `[patch]` **EC2** o parágrafo do meio encolhe ao texto e o `align:center` não tem efeito — corrigido com `flex: 1 1 auto` no item do meio (uma declaração), para a classe que a alteração emite ser verdadeira.
  - `[low]` `[defer]` **VG1** as três ligações novas não são observadas por verificador nenhum — pré-verificado pela camada, agrupado com BH7 e VG2; é a lacuna de infraestrutura já diferida pela 1.3/1.5/1.6/1.7.
  - `[low]` `[defer]` **VG2** o casamento entre a classe `ipcn-footer-base` e a regra que a veste não é observado — mesma causa, mesma rota; demonstrável por mutação (renomear a classe deixa tudo verde).
  - `[low]` `[reject]` **VG3** (*outro*) a expectativa do `git diff --stat` não bate com a impressão — mesma causa de BH6.
  - `[medium]` `[defer]` **IA1** o diff não entrega a metade titular da intenção (o menu) e as superfícies divergem — verdadeiro e é o achado mais importante da passagem. Registado com a rota `defer` e não `intent_gap`: o resultado mau é real, mas não é produzido por esta alteração (os itens errados já estavam na base de dados) e o remédio não se decide no repositório — vem da AD-7 comprometida, do `EXPERIENCE.md:23` e da própria premissa do AC, que localiza os itens na base de dados. Fica no diferido com a lista do que falta escrever; se o dono entender que o tema devia passar a escrever o primeiro nível, isso é decisão de arquitectura contra a AD-7, não um defeito do diff.
  - `[low]` `[defer]` **IA2** o diff entrega um regime de ligações misto (canónico no novo, sem barra e com `data-id` obsoleto no antigo) — agrupado com BH13 e BH14: é dívida pré-existente na mesma linha, mantida por decisão registada no Change Log.
  - `[low]` `[patch]` **IA3** o `deferred-work.md` fica fora do diff, logo o handoff não chega ao ledger — agrupado com BH11 e aplicado.
  - `[false]` `[reject]` **IA4** as ligações novas excedem o texto do AC (que só nomeia a política) — o AC diz "as páginas institucionais a partir do rodapé" e o PRD §12 define-as como Quem somos, Projetos, Fale conosco e Política de privacidade; o `EXPERIENCE.md:61` nomeia explicitamente a ausência de Fale conosco. Sem as três, o AC não se cumpre.

**Encaminhamento.** Agrupados por causa comum e rota: **defer** — A (BH3, BH5, IA1: a metade do menu, do dono), B (BH7, VG1, VG2: verificação durável do markup e do CSS), C (BH13, BH14, IA2: a dívida de identidade dos `href` do rodapé, com o item do diferido reescrito), D (BH19: regras vestigiais) e E (EC1: a affordance das ligações do rodapé). **patch** — F (BH11, IA3: oito entradas no `deferred-work.md`) e G (EC2: `flex: 1 1 auto`). Os restantes 14 rejeitados por refutação ou por terem a spec por remédio, com a razão em cada linha. Sem `intent_gap` nem `bad_spec`: sem loopback.

## Design Notes

**Porquê o menu não entra no diff.** O AC do primeiro nível pede quatro itens que existem na base de dados, e a AD-7 (`ARCHITECTURE-SPINE.md:107`) é explícita: "a referência da navegação no cabeçalho e os ids de páginas nos links do rodapé … não se convertem em slug, e mudam-se na base de dados, não no tema". Escrever `Home · Notícias · Acervo · Agenda` em `parts/header.html` faria o tema discordar da BD e reapareceria na primeira edição do menu; trocar o menu para `core/page-list` mudaria o desenho. O que o dono precisa de saber está no diferido, com os quatro itens actuais e o defeito observado.

**Porquê um hook novo em vez de sublinhar tudo.** O `text-decoration: none` de `.ipcn-footer a` é o que faz o rodapé parecer uma lista de texto e não um bloco de ligações; alargar o sublinhado a todas mudaria três colunas para corrigir uma linha. A classe `ipcn-footer-base` segue a convenção do tema (hooks `ipcn-*` para o CSS vestir markup de bloco) e mantém a regra do hover do rodapé, que já pinta a ocre.

**Porquê o AC das Secções não gera tarefa.** O hub `templates/page-noticias.html:19` invoca o pattern curado e as 7 Secções saem por slug (`patterns/ipcn-seccoes.php:32-50`); confirmado por leitura em 2026-09-28. A 1.8 verifica, não reimplementa.

**O que a história deixa por explicar.** O menu não renderiza markup nenhum no frontend, e a causa não se confirma a partir daqui: o `content.rendered` do post de menu mistura as classes do bloco (`wp-block-navigation-item`) com as do menu clássico (`menu-item menu-item-type-post_type`). Precisa do editor do site ou de leitura directa do `post_content` na BD, e é o dono que a faz ao ajustar os itens.

## Verification

**Commands:**
- `bash scripts/check-php.sh` — esperado: os mesmos 17 problemas aceites (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1, nenhuma entrada `sintaxe:`.
- `git diff --stat` — esperado: dois ficheiros, `parts/footer.html` e `style.css`.
- `git diff -- wp-content/themes/ipcn-fse/parts/footer.html` — esperado: apenas linhas da coluna Instituto, da coluna Contato e da linha final; `data-id` nenhum acrescentado.

**Manual checks (if no CLI):**
- Sem WordPress local e com o deploy na mão do dono, o acabamento confirma-se no browser em `stagingredesign` após deploy e purga (`?nocache=1`): numa página qualquer, as três ligações novas no rodapé, a legal sublinhada e o foco ao tabular; e `/quem-somos/`, `/fale-conosco/` e `/politica-de-privacidade/` a abrir sem sessão.
- O primeiro nível do cabeçalho continua sem markup: verificar depois do dono ajustar os itens na base de dados, não antes.

## Auto Run Result

**O que mudou.** O rodapé passou a completar a navegação que o épico lhe entrega: "Sobre o instituto" — texto morto desde o Gate 4 — é agora a ligação `Quem somos` para `/quem-somos/`; a coluna Contato ganhou `Fale conosco` para `/fale-conosco/` antes do e-mail; e a linha final ganhou `Política de privacidade` para `/politica-de-privacidade/` entre o copyright e o crédito, com o sublinhado que a distingue do texto © ao lado (o `text-decoration: none` de `.ipcn-footer a` vencia o sublinhado da regra do navy). As três ligações saem por slug portátil, com barra final canónica e sem `data-id`, como o resto do tema. A metade do menu **não** entrou no diff: os itens vivem no navigation post `5358` da base de dados, e a AD-7 com o `EXPERIENCE.md:23` dizem que se editam lá — fica registado como acção do dono.

**Ficheiros alterados** (o `git diff --stat` desde a base `f9f633c` imprime quatro: os dois do tema, esta spec e o ledger):
- `wp-content/themes/ipcn-fse/parts/footer.html` — as três ligações (coluna Instituto, coluna Contato, linha final) e a classe-hook `ipcn-footer-base` no grupo da linha final.
- `wp-content/themes/ipcn-fse/style.css` — `.ipcn-footer-base a { text-decoration: underline }` e o `flex: 1 1 auto` que dá efeito ao `align:center` do item do meio.
- `_bmad-output/implementation-artifacts/spec-1-8-menu-de-primeiro-nivel-curto-e-rodape-completo.md` — esta spec.
- `_bmad-output/implementation-artifacts/deferred-work.md` — oito entradas novas da 1.8 (patch da revisão).

**Triagem da revisão.** 35 achados de quatro camadas — `high` 0, `medium` 3, `low` 18, `false` 14 — sem `intent_gap` nem `bad_spec`, logo sem loopback. **2 entradas corrigidas por patch, ambas `low`:** o `deferred-work.md` recebeu as oito entradas (BH11 + IA3 — o handoff não chegava ao ledger) e o item do meio da linha final ganhou `flex: 1 1 auto` (EC2 — a classe `has-text-align-center` que a alteração emite não tinha efeito nenhum). **5 entradas diferidas, 10 achados:** a metade do menu do dono (BH3, BH5, IA1 — `medium`), a verificação durável do markup e do CSS (BH7, VG1, VG2), a dívida de identidade dos `href` do rodapé com o item reescrito para nomear as quatro anomalias (BH13, BH14, IA2), as regras vestigiais do `style.css` (BH19) e a affordance das ligações do rodapé (EC1). **21 rejeitados:** 14 por refutação — todos os `false`, BH1, BH8, BH9, BH10, BH12, BH15, BH16, BH17, BH20, BH22, BH23, BH24, BH26 e IA4 — e 7 `low` cujo remédio era texto da spec ou mexer em linhas não tocadas (BH2, BH4, BH6 com VG3, BH18, BH21, BH25). Duas notas de honestidade: BH6/VG3 (a expectativa "dois ficheiros" do `git diff --stat` não bate com a impressão, que traz quatro) e BH13 (o 301 por clique é inferência, não medição) ficam registados aqui porque o remédio era editar a spec e o achado é verdadeiro.

**Recomendação de passagem seguinte:** `followup_review_recommended: false` — duas entradas corrigidas por patch, ambas `low`, nenhuma `high` e menos de duas `medium`.

**Verificação.** `bash scripts/check-php.sh` → 17 problemas, todos `block markup` (12 em `inc/agenda-block.php`, 5 em `inc/listings.php`), saída 1 e 0 entradas `sintaxe:` — idêntico ao baseline; `bash scripts/check-php.test.sh` → 14/14; `style.css` com 183 `{` e 183 `}`; frontmatter da spec lido como YAML (`uv run --with pyyaml`) com um só campo `deferred` de oito itens. Harness de sessão (fora do deliverable, como os das 1.6 e 1.7) em 19/19, cobrindo as cinco linhas da matriz: as quatro institucionais e o contacto no ficheiro, o sublinhado e a sua ordem contra `.ipcn-footer a`, o crescimento do item do meio, o anel de foco da 1.4 intacto sem `outline: none` novo e o empilhamento a ≤781px. As três páginas institucionais foram confirmadas por HTTP em 2026-09-28 (`/quem-somos/`, `/fale-conosco/`, `/politica-de-privacidade/` a 200, sem sessão, a política com texto). Sem WordPress local, a passagem no browser fica pendente de deploy e purga — o HTML servido ainda mostra "Sobre o instituto" e nenhuma ligação à política, ou seja, anterior a esta história.

**Riscos residuais.** (1) O AC do primeiro nível continua por cumprir e não é deste repositório: enquanto o dono não ajustar os itens na base de dados, o cabeçalho não mostra `Home · Notícias · Acervo · Agenda` e o bloco continua sem produzir markup — a causa de não renderizar precisa do editor do site. (2) Nada no repositório observa as ligações novas nem o casamento entre a classe e a regra: uma mutação deixa tudo verde. (3) As ligações do rodapé continuam sem se distinguir do texto que as rodeia, agora com três instâncias a mais. (4) O sublinhado assenta em ordem de ficheiro — se alguém mover `.ipcn-footer-base a` para cima de `.ipcn-footer a`, a correcção desaparece em silêncio.
