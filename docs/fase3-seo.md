# Fase 3 — SEO, performance e segurança

> Executado em **26/08/2026** no **staging** (`https://staging.ipcnbrasil.org`), via SSH + WP-CLI.
> Publicação em produção: manual, pela contratante (hPanel), quando aprovado.

## 1. SEO (Yoast)

- Plugin `wordpress-seo` (28.3) **reativado** + configurado (title/metadesc/Open Graph/breadcrumbs).
- `robots.txt` + sitemap corrigidos (estavam 404): `sitemap_index.xml` → **200**, bloco Yoast com `Sitemap:`.
- **Pitfall:** o CDN da Hostinger (HCDN) cacheia o `robots.txt` antigo (`x-hcdn-cache-status: HIT`). Na publicação, **purgar o cache do HCDN no hPanel** ou o robots.txt velho persiste.
- **Typo corrigido**: "Culturas Negra" → "Culturas Negras" (no `blogname`).

## 2. Performance (LiteSpeed Cache)

- Plugin `litespeed-cache` **instalado e ativado**.
- Cache de página **ligado** (servidor LiteSpeed da Hostinger entrega `x-litespeed-cache: hit`).
- **CDN (HCDN) integrado** via `O_CDN = true`.
- Object cache **off** (sem Redis/Memcached na Hostinger shared — correto).
- **Resultado:** TTFB ~2.2s (miss) → **16ms** (hit) — melhoria de ~137x.
- Comando WP-CLI nativo do LiteSpeed (`wp litespeed-conf`) tem bug fatal em PHP 8.2 — configurado via API interna (`LiteSpeed\Conf::update_confs`).

## 3. Segurança (Wordfence)

- Plugin `wordfence` **instalado e ativado**.
- Firewall **ligado** em *learning mode* (seguro para staging).
- **XML-RPC bloqueado** (403) — regra no `.htaccess` (`<Files xmlrpc.php> Deny from all`) + filtro `xmlrpc_enabled=false` no mu-plugin.
- **Versão do WP escondida** — `the_generator` vazio no mu-plugin + `hideWPVersion` no Wordfence. (Resta só "Site Kit by Google" no generator, que não expõe versão do core.)
- **X-Pingback header removido** no mu-plugin.
- **Live traffic off** (performance).
- **2FA obrigatório** para `administrator` e `editor` (Wordfence Login Security). Cada usuário ativa no próprio perfil com app TOTP (Google Authenticator/etc).

### mu-plugin atualizado (`wp-content/mu-plugins/ipcn-optimizations.php`)
Adicionados:
- `the_generator` → vazio (esconde versão)
- `xmlrpc_enabled` → false
- remove header `X-Pingback`

## 4. Really Simple SSL — desativado

O plugin `really-simple-ssl` foi **desativado** no staging. É redundante (a Hostinger já força HTTPS) e foi a **causa do bug de permalinks 404 na Fase 0** (seus blocos de redirect no `.htaccess` quebravam o rewrite do WP). Os blocos já foram removidos do `.htaccess` na Fase 2 deste staging.

## Validação (frontend)

| Item | Antes | Depois |
|---|---|---|
| `robots.txt` | `Disallow: /` (404 no sitemap) | Bloco Yoast + `Sitemap:` |
| `sitemap_index.xml` | 404 | **200** |
| TTFB (hit) | ~2.2s | **16ms** |
| XML-RPC | 200 (aberto) | **403** (bloqueado) |
| Versão do WP | exposta no `<meta generator>` | **removida** |
| 2FA admin/editor | off | **obrigatório** |
| Home / wp-admin | 200 / 302 | 200 / 302 ✓ |

## Pendências de publicação
1. **Publicar em produção** + purgar cache do HCDN (senão robots.txt antigo persiste).
2. Aplicar o mu-plugin atualizado também na produção (versionado no repo).
3. Usuários ativarem 2FA nos próprios perfis (app TOTP).
4. (Opcional) trocar URL de login.
