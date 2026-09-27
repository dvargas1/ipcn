# Addendum — IPCN PRD

Profundidade que não cabe no PRD. Não é requisito.

## Alternativas rejeitadas em 2026-09-24

- **Portal, paywall e pagamento na mesma vaga que o redesign.** Rejeitado. O plano de 30/08 metia cadastro, login, painel, últimos 10 itens e escada de seis cargos no v1. O utilizador fechou o contrário: site público primeiro, cargos a seguir, portal e pagamento fora.
- **Criadora só no acervo.** O plano dizia publicador restrito ao acervo. A decisão de 2026-09-24 alarga a submissão a Notícias, sempre sem publicar direto.
- **Home de três secções como no STATUS da linha Divi.** Esse "3 sections" é histórico Divi, não requisito do FSE.
- **Home catálogo ao estilo Museu Afro** (destaques, acervo, agenda como três blocos de museu). Rejeitada: a Home canónica é a do redesign em revisão.
- **Menu de primeiro nível com todos os destinos**, plano ou agrupado. Rejeitado: primeiro nível curto, Secções com página própria dentro de Notícias.
- **Busca no site nesta entrega.** Rejeitada: sem Tema preenchido, uma busca promete mais do que entrega. Candidata a v2.
- **Barra de cookies translúcida.** Rejeitada pela revisão de acessibilidade: sobre conteúdo variável, a razão de contraste deixa de ser computável e o foco deixa de ser fiável.
- **Registo formal do veredicto da Contratante** (e-mail de aprovação ou checklist assinada). Rejeitado em 24/09: o veredicto é falado e o registo é o próprio deploy. O custo está em §16 do PRD.
- **Nomes de papel Publicador / Revisor.** Rejeitado: a interface usa Criadora / Aprovador / Administrador.

## Decisões de identidade que se afastaram do plano de agosto

Registado porque a reconciliação de 24/09 as apanhou sem rasto:

- **Ocre e terracota deixam de ser alternativas.** O plano tratava-os como pivots mutuamente exclusivos ("só troca ocre por terracota") e o ocre como acento opcional. No site em revisão, o ocre é o fundo do botão primário e da faixa de contacto, e a terracota é o acento de rótulo — as duas convivem. `DESIGN.md` codifica isso.
- **Playfair Display entra como terceira família.** O plano limitava a duas (Oswald para títulos, Inter/Open Sans para corpo) e dizia "nada de 12 variações". A terceira família existe para leitura longa e está no `theme.json` desde antes desta vaga. `DESIGN.md` mantém-na com papel restrito.
- **O hero tem imagem de fundo.** O plano pedia hero "tipográfico, sem imagem esticada". O que existe é `assets/hero-bg.jpg` servida como cobertura (`cover`), não imagem esticada, e o texto lê-se com a imagem bloqueada (FR-2).

## Papéis do plano de 30/08, e onde foram parar

| Plano 30/08 | Destino |
|---|---|
| não associado, associado, ficha com CPF | Fora. Fase posterior, se houver portal. |
| publicador só no acervo, pending | Vira Criadora: Notícia e Acervo, sem publicar. |
| coordenador, diretoria | Não se herdam como nomes. A função de publicar fica no Aprovador. |
| administrador (SRE) | Permanece como Administrador técnico. Não é, por definição, o Aprovador. |
| núcleo | Continua fora, como no plano. |

Mecanismo (plugin de papéis, `user_meta`, CPT) não é decisão deste PRD. Vai para arquitetura.

## Sobre o cabeçalho centralizado

O plano da linha Divi e a auditoria de 17/09 tratavam "marca em cima, menu por baixo, centrado" como decisão da contratante. O cabeçalho do tema `ipcn-fse` tem hoje esse arranjo, mas **não** por herança daquela regra: é o desenho que ficou. Não é requisito deste PRD nem motivo para o desfazer. Se a contratante quiser impor o arranjo da linha Divi, é decisão nova.

## Estado do backlog da auditoria

O commit `57825f5` (backlog da auditoria aplicado) resolveu parte substancial das 13 tarefas de `docs/auditoria-redesign-fse-2026-09-17.md`: URL absoluto do hero, term_id da home, template de taxonomia duplicado, fallback de fonte do Oswald, `navy-soft`. O que sobra da auditoria é backlog técnico, não requisito deste PRD.

## Paisagem (consulta 2026-09-24)

Padrão dos sites lidos: menu por tarefa, agenda própria, acervo acessível a distância, apoio no primeiro nível sem área de sócio, notícia e memória na entrada. Museu Afro Brasil, Amistad, Cultne e MAN não mostraram acervo de pesquisa fechado por login.

Isto sustenta o corte, não é requisito de paridade.

Fontes: https://museuafrobrasil.org.br/ , https://amistadresearchcenter.org/ , https://cultne.org.br/ , https://man.ipeafro.org.br/

## LGPD — nota para a fase que criar ficha

Se um dia houver ficha de associada, o PRD dessa fase tem de nomear a base legal (execução do vínculo ou consentimento), a finalidade de cada campo, quem vê o dado e por quanto tempo. Não assumir que tudo é consentimento. Não é parecer. Texto de referência: Lei 13.709/2018.

## Acessibilidade — origem das correções

A revisão de acessibilidade de 24/09 calculou contraste contra os hex declarados e encontrou três falhas no primeiro rascunho do par de UX: ação de cookies em ink sobre navy (1.15:1), borda de campo em muted (1.23:1) e barra translúcida sem razão computável. As correções estão em `DESIGN.md` — `cookie-action`, `input`, barra opaca e anel de foco duplo. Ficheiro: `ux-designs/ux-ipcn-2026-09-24/review-accessibility.md`.
