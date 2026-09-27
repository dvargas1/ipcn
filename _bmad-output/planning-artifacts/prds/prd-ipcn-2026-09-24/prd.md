---
title: IPCN — Site público e operação editorial
status: final
created: 2026-09-24
updated: 2026-09-24
---

# PRD: IPCN — Site público e operação editorial

*Título de trabalho.*

## 0. Document Purpose

Este PRD é para a contratante, para o PM e para quem vier a desenhar arquitectura e histórias. Define o que o site do Instituto de Pesquisas das Culturas Negras tem de fazer, em duas fases, sem dizer como se constrói.

O vocabulário canónico está no Glossário. Requisitos funcionais usam esses termos e IDs estáveis (FR-1 a FR-18). Jornadas usam UJ-1 a UJ-5. Inferências estão marcadas `[ASSUMPTION]` e reunidas no índice. Decisões de mecanismo, papéis herdados do plano de 30/08 e razão de alternativas rejeitadas estão em `addendum.md`.

O par de UX já existe: `_bmad-output/planning-artifacts/ux-designs/ux-ipcn-2026-09-24/` (`DESIGN.md` para identidade visual, `EXPERIENCE.md` para arquitectura, estados e fluxos). Este PRD governa o âmbito do produto; o par de UX governa o desenho e o comportamento.

Constrói em cima de `docs/plano-tema-custom-e-portal-associados.md` (identidade e o que o visitante vê) e de `AGENTS.md` (regras de produção). Não herda o portal, o paywall, os cargos por núcleo nem a escada de seis papéis desse plano. O que dele se afastou está registado em `addendum.md`. A auditoria de 17/09 não é fonte.

## 1. Vision

O IPCN precisa de um site que se leia como instituto de pesquisa e memória, não como um WordPress acumulado. Quem chega — estudante, professora, jornalista, pessoa do movimento — encontra notícias, a agenda e o acervo sem criar conta, no telemóvel, com a mesma clareza no computador.

A primeira entrega é esse site público, completo, aprovado pela contratante antes de ir para produção. A produção continua a ser publicada manualmente no hPanel pelo Daniel. A linha antiga do site, em tema de terceiros sem licença e com plugins mortos, não é a superfície deste trabalho.

A fase seguinte separa quem escreve, quem aprova e quem administra, para a equipa publicar notícias e acervo sem depender de uma só pessoa técnica. Portal de associados, acervo fechado e pagamento não entram nesta vaga. Associar-se é um pedido. Apoiar é um gesto público, manual.

## 2. Target User

### 2.1 Jobs To Be Done

- **Visitante:** perceber o que o IPCN é, ler uma notícia, ver o próximo encontro e folhear o acervo, no telemóvel, sem conta.
- **Candidata:** pedir associação com os dados mínimos, e saber que o pedido chegou.
- **Quem apoia:** ver como contribuir (PIX) sem criar conta nem pagar dentro do site.
- **Criadora** (fase seguinte): submeter notícia ou item de acervo, e ver que ficou por aprovar.
- **Aprovador** (fase seguinte): rever, devolver ou publicar, com rasto de quem decidiu.
- **Administrador:** manter o site e, na fase seguinte, as contas. Não precisa de ser quem aprova o texto.
- **Contratante:** rever o site público no telemóvel e no computador e dizer se pode ir para produção.

### 2.2 Non-Users (v1)

- Associada à espera de área logada, acervo fechado ou anuidade cobrada no site.
- Quem procura uma app nativa.
- Quem edita a linha antiga do site (staging Divi) como se fosse este produto.

### 2.3 Key User Journeys

