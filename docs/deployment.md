# Deployment

## Production Build

```bash
npm run build
```

This runs both Vite (theme CSS/JS) and wp-scripts (block editor scripts). Output goes to `public/build/` and `public/blocks/`.

## Release Workflow

FluxStack uses GitHub Releases for distribution and auto-updates.

### Creating a Release

1. Update version in `style.css`
2. Update `CHANGELOG.md`
3. Commit and push to `main`
4. Create a GitHub release with a version tag (e.g., `v2.0.0`)
5. The `.distignore` file controls what's excluded from the release zip

### .distignore

Files excluded from the distribution zip:

```
.git
.github
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
webpack.blocks.config.cjs
TASKS.md
docs/
```

## Auto-Updates

To enable auto-updates from GitHub releases, add an update checker to the theme. This checks the GitHub API for new releases and prompts WordPress to update.

## Per-Project Deployment

FluxStack is designed to be forked per project:

1. Fork/clone the repo for the new project
2. Enable needed modules in Module Manager
3. Enable needed Site Settings sub-pages
4. Customize design tokens in `resources/css/app.css`
5. Add project-specific modules as needed
6. Deploy to hosting

## Environment

### DevKinsta (Local)

The theme works with DevKinsta out of the box. Set `APP_URL` in your environment or let Vite auto-detect.

### Staging / Production

1. Run `npm run build` before deploying
2. Ensure `vendor/` is included (or run `composer install --no-dev` on server)
3. The `public/build/` and `public/blocks/` directories must be present
4. Activate the theme in WordPress admin

### Server Requirements

- PHP 8.3+
- WordPress 6.4+
- HTTPS recommended
- `composer install` must be run (vendor is gitignored)
