const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const fs = require('fs');

// Auto-discover block editor.js entry points from modules directory.
// Outputs compiled files to a build subfolder within each block module.
function getBlockEntries() {
    const entries = {};
    const modulesDir = path.resolve(__dirname, 'modules');

    if (!fs.existsSync(modulesDir)) {
        return entries;
    }

    const moduleDirs = fs.readdirSync(modulesDir, { withFileTypes: true })
        .filter(d => d.isDirectory() && !d.name.startsWith('_'));

    for (const moduleDir of moduleDirs) {
        const modulePath = path.join(modulesDir, moduleDir.name);

        // Top-level editor.js (standalone block modules)
        const editorFile = path.join(modulePath, 'editor.js');
        if (fs.existsSync(editorFile)) {
            entries[moduleDir.name + '/build/editor'] = editorFile;
        }

        // Nested blocks inside CPT modules
        const blocksDir = path.join(modulePath, 'blocks');
        if (fs.existsSync(blocksDir)) {
            const blockDirs = fs.readdirSync(blocksDir, { withFileTypes: true })
                .filter(d => d.isDirectory());

            for (const blockDir of blockDirs) {
                const blockEditorFile = path.join(blocksDir, blockDir.name, 'editor.js');
                if (fs.existsSync(blockEditorFile)) {
                    entries[moduleDir.name + '/blocks/' + blockDir.name + '/build/editor'] = blockEditorFile;
                }
            }
        }
    }

    return entries;
}

const entries = getBlockEntries();

module.exports = {
    ...defaultConfig,
    entry: entries,
    output: {
        ...defaultConfig.output,
        path: path.resolve(__dirname, 'modules'),
        filename: '[name].js',
    },
};
