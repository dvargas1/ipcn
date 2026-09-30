# Epic 3 Context: Falar com o instituto, e decidir sobre os próprios dados

<!-- Generated from planning artifacts. Regenerate with compile-epic-context if planning docs change. -->

## Goal

Dar ao Visitante e à Candidata as interacções públicas que faltam — pedir associação, escrever ao instituto e saber como apoiar por PIX — e o controlo real sobre cookies. Nenhuma destas superfícies cria conta nem cobra: associar-se é uma mensagem, apoiar é informação. O épico fixa o contrato dos formulários (peças nomeadas, retenção do que foi escrito no envio falhado, protecção de origem declarada que existe de facto), alinha o segundo formulário ao primeiro, e conserta a barra de cookies para que nada dispare antes de uma escolha. O resultado é verificável no browser, superfície por superfície.

## Stories

- Story 3.1: Associe-se, com o contrato fixado
- Story 3.2: O envio falhado não perde o que a pessoa escreveu
- Story 3.3: A origem é verificada nos formulários
- Story 3.4: Fale conosco
- Story 3.5: Apoia-se, com o QR honesto
- Story 3.6: Cookies com escolha real

## Requirements & Constraints

- **FR8 — pedido de associação.** A Candidata envia nome, e-mail e telefone; o pedido chega a `contato@ipcnbrasil.org` sem nascer utilizador. Confirmação visível quando aceito; no falhanço, erro na mesma página, campos mantidos e resumo no topo — nunca confirmar o que não aconteceu. Submissão automatizada gera o mesmo estado de erro, nunca silêncio. Não se pede CPF, núcleo nem "como contribuir".
- **FR9 — Fale conosco.** O Visitante envia nome, e-mail e mensagem, para o mesmo destino, com a mesma confirmação e o mesmo estado de erro. O assunto não se confunde com o de um pedido de associação. A página é alcançável a partir de qualquer outra.
- **FR10 — apoio manual.** Apoia-se mostra como contribuir por PIX, sem checkout e sem conta; sem botão que cobre cartão ou recorrência; QR vencido ou em falta diz que o código está em actualização e oferece o contacto, em vez de mostrar um código morto. O QR estático vem da contratante; o site não gera PIX.
- **FR11 — privacidade e cookies.** O Visitante aceita, recusa o não-essencial ou gere preferências antes de analítica ou marketing dispararem. As três acções têm igual proeminência e nenhuma é pré-seleccionada. A escolha persiste no browser e pode ser revista. A barra não obscurece conteúdo nem foco. A Política de privacidade abre sem login.
- **NFR2:** erro de formulário identificado por texto além da cor, ligado ao campo, com resumo no topo.
- **NFR3:** contacto e política de privacidade alcançáveis de qualquer página, pelo rodapé.
- **NFR5 (LGPD):** colher só nome, e-mail e telefone para contacto humano; nada de dados sensíveis; o consentimento de cookies é separado do pedido de associação.
- **NFR7:** sem nonce — o serviço de cache entrega HTML antigo; a protecção é honeypot mais verificação de origem, sem guardar estado, e um cabeçalho ausente não bloqueia pessoas reais.
- **NFR9:** sem build step, suite de testes ou CI; a verificação é `php -l` sobre o PHP alterado e observação no browser, depois de purgar a cache.
- **AR17:** o par de UX é vinculativo para o desenho e o comportamento.

## Technical Decisions

