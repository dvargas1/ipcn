# Epic 4 Context: Aprovar e publicar sem surpresas

<!-- Generated from planning artifacts. Regenerate with compile-epic-context if planning docs change. -->

## Goal

Fecha a entrega: define o que chega ao ambiente de revisão, como o procedimento de publicação se repete sem depender de memória, e como a Contratante verifica o site antes de a produção mudar. O resultado é verificável, não declarativo: nenhum artefacto deste repositório publica em produção — que só muda à mão no hPanel, pelo Daniel — e a revisão não é julgada por HTML em cache. Sem este épico, o site pode estar pronto e ainda assim mudar em produção sem veredicto, ou ser avaliado por páginas servidas de cache que já não correspondem ao código.

## Stories

- Story 4.1: O procedimento de deploy escrito e repetível
- Story 4.2: A produção está fora do alcance deste repositório
- Story 4.3: A checklist de revisão da Contratante

## Requirements & Constraints

- O veredicto da Contratante é condição da publicação, e é falado: não gera e-mail, checklist assinada nem artefacto próprio. O registo da aprovação é o próprio deploy, feito depois dele.
- O ambiente de revisão é distinto da produção e é onde o veredicto é dado; o público não deve notar a diferença.
- A publicação em produção é manual, no hPanel, por uma só pessoa autorizada. Nenhum pipeline, script ou hook deste repositório publica em produção.
- Não entram no repositório segredos, `wp-config` nem cópias de segurança; não se pede nem se guarda a password de produção. As superfícies versionadas são o tema e os mu-plugins.
- O envelope operacional é Hostinger com LiteSpeed e HCDN: artefactos entregues por pacote e `scp`, extraídos com `--strip-components=3` no tema do redesign, e cache purgada com `litespeed-purge all`.
- HTML em cache não é prova. A verificação — de desempenho incluída — é feita no ambiente de revisão, depois da purga.
- Desempenho: Lighthouse ≥ 90 em mobile nas superfícies da primeira entrega.
- Não há build step, suite de testes nem CI. A verificação de código é `php -l` sobre todo o PHP alterado; a verificação visual é no browser.
- Os endereços existentes mantêm-se, e o plugin de SEO já instalado mantém-se.

## Technical Decisions

- Deploy só para `stagingredesign`. O staging Divi (linha antiga) está congelado e não é destino deste trabalho.
- O mu-plugin só se deploya quando ele próprio muda — nunca numa alteração só de tema — e nunca para o staging Divi, onde o comportamento se mantém idêntico sem ele.
- O script de verificação de sintaxe vive no repositório, corre `php -l` em cada ficheiro PHP alterado, falha com o nome do ficheiro e a linha se algum não compilar, e não altera ficheiros nem faz deploy. As suas instruções de uso ficam no repositório.
- Limite conhecido desse script: apanha erros de sintaxe, não as classes de defeito que este projecto já produziu — block markup impresso como texto, cartão a encolher por falta do marcador de imagem, título duplicado por template. Essas só se vêem no browser.
- O procedimento de deploy é conteúdo do repositório: pacote do tema, envio, extracção com `--strip-components=3`, purga; o caminho remoto fica confirmado por escrito, não inferido.
- Stack declarada pelo tema: WordPress >= 6.4, PHP >= 8.1, sem build step, sem dependências externas além das fontes do Google.

## UX & Interaction Patterns

- A revisão segue a jornada da Contratante: percorrer o site de revisão no telemóvel, repetir no computador, pelas superfícies Home, Notícia, Agenda, Acervo, Associe-se e Apoia-se.
- Um bloqueio de leitura no telemóvel é, por si, motivo de devolução — não um detalhe a corrigir depois.
- Referências de largura para a revisão: piso de 320px sem scroll horizontal, 375px como largura confortável de revisão, ponto de viragem em 782px, computador a 1440px.

## Cross-Story Dependencies

- 4.1 depende do script de verificação de sintaxe entregue no início do trabalho do tema (Epic 1) e do envelope operacional deste épico; sem ele o procedimento volta a depender de memória.
- 4.2 condiciona 4.1: sem a garantia de que nada publica sozinho, o procedimento escrito deixa de ser seguro. A regra do mu-plugin de 1.12 (deploy só quando ele muda, nunca no staging Divi) repete-se no procedimento.
- 4.3 depende de 4.1: a medição de desempenho e a contagem de títulos só valem depois da purga, no ambiente de revisão.
- As superfícies percorridas na checklist são entregues pelos Épicos 1 a 3; a checklist não as reconstrói, verifica-as. Um defeito do tipo "dois `h1` na mesma página" (corrigido no Epic 1) só aparece quando se mede o HTML servido, e não o template.
- O veredicto verificado em 4.3 é a condição de que depende a publicação manual descrita em 4.2.