- **UJ-1. Camila lê o IPCN no autocarro.** Camila, professora, ouviu falar do instituto e abre o link no telemóvel, deslogada. A Home cabe no ecrã sem banner esticado; ela abre uma Notícia, volta, entra no Acervo, filtra por Tema, abre um Item. **Clímax:** distingue peça de acervo de notícia e encontra o que procurava sem login. **Resolução:** partilha o endereço. O site não pediu cadastro. **Falha:** um Tema sem itens explica-se e devolve o caminho ao Acervo inteiro.
- **UJ-2. João quer saber se há encontro esta semana.** João abre a Agenda, do menu ou da Home. **Clímax:** numa olhadela sabe se vai ou se espera. **Falha:** sem encontros por vir, vê o vazio digno e o canal público, nunca encontros passados a fingir de agenda.
- **UJ-3. Lúcia pede para se associar.** Lúcia abre a página Associe-se, preenche nome, e-mail e telefone, envia. **Clímax:** vê confirmação de que o pedido foi aceite para envio, e a mensagem chega a contato@ipcnbrasil.org. **Resolução:** espera contacto humano. Não nasce conta. **Falha:** se o envio falha, vê erro na mesma página, com os campos mantidos, e pode repetir.
- **UJ-4. Rita deixa uma notícia por aprovar.** *(fase seguinte)* Rita entra como Criadora, cria uma Notícia ou um Item de Acervo, submete. **Clímax:** fica por aprovar e não aparece ao público. **Resolução:** o Aprovador publica ou devolve. **Falha:** publicar directo não lhe é oferecido.
- **UJ-5. A contratante diz se pode ir para produção.** A contratante percorre o site de revisão no telemóvel, repete no computador. **Clímax:** aprova, ou devolve uma lista do que falta. **Resolução:** só depois disso a produção muda, à mão.

## 3. Glossary

- **Visitante** — pessoa no site sem conta. É o público da primeira entrega.
- **Candidata** — Visitante que submete `Associe-se`. Não se torna utilizador.
- **Home** — a página de entrada. Orienta; não é o arquivo inteiro.
- **Notícia** — texto de actualidade do IPCN. Não é um Item de Acervo.
- **Secção** — divisão editorial de Notícias (Destaques, Diáspora, Colunistas, Notas, Editorial, Drops, Memórias). Não é Página institucional nem Tema.
- **Agenda** — lista de encontros com data. Um encontro sem data não conta como próximo.
- **Acervo** — coleção de pesquisa, distinta das Notícias. Público nesta vaga.
- **Item de Acervo** — uma peça do Acervo, com Tema.
- **Tema** — eixo de classificação de um Item de Acervo. Não é uma categoria de Notícia.
- **Página institucional** — página de apresentação (Quem somos, Projetos, Fale conosco, Política de privacidade). Não é Notícia, Secção nem Item.
- **Fale conosco** — a Página institucional que contém o formulário de mensagem. Não é um formulário solto.
- **Associe-se** — pedido de associação por mensagem, não uma conta.
- **Apoia-se** — página pública de apoio manual (PIX). Não é pagamento dentro do site.
- **Contratante** — quem aprova o site público antes da produção.
- **Daniel** — o Operador de produção. Único autorizado a publicar em produção, pelo hPanel.
- **Criadora** — na fase seguinte, quem submete Notícia ou Item de Acervo e não publica.
- **Aprovador** — na fase seguinte, quem revê e publica Notícia ou Item, ou devolve. Não é, por definição, o Administrador.
- **Administrador** — quem mantém o site e, na fase seguinte, as contas. Não precisa de aprovar texto.
- **Primeira entrega** — o site público completo, sem login, antes de cargos.
- **Fase seguinte** — Criadora, Aprovador e Administrador a operar Notícias e Acervo. Não inclui portal nem pagamento.
- **Produção** — https://ipcnbrasil.org. Só muda por publicação manual no hPanel, pelo Daniel, depois do veredicto da Contratante.

## 4. Features

### 4.1 Site público

**Description:** O Visitante percorre o IPCN sem conta, no telemóvel primeiro. A Home tem cinco blocos: hero, Notícias, Acervo, Agenda e faixa de contacto. Realiza UJ-1 e UJ-5.

**Functional Requirements:**

#### FR-1: Entrar sem conta

O Visitante pode ler a Home, Notícias, Agenda, Páginas institucionais e o Acervo sem login. Realiza UJ-1.

**Consequences (testable):**
- Nenhuma dessas superfícies pede autenticação.
- Um endereço de Notícia ou de Item de Acervo abre directo a quem o recebe.

#### FR-2: Home que orienta

A Home tem cinco blocos, por esta ordem: hero, Notícias, Acervo, Agenda e faixa de contacto. Realiza UJ-1.

**Consequences (testable):**
- Com as imagens bloqueadas, o primeiro ecrã continua a dizer o que o IPCN é: eyebrow, título e uma linha de substância.
- O título do hero não contém número de anos; não caduca.
- A secção de Notícias mostra só Notícias. Uma pauta de Agenda ali é defeito.
- A secção de Notícias filtra por slug (`noticias`), nunca por term_id.
- A vitrine do Acervo não repete as Notícias e usa composição própria — uma peça em destaque e duas secundárias — diferente da grelha compacta de Notícias.
- Um cartão sem imagem mostra o marcador `IPCN`, sem encolher o cartão em relação aos vizinhos.

