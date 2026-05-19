# Deployment

## Production Build

```bash
npm run build
```

Single command. Vite compiles theme CSS/JS to `public/build/`. PHP-only blocks need no compilation.

## What Gets Deployed

**Required on server:**
- All theme files except `node_modules/`
- `vendor/` directory (run `composer install --no-dev` on server, or include in deploy)
- `public/build/` directory (compiled assets)

**Not needed on server:**
- `node_modules/`
- `resources/css/`, `resources/js/` (source files — compiled output is in `public/build/`)
- `docs/`, `TASKS.md`

## .distignore

Controls what's excluded from release zips:

```
.git
.gitignore
.editorconfig
.env
node_modules
resources/css
resources/js
resources/fonts/.gitkeep
resources/images/.gitkeep
package.json
package-lock.json
composer.json
composer.lock
vite.config.js
TASKS.md
docs/
```

## Per-Project Workflow

FluxStack is forked per project:

1. Fork/clone the repo
2. `composer install && npm install && npm run build`
3. Activate theme in WordPress admin
4. Enable needed modules in Appearance → FluxStack
5. Enable Site Settings sub-pages (Header, Footer, Home)
6. Customize design tokens in `resources/css/app.css`
7. Add project-specific modules as needed
8. Deploy

## Server Requirements

- WordPress 7.0+ (for PHP-only blocks with `autoRegister`)
- PHP 8.3+
- HTTPS recommended
- Composer must be run (vendor is gitignored)

## Optimization

After deployment, run:

```bash
wp acorn optimize    # Cache config and views
wp acorn view:cache  # Pre-compile Blade templates
```

## Security

Blade templates should not be publicly accessible. Add to your server config:

**Nginx:**
```nginx
location ~* \.(blade\.php)$ {
    deny all;
}
```

**Apache:**
```apache
<FilesMatch ".+\.(blade\.php)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>
```

## Environment Notes

### DevKinsta (Local)
Works out of the box. Set `APP_URL` in environment or update `vite.config.js` for HMR.

### Kinsta (Production)
Supports Bedrock/Trellis deployments. Standard WordPress hosting works — just ensure PHP 8.3+ and `vendor/` is present.
