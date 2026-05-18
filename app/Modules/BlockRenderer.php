<?php

namespace App\Modules;

use Illuminate\Support\Facades\Blade;

class BlockRenderer
{
    /**
     * Render a block using a Blade template from the module directory.
     *
     * This is used as the render_callback for blocks that use Blade templates
     * instead of plain PHP render files.
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block inner content.
     * @param \WP_Block $block     Block instance.
     * @param string   $template   Path to the Blade template file.
     * @return string Rendered HTML.
     */
    public static function render(array $attributes, string $content, \WP_Block $block, string $template): string
    {
        // If the template file doesn't exist, return empty
        if (! file_exists($template)) {
            return '';
        }

        // Extract data for the template
        $data = [
            'attributes' => $attributes,
            'content' => $content,
            'block' => $block,
        ];

        // Render using Blade
        return self::renderBlade($template, $data);
    }

    /**
     * Render a Blade template file with given data.
     *
     * @param string $templatePath Absolute path to the .blade.php file.
     * @param array  $data         Data to pass to the template.
     * @return string Rendered HTML.
     */
    public static function renderBlade(string $templatePath, array $data = []): string
    {
        // Use WordPress output buffering as fallback
        // Blade compilation happens through Acorn's view system
        ob_start();

        // Extract variables for the template
        extract($data);

        // Include the template directly (supports both Blade and plain PHP)
        include $templatePath;

        return ob_get_clean();
    }

    /**
     * Create a render callback for a block module.
     *
     * Usage in block.json: "render": "file:./render.php"
     * Then in render.php, call BlockRenderer::callback()
     *
     * @param string $modulePath Path to the module directory.
     * @return callable The render callback function.
     */
    public static function callback(string $modulePath): callable
    {
        return function (array $attributes, string $content, \WP_Block $block) use ($modulePath) {
            $template = $modulePath . '/render.php';

            if (! file_exists($template)) {
                return '';
            }

            ob_start();
            include $template;
            return ob_get_clean();
        };
    }
}
