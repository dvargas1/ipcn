# Fase 4 — Consolidação visual (sem redesign)

> Executado em **26/08/2026** no **staging** (`https://staging.ipcnbrasil.org`), via SSH + WP-CLI.
> Publicação em produção: manual, pela contratante (hPanel), quando aprovado.

## Problemas identificados (análise estrutural)

1. **Menu demais / repetido:** existia **1 único menu** ("MENU PRIMÁRIO", 14 itens) atribuído a **3 locais** (primário, secundário, rodapé) — o mesmo menu gigante aparecia no topo, meio e rodapé.
2. **Menu "no meio da tela":** o header do Divi Theme Builder tinha **logo numa row + menu noutra row abaixo, ambos centralizados** (`et_pb_menu--style-centered`, `without-logo`). Dava a sensação de menu solto no meio.
3. **Slider gigante:** a home tinha um `et_pb_fullwidth_slider` com **14 slides** (um resumo de cada seção) — empurrava o conteúdo e criava uma segunda "faixa" visual.

## O que foi feito (staging)

### 1. Menus separados
- **Menu Principal** (7 itens): Home, Quem Somos, Projetos, Notícias, Agenda IPCN, Associe-se, Contato.
- **Menu Rodapé** (8 itens): Quem Somos, Projetos, Editorial, Notícias, Drops Antirracista, Associe-se, Apoia-se, Contato.
- Atribuição: `primary-menu` → Principal, `footer-menu` → Rodapé, `secondary-menu` → Principal (mantido por ora).
- O antigo "MENU PRIMÁRIO" (14 itens) foi substituído.

### 2. Header do Divi reestruturado (post `et_header_layout` 1284)
- De: 2 rows (logo centralizado + menu centralizado) → Para: **1 row com logo à ESQUERDA + menu à DIREITA**.
- Estilo do menu: `centered` → `inline` (padrão, à direita).
- Logo habilitado (`logo-menu-site.png` existente na biblioteca).
- Removidos ícones de busca/carrinho do header (não usados).

### 3. Slider da home reduzido (post 1906)
- De 14 slides → **4 slides representativos** (manifesto, Quem Somos, Agenda, Associe-se).
- O restante das seções continua acessível pelo menu.

## Validação (frontend)
- Home HTTP **200**, wp-admin **302** ✓
- Logo presente no header (`logo-menu-site.png`) ✓
- Menu primário: **7 itens** (Home, Quem Somos, Projetos, Notícias, Agenda IPCN, Associe-se, Contato) ✓
- Estilo do menu: `inline` (não mais `centered`) ✓
- Slider: 14 → **4 slides** ✓
- Sem erros de debug ✓

## Pendências
1. **Publicar em produção** (o header e home são templates do Divi — requer deploy staging→produção ou replicação manual).
2. **Purgar cache do HCDN** no hPanel após publicar.
3. Ajuste fino de CSS (espaçamentos, cor do menu) pode ser necessário após ver na tela real — consulte a contratante.
4. O `secondary-menu` ainda aponta pro Principal; se não houver segundo menu visível, pode ser ignorado.