#### FR-3: Ler uma Notícia

O Visitante pode abrir uma Notícia em leitura longa e voltar à lista. Realiza UJ-1.

**Consequences (testable):**
- O corpo do texto usa a medida de leitura definida em `DESIGN.md` e não obriga a scroll horizontal a 375px.
- Há um caminho de volta às Notícias, visível a partir do fim do texto.

#### FR-4: Folhear o Acervo

O Visitante pode listar o Acervo, filtrar por Tema e abrir um Item. Realiza UJ-1.

**Consequences (testable):**
- O Acervo é público por inteiro nesta vaga. Não há "últimos N" nem 403.
- Filtrar por Tema muda o endereço e o botão de voltar funciona.
- Um Tema sem itens explica-se; não mostra uma grelha de outro tipo de conteúdo.
- Um Item distingue-se de uma Notícia pela tag de Tema visível e pelo sítio onde aparece.
- Sem Tema atribuído, o Item mostra a ausência; não inventa uma tag.
- Com poucos itens hoje, a lista não mostra paginação; com mais de uma página, mostra.

#### FR-5: Menu estável

O Visitante alcança Home, Notícias, Acervo e Agenda no primeiro nível, mais as pílulas `Associe-se` e `Apoia-se`. As Secções e as Páginas institucionais têm endereço próprio.

**Consequences (testable):**
- Os destinos de primeiro nível existem e respondem.
- Cada Secção tem endereço próprio e responde, alcançável a partir de Notícias.
- Cada Página institucional tem endereço próprio e responde, alcançável a partir do rodapé.
- Nenhuma Secção nem Página institucional ocupa o primeiro nível.

### 4.2 Agenda

**Description:** A Agenda é encontro, não feed. Realiza UJ-2.

**Functional Requirements:**

#### FR-6: Próximos encontros

O Visitante vê os próximos encontros com data, nome e, quando existir, lugar. A Agenda tem superfície própria com a lista completa; a Home mostra os próximos.

**Consequences (testable):**
- Só entram encontros com data igual ou posterior a hoje.
- A ordem é cronológica.
- Sem lugar, o encontro ainda aparece; o lugar não se inventa.
- Um encontro sem data não aparece como próximo.

#### FR-7: Agenda vazia

Se não houver próximo encontro, o Visitante vê um estado vazio digno e um caminho para o canal público do IPCN.

**Consequences (testable):**
- Não aparece uma grelha de encontros passados no lugar dos próximos.
- O estado vazio nomeia o canal público e liga a ele.
- [ASSUMPTION: o canal público desse vazio é o Instagram do IPCN.]

### 4.3 Associe-se e Apoia-se

**Description:** Associar-se é pedido. Apoiar é informação. Nenhum dos dois cria conta nem cobra. Realiza UJ-3.

**Functional Requirements:**

#### FR-8: Pedido de associação

A Candidata pode enviar nome, e-mail e telefone. O pedido chega a contato@ipcnbrasil.org. Não nasce utilizador.

**Consequences (testable):**
- Confirmação visível quando o envio é aceite, dizendo que a resposta vem por e-mail.
- Num envio falhado: erro na mesma página, campos preenchidos mantidos, resumo de erro no topo e possibilidade de repetir. Nunca confirma o que não aconteceu.
- Submissão automatizada não gera pedido, e vê o mesmo estado de erro — nunca silêncio.
- [ASSUMPTION: CPF, núcleo e "como quer contribuir" não entram neste pedido. A fase seguinte, se houver ficha de associada, reabre esses campos com base legal nomeada.]

#### FR-9: Fale conosco

O Visitante pode enviar nome, e-mail e mensagem pela página Fale conosco, para o mesmo destino, com a mesma confirmação e o mesmo estado de erro.

**Consequences (testable):**
- A mensagem não se mistura com um pedido de associação no assunto.
- O mesmo destino: contato@ipcnbrasil.org.
- A página está alcançável a partir de qualquer outra.

#### FR-10: Apoio manual

A página Apoia-se mostra como contribuir por PIX, sem checkout e sem conta.

