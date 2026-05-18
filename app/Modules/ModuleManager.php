<?php

namespace App\Modules;

class ModuleManager
{
    private array $modules = [];
    private array $enabled = [];

    /**
     * Register a module instance.
     */
    public function register(BaseModule $module): void
    {
        $this->modules[$module->id()] = $module;
    }

    /**
     * Boot all enabled modules (register then boot).
     */
    public function boot(): void
    {
        // First pass: register enabled modules
        foreach ($this->modules as $module) {
            if ($this->canLoad($module)) {
                $module->register();
                $this->enabled[] = $module->id();
            }
        }

        // Second pass: boot enabled modules
        foreach ($this->enabled as $id) {
            $this->modules[$id]->boot();
        }
    }

    /**
     * Check if a module can be loaded (enabled + dependencies met).
     */
    private function canLoad(BaseModule $module): bool
    {
        if (! $module->isEnabled()) {
            return false;
        }

        foreach ($module->dependencies() as $dependency) {
            if (! isset($this->modules[$dependency]) || ! $this->modules[$dependency]->isEnabled()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all registered modules.
     */
    public function getAll(): array
    {
        return $this->modules;
    }

    /**
     * Get only enabled modules.
     */
    public function getEnabled(): array
    {
        return array_filter($this->modules, fn ($m) => $m->isEnabled());
    }

    /**
     * Get modules by category.
     */
    public function getByCategory(string $category): array
    {
        return array_filter($this->modules, fn ($m) => $m->category() === $category);
    }

    /**
     * Get a specific module by ID.
     */
    public function get(string $id): ?BaseModule
    {
        return $this->modules[$id] ?? null;
    }

    /**
     * Auto-discover modules from the modules directory.
     */
    public function discover(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $directories = glob($path . '/*', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $dirName = basename($dir);

            // Skip template/scaffold directories
            if (str_starts_with($dirName, '_')) {
                continue;
            }

            $moduleFile = $dir . '/module.php';

            if (file_exists($moduleFile)) {
                $module = require $moduleFile;

                if ($module instanceof BaseModule) {
                    $this->register($module);
                }
            }
        }
    }

    /**
     * Toggle a module on/off.
     */
    public function toggle(string $id, bool $enabled): void
    {
        $settings = get_option('fluxstack_modules', []);
        $settings[$id] = $enabled;
        update_option('fluxstack_modules', $settings);
    }

    /**
     * Get module settings from the database.
     */
    public function getSettings(): array
    {
        return get_option('fluxstack_modules', []);
    }

    /**
     * Get standalone block modules (category = 'block').
     */
    public function getBlocks(): array
    {
        return $this->getByCategory('block');
    }

    /**
     * Get non-block modules.
     */
    public function getNonBlockModules(): array
    {
        return array_filter($this->modules, fn ($m) => $m->category() !== 'block');
    }
}