- **Contrato dos formulários (AD-11) — vinculativo, ambas as pontas concordam:** acções `ipcn_assoc` e `ipcn_contact`; campos `ipcn_nome`, `ipcn_email`, `ipcn_tel`, `ipcn_msg`, `ipcn_hp` (honeypot); redirect para `/associe-se/` com `cadastro` e para `/fale-conosco/` com `contato`, com valores `ok` e `erro`; destino único `contato@ipcnbrasil.org`. Deriva nestas peças faz todas as submissões cair num erro legítimo e silencioso.
- **Origem, sem nonce.** `Origin` ou `Referer` presente que não corresponda ao host do site rejeita a submissão com o estado de erro normal; com ambos ausentes — legítimo quando uma política de privacidade do browser os retira — a submissão é aceite e fica só com o honeypot. O comentário do handler tem de descrever o que o código realmente faz.
- **Retenção (AD-11).** Ao rejeitar, o handler guarda os valores submetidos num transient de vida curta, indexado por um token que vai no URL do redirect. O pattern do formulário lê o token, re-preenche os campos, escreve o resumo de erro no topo, liga cada erro ao campo por `aria-invalid` e `aria-describedby`, e apaga o transient. Feedback de formulário por argumento de consulta.
- **Comportamento em ficheiros de preocupação (AD-4).** `inc/forms.php` (handlers, honeypot, origem, transient de retenção) e `inc/cookie-bar.php` (barra e painel de cookies). `functions.php` é carregador e não acumula lógica; nenhum ficheiro de `inc/` regista o mesmo hook que outro.
- **Markup e CSS.** O markup é block markup em `patterns/` (AD-1); PHP não escreve comentários de bloco como literais. O CSS do tema vive só em `style.css`, sem `<style>` emitido por PHP (AD-5).
- **Consentimento e correio.** Escolha guardada em `localStorage['ipcn_cookie_consent_v1']`, com o objecto `{ts, necessary, analytics, marketing, all}`. Todo o correio vai para `contato@ipcnbrasil.org`, com o From forçado pelo mu-plugin.
- **Verificação (AD-13).** `php -l` sobre o PHP alterado; verificação visual no `stagingredesign` depois de `litespeed-purge all` e com `?nocache=1`.

## UX & Interaction Patterns

- **Estados de formulário.** Em envio: botão desactivado e legível, que diz que está a enviar, para não haver segundo envio por impaciência. Aceite: confirmação visível com o que acontece a seguir. Falhado: erro na mesma página, campos mantidos, resumo de erro no topo e mensagem ligada ao campo, com possibilidade de repetir. Honeypot preenchido: mesmo estado de erro — nunca silêncio; o campo escondido é inalcançável por teclado e invisível a leitores de ecrã.
- **Campo (`input`).** Etiqueta visível e associada, acima do campo, com `autocomplete` (`name`, `email`, `tel`). Borda `text-muted`, **nunca** `muted` (que fica só para cartões). Foco navy. `error-text`/`success-text` acompanham sempre uma palavra, nunca só cor.
- **Barra de cookies.** Fundo navy **opaco**, texto base; as três acções usam o mesmo tratamento `cookie-action` (ocre com texto chumbo e contorno base), com igual proeminência e nenhuma pré-seleccionada. Aparece só sem escolha registada, não bloqueia a leitura e cede o passo ao foco do teclado; há afastamento de scroll reservado para que o conteúdo focado nunca fique escondido. Sem escolha, analítica e marketing não disparam.
- **Estado do QR de PIX.** "Código PIX em atualização. Fale conosco para apoiar agora." — nunca mostrar um código vencido.
- **Microcopy.** PT-BR, voz institucional, frases completas, sem entusiasmo de marketing, exclamações ou emoji: "Recebemos seu pedido"; "Não foi possível enviar agora. Seus dados continuam aqui — tente de novo."; "Código PIX em atualização…".
- **Piso de acessibilidade.** Alvos de 44px em todo o controlo (incluindo acções de cookies); skip link como primeiro elemento focável; um só `h1` por página — nas superfícies de entrada é o da `page-hero`, e o conteúdo retoma no `h2` sem repetir o título; `autocomplete` nos campos e erros por `aria-invalid`/`aria-describedby`; resultado do envio anunciado a leitores de ecrã, incluindo quando falha; anel duplo de foco visível; ordem de foco sem armadilhas; movimento reduzido; reflow a 320px sem scroll horizontal. As páginas institucionais (`/associe-se/`, `/fale-conosco/`, `/apoia-se/`) abrem com a `page-hero` (UX-DR26).

## Cross-Story Dependencies

- Story 3.4 (Fale conosco) herda o contrato fixado em Story 3.1; Stories 3.2 (retenção) e 3.3 (origem) aplicam-se a ambos os formulários.
- `inc/forms.php` e `inc/cookie-bar.php` são partilhados com o Epic 1 — sobreposição de ficheiros aceite: o Epic 1 entrega a leitura pública, o Epic 3 as interacções.
- O acesso de primeiro nível às pílulas `Associe-se` e `Apoia-se` vem do Epic 1 (FR5); o FR11 depende da Política de privacidade (Página institucional) e do rodapé com contacto (NFR3).
- O envio de correio depende do mu-plugin que força o From. A publicação em produção fica fora deste épico (veredicto da Contratante, Epic 4).