**Consequences (testable):**
- Não há botão que cobre cartão ou recorrência.
- Se o QR estiver vencido ou em falta, a página diz que o código está a ser actualizado em vez de mostrar um código morto.
- [ASSUMPTION: o QR estático válido é fornecido pela contratante; o site não gera PIX.]

### 4.4 Confiança e leitura

**Description:** O site trata dados e leitura como instituto público, não como vitrine de plugins.

**Functional Requirements:**

#### FR-11: Privacidade e cookies

O Visitante pode aceitar, recusar o não-essencial ou gerir preferências antes de analítica ou marketing dispararem. A política de privacidade é uma Página institucional.

**Consequences (testable):**
- Sem escolha, analítica e marketing não disparam.
- As três acções de cookies têm igual proeminência e nenhuma é pré-seleccionada.
- A barra de cookies não obscurece o conteúdo nem o foco do teclado.
- A escolha persiste neste browser e pode ser revista depois.
- A política de privacidade abre sem login.

#### FR-12: Identidade visual e acessibilidade

O site lê-se como instituto: muito branco, navy como cor da instituição, ocre como convite (fundo, texto escuro), terracota como acento, títulos fortes, corpo legível. Cartões com imagem recortada, nunca cabeçalho esticado.

**Consequences (testable):**
- WCAG 2.2 AA verificado nos pares de cor usados, com a razão declarada por componente.
- Texto claro sobre fundo ocre não passa.
- Foco visível em todos os elementos interactivos, incluindo sobre navy, ocre e terracota.
- Links distinguem-se do texto por mais do que cor.
- O texto de interface está em português do Brasil e acentuado correctamente; copy sem acentos é defeito.
- [ASSUMPTION: as referências de tom continuam a ser Museu Afro Brasil, Amistad e NYPL Events, já aceites no plano de 31/08. Anti-referência: o site Divi acumulado.]

#### FR-13: Telemóvel primeiro

As superfícies da primeira entrega funcionam no telemóvel e no computador, sem app.

**Consequences (testable):**
- A 320px de largura não há scroll horizontal, no fluxo de UJ-1, UJ-2 e UJ-3.
- Todo o controlo tem alvo de toque de pelo menos 44px.

### 4.5 Fase seguinte — quem escreve e quem publica

**Description:** Depois do site público aprovado, a equipa deixa de depender de uma só pessoa técnica para Notícias e Acervo. Fora desta fase: portal, paywall, pagamento. Realiza UJ-4.

**Functional Requirements:**

#### FR-14: Criadora submete, não publica

A Criadora pode criar e submeter Notícia e Item de Acervo. O conteúdo nasce por aprovar e não aparece ao Visitante até o Aprovador publicar.

**Consequences (testable):**
- Publicar directo não lhe é oferecido.
- A Criadora não cria nem altera Agenda nem Páginas institucionais.
- [ASSUMPTION: há uma ou mais Criadoras, cada uma com conta própria. Não há conta partilhada.]

#### FR-15: Aprovador decide

O Aprovador pode publicar ou devolver uma Notícia ou um Item, sobre a versão submetida. A decisão fica registada (quem, quando, versão).

**Consequences (testable):**
- Devolver exige uma nota escrita; sem nota, a devolução não acontece.
- A nota fica guardada com a decisão e visível a quem submeteu.
- O Visitante só vê a versão publicada.
- [ASSUMPTION: um Aprovador basta. Não há comité obrigatório por peça.]

#### FR-16: Administrador não é o Aprovador

O Administrador pode manter o site e as contas. Aprovar texto é do Aprovador, salvo se a mesma pessoa acumular os dois papéis por decisão explícita.

**Consequences (testable):**
- Dá para ter Administrador que não publica Notícias.
- [ASSUMPTION: Agenda e Páginas institucionais são editadas pelo Aprovador ou pelo Administrador, não pela Criadora.]

### 4.6 Publicação em produção

**Description:** Produção não é um botão do fluxo de conteúdo. Realiza UJ-5.

**Functional Requirements:**

#### FR-17: Veredicto da contratante antes da produção

O site público não substitui a produção sem o veredicto da Contratante. O veredicto é falado e não gera artefacto próprio.

**Consequences (testable):**
- Existe um ambiente de revisão distinto da produção, e é aí que o veredicto é dado.
- A produção não muda antes desse veredicto. O registo da aprovação é o próprio deploy, feito depois dele.
- Nenhum artefacto deste trabalho publica em produção.

