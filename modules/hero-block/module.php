<?php

use App\Modules\BlockModule;

return new class extends BlockModule
{
    public function id(): string { return 'hero-block'; }
    public function name(): string { return 'Hero Block'; }
    public function description(): string { return 'Full-width hero section with background image, heading, subheading, and CTA buttons.'; }
    public function blockName(): string { return 'fluxstack/hero'; }
    public function enabledByDefault(): bool { return true; }
};
