---
id: SPEC-ipcn
companions:
  - glossary.md
  - ../../planning-artifacts/ux-designs/ux-ipcn-2026-09-24/DESIGN.md
  - ../../planning-artifacts/ux-designs/ux-ipcn-2026-09-24/EXPERIENCE.md
  - ../../planning-artifacts/architecture/architecture-ipcn-2026-09-24/ARCHITECTURE-SPINE.md
  - ../../planning-artifacts/prds/prd-ipcn-2026-09-24/addendum.md
sources:
  - ../../planning-artifacts/prds/prd-ipcn-2026-09-24/prd.md
---

> **Canonical contract.** This SPEC and the files in `companions:` are the complete, preservation-validated contract for what to build, test, and validate. Source documents listed in frontmatter are for traceability — consult them only if you need narrative rationale or prose color this contract intentionally omits.

# IPCN — Site público e operação editorial

## Why

A mandate and a pain, together. O site do Instituto de Pesquisas das Culturas Negras acumulou anos de construtor de página, tema de terceiros sem licença e plugins mortos; a contratação existe para arrumar a casa e construir a partir dela. Quem chega — estudante, professora, jornalista, pessoa do movimento — não encontra hoje um instituto de pesquisa legível no telemóvel. A força que move o trabalho é tornar o IPCN encontrável, com o acervo aberto e a agenda clara, antes de dar à equipa as ferramentas de quem escreve e quem aprova. A produção só muda depois de a contratante aprovar.

## Capabilities

- **CAP-1**
  - **intent:** O Visitante lê a Home, Notícias, a Agenda, as páginas institucionais e o Acervo sem autenticação.
  - **success:** Nenhuma dessas superfícies pede login; um endereço de Notícia ou de Item de Acervo abre directo a quem o recebe.
- **CAP-2**
  - **intent:** A Home apresenta o instituto em cinco blocos — hero, Notícias, Acervo, Agenda e faixa de contacto.
  - **success:** Com as imagens bloqueadas o primeiro ecrã continua a dizer o que o IPCN é; o título do hero não contém número de anos; Notícias mostra só Notícias; o Acervo usa vitrine (uma peça em destaque e duas secundárias) e não repete Notícias; um cartão sem imagem mostra o marcador `IPCN` sem encolher.
- **CAP-3**
  - **intent:** O Visitante abre uma Notícia em leitura longa e volta à lista.
  - **success:** O corpo usa a medida de leitura definida em `DESIGN.md` e não há scroll horizontal a 375px; há caminho de volta visível a partir do fim do texto.
- **CAP-4**
  - **intent:** O Visitante lista o Acervo, filtra por Tema e abre um Item.
  - **success:** O Acervo é público por inteiro, sem "últimos N" nem 403; filtrar por Tema muda o endereço e o botão de voltar funciona; um Tema sem itens explica-se; um Item distingue-se de uma Notícia pela etiqueta de Tema; sem Tema atribuído mostra a ausência em vez de inventar; a paginação só aparece com mais de uma página.
- **CAP-5**
  - **intent:** O Visitante alcança Home, Notícias, Acervo e Agenda no primeiro nível, mais as pílulas `Associe-se` e `Apoia-se`; as Secções e as páginas institucionais têm endereço próprio.
  - **success:** Cada Secção responde a partir de Notícias; cada página institucional responde a partir do rodapé; nenhuma delas ocupa o primeiro nível.
- **CAP-6**
  - **intent:** O Visitante vê os próximos encontros, com data, nome e lugar quando existe, na Home e numa página de Agenda.
  - **success:** Só entram encontros com `data_evento` igual ou posterior a hoje, por ordem cronológica; sem lugar o encontro ainda aparece; sem `data_evento` preenchido o encontro não aparece.
- **CAP-7**
  - **intent:** Sem encontros por vir, o Visitante encontra um estado vazio digno e o canal público do IPCN.
  - **success:** Não aparecem encontros passados no lugar dos próximos; o estado vazio nomeia o canal e liga a ele.
- **CAP-8**
  - **intent:** A Candidata envia nome, e-mail e telefone, e o pedido chega a `contato@ipcnbrasil.org` sem criar conta.
  - **success:** Confirmação visível quando o envio é aceite; num envio falhado, erro na mesma página com os campos mantidos, resumo no topo e possibilidade de repetir; uma submissão automatizada vê o mesmo estado de erro, nunca silêncio.
- **CAP-9**
  - **intent:** O Visitante envia nome, e-mail e mensagem pela página Fale conosco, para o mesmo destino.
  - **success:** O assunto não se mistura com o pedido de associação; a página está alcançável a partir de qualquer outra.
- **CAP-10**
  - **intent:** A página Apoia-se mostra como contribuir por PIX, sem checkout e sem conta.
  - **success:** Não há botão que cobre cartão ou recorrência; um QR vencido ou em falta mostra o estado de actualização em vez de um código morto.
- **CAP-11**
  - **intent:** O Visitante aceita, recusa o não-essencial ou gere preferências antes de analítica ou marketing dispararem.
  - **success:** Sem escolha, nada dispara; as três acções têm igual proeminência e nenhuma é pré-seleccionada; a barra não obscurece o conteúdo nem o foco do teclado; a escolha persiste no browser e pode ser revista; a política de privacidade abre sem login.