#### FR-18: Produção manual

A publicação em produção é feita pelo Daniel no hPanel. Ninguém a automatiza.

**Consequences (testable):**
- Não existe pipeline deste repo para produção.
- A linha antiga (staging Divi) não recebe o site novo.

## 5. Non-Goals (Explicit)

- Não é um portal de associados.
- Não fecha o Acervo atrás de login, nem mostra "últimos 10" como substituto de paywall.
- Não cobra anuidade, doação recorrente nem cartão.
- Não tem busca nesta entrega.
- Não é uma app.
- Não redesenha a linha Divi. Essa linha está congelada.
- Não migra o arquivo antigo de Notícias para dentro do Acervo.
- Não cria cargos por núcleo.
- Não é um arquivo de pesquisa com sala marcada, finding aid ou empréstimo. Isso seria outro produto.

## 6. MVP Scope

### 6.1 In Scope

FR-1 a FR-13: site público completo — Home, Notícias e Secções, Acervo e Temas, Agenda, Páginas institucionais, Associe-se por mensagem, Fale conosco, Apoia-se manual, cookies e política de privacidade, telemóvel primeiro — mais o gate da Contratante (FR-17) e a produção manual (FR-18).

### 6.2 Out of Scope for MVP

- FR-14 a FR-16 ficam para a fase seguinte, logo após o site público aprovado. Não são um produto à parte; são a continuação nomeada. `[NOTE FOR PM]` A contratante pode querer ver "quem publica" cedo. Isso não puxa o portal.
- Portal, ficha de associada, paywall, pagamento: fase posterior, com dono operacional. Reabrir só com decisão nova.
- Busca.
- Coordenação por núcleo.
- Migração do legado para o Acervo.

## 7. Success Metrics

**Primary**

- **SM-1:** A Contratante percorre Home, Notícia, Agenda, Acervo, Associe-se e Apoia-se no telemóvel e no computador e diz que pode ir para produção, sem lista aberta de bloqueios. Valida FR-2, FR-12, FR-17.
- **SM-2:** Um Visitante completa UJ-1 e UJ-2 sem conta, em sessão de revisão. Valida FR-1, FR-4, FR-6.

**Secondary**

- **SM-3:** Um pedido de Associe-se de teste chega a contato@ipcnbrasil.org e a pessoa vê confirmação. Valida FR-8.
- **SM-4:** Na fase seguinte, uma Notícia submetida por Criadora não aparece ao Visitante até o Aprovador publicar, e a decisão fica registada. Valida FR-14, FR-15.

**Counter-metrics (do not optimize)**

- **SM-C1:** Número de contas criadas na primeira entrega. Deve permanecer zero.
- **SM-C2:** Plugins ou temas de terceiros acrescentados para imitar o site antigo. Não é sinal de progresso.

## 8. Open Questions

1. O QR de PIX vigente chega quando? Não bloqueia a primeira entrega — o estado "código em actualização" cobre a espera.

## 9. Assumptions Index

- §4.2 FR-7 — Vazio da Agenda aponta o Instagram do IPCN.
- §4.3 FR-8 — Pedido de associação não pede CPF nem núcleo.
- §4.3 FR-10 — QR estático vem da contratante; o site não gera PIX.
- §4.4 FR-12 — Referências de tom: Museu Afro, Amistad, NYPL Events.
- §4.5 FR-14 — Contas de Criadora são individuais.
- §4.5 FR-15 — Um Aprovador basta por peça.
- §4.5 FR-16 — Agenda e Páginas institucionais não são da Criadora.

## 10. Platform

Web. Telemóvel primeiro, piso de 320px de largura, computador obrigatório. Sem app, sem PWA como requisito. Sem modo escuro nesta entrega. Um ambiente de revisão distinto da produção.

## 11. Aesthetic and Tone

Instituto, não portal de notícias e não revista. Muito branco. Rótulos curtos. Data e lugar em voz baixa. Hero em substância: pesquisa, memória e cultura negra — acervo vivo para quem estuda, ensina e transforma. Ocre convida; o segundo gesto é associar-se, como acto cívico, não como chave. Agenda parece encontro (dia, lugar, roda). Fotografia acompanha o texto; não faz de banner.

Anti-referência: cabeçalho esticado, grelhas inconsistentes, logo a ocupar o telemóvel, menu sem hierarquia.

