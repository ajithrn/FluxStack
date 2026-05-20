<?php

namespace App\Modules;

class ModuleManager
{
    private array $modules = [];
    private array $enabled = [];
    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get core module IDs (cannot be disabled).
     */
    public function getCoreModules(): array
    {
        return $this->config['core'] ?? ['module-manager', 'site-settings'];
    }

    /**
     * Register a module instance.
     */
    public function register(BaseModule $module): void
    {
        $this->modules[$module->id()] = $module;
    }

    /**
     * Boot all enabled modules (register then boot).
     * Modules are sorted by dependencies before registration.
     */
    public function boot(): void
    {
        // Sort modules so dependencies come first
        $sorted = $this->sortByDependencies();

        // First pass: register enabled modules
        foreach ($sorted as $module) {
            if ($this->canLoad($module)) {
                try {
                    $module->register();
                    $this->enabled[] = $module->id();
                } catch (\Throwable $e) {
                    error_log(sprintf('[FluxStack] Module "%s" failed to register: %s', $module->id(), $e->getMessage()));
                }
            }
        }

        // Second pass: boot enabled modules
        foreach ($this->enabled as $id) {
            try {
                $this->modules[$id]->boot();
            } catch (\Throwable $e) {
                error_log(sprintf('[FluxStack] Module "%s" failed to boot: %s', $id, $e->getMessage()));
            }
        }
    }

    /**
     * Sort modules so that dependencies are loaded before dependents.
     */
    private function sortByDependencies(): array
    {
        $sorted = [];
        $visited = [];

        $visit = function (BaseModule $module) use (&$visit, &$sorted, &$visited) {
            if (isset($visited[$module->id()])) return;
            $visited[$module->id()] = true;

            foreach ($module->dependencies() as $depId) {
                if (isset($this->modules[$depId])) {
                    $visit($this->modules[$depId]);
                }
            }

            $sorted[] = $module;
        };

        foreach ($this->modules as $module) {
            $visit($module);
        }

        return $sorted;
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

        // Reset scaffold flag so views/styles are re-checked on next boot
        if ($enabled) {
            delete_option('fluxstack_scaffolded_' . $id);
        }
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
