# Fase 4 (cont.) — Correção da fonte de ícones do Divi (ETmodules)

> Executado em **26/08/2026** no **staging** (`https://staging.ipcnbrasil.org`), via SSH + WP-CLI.
> Publicação em produção: manual, pela contratante (hPanel), quando aprovado.

## Sintomas reportados
1. Menu mobile virava a letra **"A"** ao invés do ícone de hambúrguer.
2. Setas do slider mostravam **"4" e "5"** ao invés de `‹` / `›`.
3. Menu não ocupava todo o espaço (layout mobile quebrado como efeito colateral).

## Causa raiz
O pacote do tema **Divi 4.20.2 está incompleto/corrompido**: os arquivos `ETmodules.{woff,ttf,eot,svg}`
e o `@font-face` correspondente **não existiam** no tema (nem no staging, nem na produção).
O Divi usa a fonte `ETmodules` para ícones de UI (hambúrguer, setas). Sem ela, o navegador
renderiza o **caractere Unicode cru** da tabela de ícones:
- Hambúrguer = `\61` → letra **"a"/"A"**
- Setas do slider = códigos que mapeiam para **"4" e "5"**

**Não é a versão do Divi** — é o pacote que veio sem os arquivos de fonte (típico de download
nulled incompleto). Confirma a intuição do cliente: o problema é do **tema**, não da configuração.

## Solução (reversível e versionada)
1. Restaurados os 4 arquivos `ETmodules.{woff,ttf,eot,svg}` na pasta
   `wp-content/themes/Divi/core/admin/fonts/` (baixados de mirror público confiável —
   a fonte `ETmodules` é idêntica entre as versões do Divi para ícones de UI).
2. Injetado o `@font-face` do `ETmodules` via **mu-plugin** `ipcn-optimizations.php`
   (em vez de editar o `style.min.css`, que seria sobrescrito em atualização).

## Validação (staging)
- Arquivos da fonte: **200** (woff/ttf/eot/svg)
- `@font-face ETmodules` presente no `<head>` (`ipcn-etmodules-fix`)
- WOFF válido (header `wOFF`, tabela `cmap` presente)
- Home 200, wp-admin 302, sem erros de debug

## Pendências
1. **Validar na tela real** (cliente abrir o staging no celular / devtools mobile) — confirmar que
   o hambúrguer e as setas aparecem como ícones.
2. **Publicar em produção** + aplicar os arquivos de fonte também na produção (versionado no repo
   sob `wp-content/themes/Divi/core/admin/fonts/`? NÃO — tema não é versionado; aplicar manualmente
   ou via script de deploy).
3. **Decisão sobre o Divi (licenciar vs migrar)** — ver `docs/plano-tema-divi.md`.

⚠️ **Nota de risco:** os arquivos de fonte restaurados NÃO estão no repositório git (o tema Divi
não é versionado por ser nulled). Se o tema for reinstalado/atualizado, a fonte some de novo.
A correção definitiva passa por licenciar o Divi ou migrar de tema (ver plano).
