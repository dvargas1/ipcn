# Staging Divi — 10/09/2026: QR PIX + hero Apoia-se + eyebrow Associe-se + footer

## O que foi feito no staging (https://staging.ipcnbrasil.org) — Fase 4, sem redesign
- **Apoia-se (2382)**: QR Code PIX do PagSeguro importado (attachment 5358, uploads/2026/09/qrcode-pix.jpg), placeholder "Em breve" removido. OBS: QR é dinâmico PagSeguro com validade 08/09/2026 — confirmar com a cliente se troca por Pix estático da chave contato@ipcnbrasil.org.
- **Hero Apoia-se**: espelhado no Quem Somos (background_size contain, min_height 493px, custom_padding="||||false|false"). Antes: contain + 10vw padding na section vazia = faixa gigante oca.
- **Associe-se (2188)**: eyebrow "Formulário de associação" 11px → 15px bold, letter-spacing 0.12em.
- **Footer (TB layout 3384, usado pelos templates 3394/2580)**: refeito — logo horizontal logo-menu-site.png (220px, link home), email corrigido contato@ipcnbrasil.org (tinha contato@staging...), tel/email clicáveis, texto institucional curto, sem margem negativa -40px, crédito centralizado.
- **mu-plugin ipcn-optimizations.php**: removido o CSS hack "ipcn-footer-logo-fix" (max-width 180px/140px no logo vertical antigo) — não conflita mais.

## Backups no servidor (/tmp)
- footer_backup_*.txt (footer 3384 anterior)
- apoia_backup_*.txt / apoia_backup2/3/4_*.txt (post_content 2382 nas 4 versões)
- associe_backup_*.txt (2188)
- muplugin_backup*.php (mu-plugin original)

## Estimativa de deploy produção (manual pela contratante)
- conteúdo: páginas Apoia-se/Associe-se (posts) + attachment QR + footer do Theme Builder
- código: nada novo no tema/mu-plugin (só REMOÇÃO do hack css)
- o QR do PIX venceu 08/09 — pedir QR estático ou novo dinâmico antes/acima do go-live

## Pendências
- cliente aprovar footer + hero no staging
- decisão Pix estático vs dinâmico
- validação mobile 375px nas 3 páginas tocadas

## SSH desbloqueado 10/09: chave ED25519 autorizada no hPanel pelo Daniel (chave nova ssh-key + nossa hermes-agent-ipcn)
