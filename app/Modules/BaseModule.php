<?php

namespace App\Modules;

abstract class BaseModule
{
    /** Module unique identifier */
    abstract public function id(): string;

    /** Human-readable module name */
    abstract public function name(): string;

    /** Module description */
    abstract public function description(): string;

    /** Module category (block, cpt, feature, utility) */
    abstract public function category(): string;

    /** Register hooks, CPTs, taxonomies, etc. */
    abstract public function register(): void;

    /** Boot/initialize the module (runs after all modules registered) */
    public function boot(): void {}

    /** Check if module is enabled */
    public function isEnabled(): bool
    {
        $modules = get_option('fluxstack_modules', []);
        return $modules[$this->id()] ?? $this->enabledByDefault();
    }

    /** Whether module is enabled by default */
    public function enabledByDefault(): bool
    {
        return false;
    }

    /** Module dependencies (other module IDs) */
    public function dependencies(): array
    {
        return [];
    }

    /** Get module directory path */
    public function path(): string
    {
        return get_theme_file_path("modules/{$this->id()}");
    }

    /** Get module directory URI */
    public function uri(): string
    {
        return get_theme_file_uri("modules/{$this->id()}");
    }
}
