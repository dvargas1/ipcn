# Reconciliação — plano de 30/08 contra o PRD e o par de UX

- **Input:** `docs/plano-tema-custom-e-portal-associados.md`
- **Contra:** `prd.md`, `addendum.md`, `DESIGN.md`, `EXPERIENCE.md`
- **Data:** 2026-09-24

## O que ficou por resolver, e o que já foi tratado

Todos os itens abaixo foram absorvidos em 24/09 — ficam aqui como rasto da reconciliação.

1. **Cabeçalho centralizado.** O plano e a auditoria de 17/09 tratavam o arranjo "marca em cima, menu por baixo" como decisão da contratante para a linha Divi. O adendo rejeitava herdar isso como requisito, mas o `EXPERIENCE.md` descrevia o mesmo arranjo como o do FSE. Não é contradição — é coincidência de forma. Clarificado no adendo: o arranjo do FSE não é herança nem requisito; é o que ficou.
2. **Hero com imagem de fundo vs. hero tipográfico.** O plano pedia hero tipográfico sem imagem esticada; o tema serve `assets/hero-bg.jpg` como cobertura. Registado no adendo: não é imagem esticada, e o texto lê-se com a imagem bloqueada (FR-2).
3. **Playfair Display como terceira família.** O plano limitava a duas famílias. A terceira existe no `theme.json` e tem papel restrito a leitura longa. Registado no adendo.
4. **Ocre e terracota deixam de ser alternativas exclusivas.** O plano tinha-os como pivots mutuamente exclusivos e o ocre como acento opcional. No site, as duas convivem: ocre como fundo de botão primário, terracota como acento de rótulo. Registado no adendo e codificado em `DESIGN.md`.
5. **Restrições do plano que faltavam ao PRD.** Desempenho (Lighthouse ≥ 90 em mobile) e manutenção dos endereços/SEO entraram na §14 do PRD. A purga de cache como condição de verificação ficou na mesma secção.

## Confirmações

- Portal, paywall, escada de seis cargos, pagamento, campos CPF/núcleo, taxonomia `nucleo` e migração do legado: o PRD exclui e o adendo já registava. Não são lacunas.
- Tom e identidade: o que o plano descrevia (Museu Afro, Amistad, NYPL; branco, navy, ocre; sem cabeçalho esticado) mantém-se, agora distribuído entre §11 do PRD e `DESIGN.md`.