- **CAP-12**
  - **intent:** O site lê-se como instituto e cumpre WCAG 2.2 AA.
  - **success:** Razões de contraste declaradas por componente; texto claro sobre ocre reprovado; foco visível sobre navy, ocre e terracota; links distinguidos por mais do que cor; texto de interface em português do Brasil, acentuado.
- **CAP-13**
  - **intent:** As superfícies da primeira entrega funcionam no telemóvel e no computador, sem app.
  - **success:** A 320px de largura não há scroll horizontal nos fluxos de leitura, agenda e associação; todo o controlo tem alvo de toque de pelo menos 44px.
- **CAP-14**
  - **intent:** A Criadora cria e submete Notícia e Item de Acervo, que ficam por aprovar. *(fase seguinte)*
  - **success:** Publicar directo não lhe é oferecido; não cria nem altera Agenda nem páginas institucionais.
- **CAP-15**
  - **intent:** O Aprovador publica ou devolve uma Notícia ou um Item, com a decisão registada. *(fase seguinte)*
  - **success:** Devolver exige nota escrita, guardada com a decisão e visível a quem submeteu; o público só vê a versão publicada.
- **CAP-16**
  - **intent:** O Administrador mantém o site e as contas sem ser, por definição, quem aprova texto. *(fase seguinte)*
  - **success:** É possível ter um Administrador que não publica Notícias.
- **CAP-17**
  - **intent:** O site público não substitui a produção sem o veredicto da Contratante.
  - **success:** Existe um ambiente de revisão distinto; a produção não muda antes do veredicto; nenhum artefacto deste repositório publica em produção.
- **CAP-18**
  - **intent:** A publicação em produção é feita pelo Daniel no hPanel.
  - **success:** Não existe pipeline deste repositório para produção; a linha antiga do site não recebe o trabalho novo.

## Constraints

- **A primeira entrega é CAP-1 a CAP-13, mais CAP-17 e CAP-18.** CAP-14 a CAP-16 são a fase seguinte e não entram no mesmo lote de construção.
- **Sem contas.** Nenhuma superfície desta entrega pede autenticação.
- **Web, telemóvel primeiro.** Piso de 320px de largura, computador obrigatório, sem app, sem modo escuro nesta entrega.
- **Sem build step.** Sem framework de JavaScript, sem bundler, sem pré-processador de CSS.
- **Formulários sem nonce.** O serviço de cache entrega HTML antigo e um nonce em página cacheada quebra o formulário. A protecção é honeypot mais verificação de origem; um cabeçalho ausente não bloqueia pessoas reais.
- **LGPD no que esta entrega colhe.** Nome, e-mail e telefone para contacto humano, nada de dados sensíveis; o consentimento de cookies é separado do pedido de associação.
- **O pedido de associação colhe nome, e-mail e telefone.** Nem CPF, nem núcleo de interesse, nem "como quer contribuir".
- **Não se imita o aspecto do site antigo.** Não se acrescentam temas ou plugins para reproduzir a superfície acumulada; a aprovação não se compra com remendos.
- **O veredicto é falado e não deixa artefacto.** Só o Daniel publica, e é ele quem conhece a regra. Nada impede tecnicamente uma publicação sem veredicto.
- **Deploy manual, para o ambiente de revisão.** Purga de cache antes de acreditar no HTML. A produção é publicada à mão no hPanel. A linha antiga do site está congelada e não recebe este trabalho.
- **O tema e os mu-plugins são as únicas superfícies versionadas.** Não se versiona o tema de terceiros nem plugins externos.
- **O mu-plugin de otimizações é congelado**, com uma excepção autorizada: os blocos só-Divi passam a ser emitidos apenas quando o tema Divi está activo.
- **Sem suite de testes e sem CI.** A verificação é `php -l` sobre o PHP alterado e observação no browser, depois da purga.
- **Desempenho e endereços.** Lighthouse ≥ 90 em mobile; nenhum endereço existente muda nesta entrega.
- **Custo.** Não acrescentar tema ou plugin pago para cumprir o que os blocos nativos do WordPress já cumprem.

## Non-goals

- Não é um portal de associados.
- Não fecha o Acervo atrás de login, nem mostra "últimos 10" como substituto de paywall.
- Não cobra anuidade, doação recorrente nem cartão.
- Não tem busca nesta entrega.
- Não é uma app.
- Não redesenha a linha antiga do site, que está congelada.
- Não migra o arquivo antigo de Notícias para dentro do Acervo.
- Não cria cargos por núcleo.
- Não é um arquivo de pesquisa com sala marcada, finding aid ou empréstimo.

## Success signal

A Contratante percorre a Home, uma Notícia, a Agenda, o Acervo, Associe-se e Apoia-se no telemóvel e no computador, e diz que pode ir para produção, sem lista aberta de bloqueios. Um Visitante completa a leitura no telemóvel e a consulta da agenda sem conta. Nenhuma conta é criada nesta entrega.

## Assumptions

- O estado vazio da Agenda aponta o Instagram do IPCN.
- O QR estático do PIX é fornecido pela contratante; o site não gera PIX.
- As referências de tom são Museu Afro Brasil, Amistad Research Center e NYPL Events.
- As contas de Criadora são individuais, e um Aprovador basta por peça.

## Open Questions

- Quando chega o QR de PIX vigente? Não bloqueia a entrega: o estado de código em actualização cobre a espera.
- Como é que um administrador edita hoje um item do Acervo, dado que o tipo de conteúdo declara um `capability_type` próprio sem capabilities atribuídas a nenhuma role? Condiciona CAP-14 a CAP-16.
