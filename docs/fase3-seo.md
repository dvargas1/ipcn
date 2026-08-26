# Fase 3 — SEO (parte 1: Yoast + robots/sitemap)

> Executado em **26/08/2026** no **staging** (`https://staging.ipcnbrasil.org`), via SSH + WP-CLI.
> Publicação em produção: manual, pela contratante (hPanel), quando aprovado.

## Problema de origem
O diagnóstico inicial marcava "SEO quebrado: Yoast inativo → `robots.txt` e sitemap retornam 404".

## O que foi feito (staging)

### 1. Ativar e configurar Yoast SEO
- Plugin `wordpress-seo` (28.3) **reativado**.
- `wpseo`: `enable_xml_sitemap => true`.
- `wpseo_titles`: templates de title (post/página/arquivo/home), metadesc da home, **Open Graph + Twitter** ligados, **breadcrumbs** ligados.

### 2. Corrigir robots.txt / sitemap 404
- **Causa real:** o `robots.txt` era servido com `Disallow: /` (WordPress default bloqueando indexação) e o sitemap dava 404 porque o Yoast estava inativo.
- Após ativar: `sitemap_index.xml` → **200**, `post-sitemap.xml` → **200**, `robots.txt` (WP real) → bloco Yoast: `Disallow:` (vazio) + `Sitemap: https://staging.ipcnbrasil.org/sitemap_index.xml`.
- **Pitfall descoberto:** o **CDN da Hostinger (HCDN)** cacheia o `robots.txt` antigo em borda (`x-hcdn-cache-status: HIT`). Para ver o WP real, use cache-buster (`?cb=...`). **Na publicação, é preciso purgar o cache do HCDN no hPanel** ou o robots.txt antigo continua sendo servido.

### 3. .htaccess do staging igualado à produção
- Removidos os blocos **Really Simple SSL Redirect** e **Really Simple Security Redirect** (redundantes — a Hostinger já força HTTPS; na produção esses blocos causaram o bug de permalinks 404 na Fase 0).
- Resultado: `.htaccess` do staging idêntico ao da produção (só bloco WordPress).

### 4. Typo corrigido (quick win de identidade)
- Nome do site: "Instituto de Pesquisas das Culturas **Negra**" → "Culturas **Negras**" (plural correto). Reflete no `<title>` das páginas.

## Validação (frontend)
- `<title>`: `IPCN é membro do Comitê Gestor do Cais do Valongo - Instituto de Pesquisas das Culturas Negras` ✓
- Open Graph: `og:title`, `og:description`, `og:url`, `og:locale=pt_BR` ✓
- Sitemap posts/páginas/categorias: **200** ✓
- Home WP: **200** (permalinks OK após mexer no .htaccess) ✓

## Pendências
1. **Publicar em produção** + purgar cache do HCDN (senão robots.txt antigo persiste).
2. **Performance** (LiteSpeed Cache + HCDN) — não feito ainda.
3. **Segurança** (Wordfence, XML-RPC, 2FA) — não feito ainda.
4. Separador de título do Yoast está `sc-dash` (–); avaliar trocar por `-` ou `|`.