Voz de qualquer texto de interface: directa, institucional, em português do Brasil. Sem marketing de startup. Sem "desbloqueie o acervo".

As famílias tipográficas, a escala, os tokens de cor e as regras de contraste estão em `DESIGN.md`; o comportamento, estados e fluxos em `EXPERIENCE.md`. Este PRD não os duplica.

## 12. Information Architecture

Primeiro nível: Home, Notícias, Acervo, Agenda, mais as pílulas `Associe-se` e `Apoia-se`.

Dentro de Notícias: as Secções editoriais (Destaques, Diáspora, Colunistas, Notas, Editorial, Drops, Memórias), cada uma com endereço próprio.

Páginas institucionais: Quem somos, Projetos, Fale conosco, Política de privacidade. Alcançam-se pelo rodapé.

O Acervo filtra-se por Tema. Notícias não usam Tema. Agenda não é uma categoria disfarçada de Acervo. `/temas/<slug>` responde com o Acervo filtrado; não tem ecrã próprio.

Sem busca.

## 13. Stakeholders and Approvals

- **Contratante:** dá o veredicto antes da produção (UJ-5, FR-17). Falado, sem artefacto formal — decisão de 24/09.
- **Daniel:** o Operador de produção. Único autorizado a publicar em produção, pelo hPanel (FR-18).
- **Aprovador** (fase seguinte): veredicto de cada Notícia e Item, não da produção.

Sem o veredicto da Contratante, arquitectura e histórias podem avançar no ambiente de revisão. Produção, não.

## 14. Constraints and Guardrails

- **Privacidade:** o pedido de Associe-se coleta nome, e-mail e telefone para contacto humano, não para criar perfil. Destino único: contato@ipcnbrasil.org. Não pedir dado sensível nesta vaga. A base legal de uma ficha de associada futura não se assume aqui. Consentimento de cookies é separado do pedido de associação.
- **Segurança:** não há pipeline para produção. Segredos, `wp-config` e cópias de segurança não entram no repositório. Não se pede nem se guarda password de produção.
- **Desempenho:** Lighthouse ≥ 90 em mobile nas superfícies da primeira entrega, herdado do plano (T6.1). Verificar no ambiente de revisão, depois de purgar a cache — sem purge, o HTML servido não é prova.
- **SEO:** os endereços existentes mantêm-se. Nenhuma Secção, o Acervo nem as Páginas institucionais mudam de URL nesta entrega. O plugin de SEO já instalado mantém-se.
- **Custo:** não acrescentar tema ou plugin pago para cumprir um FR que os blocos nativos do WordPress já cumprem.
- **Linha congelada:** o staging do tema antigo não recebe este trabalho.
- **Ambiente de revisão:** o menu de primeiro nível vive na base de dados, não no tema. A arquitectura acima edita a navegação, não o HTML do cabeçalho.

## 15. Compliance

- WCAG 2.2 AA em texto, controlos, foco, reflow (320px) e alvos de toque. Inclui identidade de erro nos formulários e ajuda consistente no rodapé.
- LGPD no que esta vaga realmente coleta: cookies com escolha antes do não-essencial (FR-11) e formulários com finalidade visível e dado mínimo (FR-8, FR-9). Não é um programa de conformidade. Encarregado e enquadramento de pequeno porte ficam por nomear se a fase seguinte passar a guardar ficha de associada. `[NOTE FOR PM]`

## 16. Risks

- **O gate sem prova.** O veredicto da Contratante é falado e não deixa artefacto. Nada impede tecnicamente uma publicação sem ele. Mitigação: só o Daniel publica, e é ele quem conhece a regra; aceite como risco em 24/09.
- **Aprovação comprada com o visual antigo.** Mitigação: SM-C2 e a anti-referência da §11.
- **Confundir fase seguinte com portal.** Mitigação: §5 e SM-C1. Criadora não é associada.
- **QR vencido no ar.** Mitigação: FR-10.
- **Descoberta sem busca.** Mitigação parcial: o hub de Notícias e o filtro de Tema. Vigiar: se o Tema ficar por preencher, a descoberta piora. Candidato a v2.
- **Auditoria de 17/09 a reentrar como backlog.** Mitigação: §0. Grande parte dela já foi aplicada no commit `57825f5`; o que sobra é backlog técnico, não requisito deste PRD.
