# Checklist de aceite — ambiente de revisão (stagingredesign)

> O que a Contratante percorre antes de a produção mudar, e o que conta como bloqueio. O veredicto é falado, não deixa artefacto e é condição da publicação. Base: `_bmad-output/planning-artifacts/epics.md` (história 4.3) e `_bmad-output/implementation-artifacts/epic-4-context.md`.

---

## Como usar esta checklist

A revisão é feita no ambiente de revisão `https://stagingredesign.ipcnbrasil.org`, **nunca em produção**. Percorre-se cada superfície primeiro no **telemóvel** e depois no **computador**; só conta o que é visto depois da purga da cache, com `?nocache=1` no endereço.

O veredicto é falado: não gera e-mail, checklist assinada nem artefacto próprio. Esta checklist é o que se percorre, não um documento a preencher e entregar. A aprovação é condição da publicação em produção, que é manual, no hPanel, pelo Daniel.

**Pré-condições (acção do dono, antes de começar):** o tema entregue em `stagingredesign` (`bash scripts/deploy-staging.sh`) e a cache purgada (o passo 5 do procedimento — `wp --path=… litespeed-purge all`, no servidor, por `ssh`) — o procedimento está em [`docs/deploy-stagingredesign.md`](./deploy-stagingredesign.md). Sem a purga, o HTML servido pode ser velho e não conta como prova.

---

## 1. As seis superfícies

Percorrer no telemóvel (a revisão é confortável a 375px; piso de 320px sem scroll horizontal) e repetir no computador (1440px).

### Home

Servida por `wp-content/themes/ipcn-fse/templates/front-page.html` — `/`.

- [ ] Telemóvel: sem scroll horizontal a 320px; o hero, as últimas notícias, a vitrine do acervo, a agenda e o rodapé leem-se sem cortes.
- [ ] Computador: as mesmas secções, com as larguras e o espaçamento esperados.

### Uma Notícia

Servida por `wp-content/themes/ipcn-fse/templates/single.html`.

- [ ] Telemóvel: um só título de nível 1, medida de leitura confortável, imagens dentro da largura.
- [ ] Computador: a mesma leitura, com a coluna de texto centrada e sem saltos.

### A Agenda

Servida por `wp-content/themes/ipcn-fse/templates/page-agenda-ipcn.html` (`page-<slug>`) — `/agenda-ipcn/`.

- [ ] Telemóvel: os encontros por vir listados por data ascendente, cada um com a data visível; sem encontros, o estado vazio explica-se.
- [ ] Computador: a lista completa e o cabeçalho de página coerentes.

### O Acervo

Servido por `wp-content/themes/ipcn-fse/templates/archive-acervo_ipcn.html` — `/acervo/`.

- [ ] Telemóvel: a grelha de cartões com imagens; sem itens publicados, o estado vazio explica-se.
- [ ] Computador: a grelha e os filtros por tema funcionam.

### Associe-se

Servida por `wp-content/themes/ipcn-fse/templates/page.html` mais o conteúdo da base de dados — `/associe-se/`.

- [ ] Telemóvel: o formulário é utilizável; campos com etiqueta, alvos de toque confortáveis, sem overflow.
- [ ] Computador: o formulário submete e a mensagem de sucesso é clara.

### Apoia-se

Servida por `wp-content/themes/ipcn-fse/templates/page-apoia-se.html` — `/apoia-se/`.

- [ ] Telemóvel: a faixa e o estado do QR leem-se; o contacto e a chave PIX continuam alcançáveis.
- [ ] Computador: a mesma superfície, sem elementos desalinhados.

---

## 2. O que conta como bloqueio

- [ ] **Um bloqueio de leitura no telemóvel é, por si, motivo de devolução** — não um detalhe a corrigir depois. Se alguma das seis superfícies não se conseguir ler (texto cortado, sobreposição, scroll horizontal, controlo inalcançável) na revisão de telemóvel, a entrega volta.

Um bloqueio de leitura nomeia-se na devolução pela superfície onde acontece (Home, uma Notícia, a Agenda, o Acervo, Associe-se ou Apoia-se) e pela acção que o revelou.

---

## 3. Desempenho

- [ ] **Lighthouse ≥ 90 em mobile** nas superfícies da primeira entrega, medido no ambiente de revisão **depois da purga**.

Medir em `/`, `/noticias/`, `/agenda-ipcn/`, `/acervo/`, `/associe-se/` e `/apoia-se/` — as superfícies percorridas na secção 1 — com `?nocache=1`, depois da purga.

Esta medição está **pendente**: não há nenhum valor Lighthouse registado no repositório e o repositório não tem WordPress local — a medição só existe depois do deploy e da purga em `stagingredesign`, que são acção do dono. Fica o critério e o sítio da medição; o resultado apura-se no browser, com `?nocache=1`.

---

## 4. Os oito endereços que não podem mudar

Conferir que cada endereço existente responde como antes (200 e o conteúdo esperado), no telemóvel e no computador.

- [ ] `/` — Home.
- [ ] `/noticias/` — o hub das notícias.
- [ ] `/acervo/` — o archive do Acervo.
- [ ] `/temas/<slug>/` — um tema do Acervo (ex.: `/temas/memoria-oral/`), servido pelo archive.
- [ ] `/apoia-se/` — a superfície de apoio.
- [ ] `/associe-se/` — a página de associação.
- [ ] `/fale-conosco/` — o contacto.
- [ ] `/politica-de-privacidade/` — a política.

---

## 5. O veredicto

Depois de percorridas as seis superfícies e conferidos os oito endereços, o veredicto é dado falado — «aprovado» ou a devolução com as superfícies a corrigir. Não deixa artefacto. Só depois dele a produção muda, e a publicação é manual, no hPanel, pelo Daniel; nenhum pipeline, script ou hook deste repositório publica em produção.
