# Plano: Tema Divi (licenciar vs migrar)

> Contexto: o site `ipcnbrasil.org` usa o **Divi 4.20.2 nulled** (sem licença).
> Esta decisão trava várias frentes e deve vir **antes** de investir em reconfiguração visual.

## Por que isso importa agora

1. **Segurança:** tema nulled **não recebe atualizações** da Elegant Themes → sem patches de
   vulnerabilidades. O Wordfence está instalado (Fase 3), mas não substitui a atualização do tema.
2. **Integridade:** o pacote veio **incompleto** (faltou a fonte `ETmodules` — corrigida na Fase 4
   de forma reversível, mas ela some se o tema for tocado).
3. **Funcionalidade futura:** sem licença, não há suporte, não há atualizações de compatibilidade
   com WP/PHP novos, e plugins premium do ecossistema Divi ficam de fora.
4. **Legal/contratante:** uso de tema nulled em site institucional tem implicação de direitos.

## Opção A — Licenciar o Divi (Elegant Themes)
- **Prós:** mantém todo o layout atual (header, slider, Theme Builder), só vira legítimo e
  passa a receber updates. Menor risco de quebrar o que já existe.
- **Contras:** custo de assinatura (Elegant Themes cobra licença anual ~US$ 89/ano ou lifetime).
  Exige credencial para baixar pacote oficial limpo.
- **Como proceder:** contratante compra licença → agente baixa o Divi oficial 4.20.x →
  substitui os arquivos do tema (preservando `wp-content` e DB) → fonte ETmodules volta nativa.

## Opção B — Migrar para tema gratuito (GeneratePress / Astra)
- **Prós:** 100% livre de custo e de risco de licença; tema enxuto, rápido, bem mantido.
- **Contras:** **perde o layout atual** (Theme Builder, slider, header centralizado) — exigiria
  reconstruir a home e o header do zero. É um "redesign" de fato, não só consolidação.
- **Como proceder:** instalar GP/Astra → recriar header/menu/slider com o page builder nativo
  (ou o próprio Gutenberg) → migrar conteúdo (já é WP, então posts/páginas permanecem).

## Opção C — Manter nulled (status quo)
- **Prós:** zero custo hoje.
- **Contras:** todos os riscos acima; técnico precisa "remendar" manualmente sempre que algo falta.
  **Não recomendado** para site institucional de longo prazo.

## Recomendação do agente
- **Curto prazo:** manter nulled **só até** a contratante decidir, com as correções de fonte
  aplicadas (já feitas no staging) e documentadas. Não investir em redesign profundo enquanto
  o tema estiver nesse estado.
- **Decisão recomendada:** **Opção A (licenciar)** se o orçamento permitir — preserva o trabalho
  já feito e resolve a raiz (segurança + integridade). **Opção B** só se a contratante quiser
  mudar a identidade visual de fato (redesign).

## Perguntas para a contratante
1. Há orçamento para licenciar o Divi?
2. O layout atual (header centralizado + slider) deve ser preservado, ou ela quer redesign?
3. Site institucional pode usar tema nulled (risco legal) ou precisa ser 100% legítimo?
