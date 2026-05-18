<?php

use App\Modules\BlockModule;

return new class extends BlockModule
{
    public function id(): string { return 'cta-block'; }
    public function name(): string { return 'CTA Banner'; }
    public function description(): string { return 'Call-to-action banner with heading, description text, and button.'; }
    public function blockName(): string { return 'fluxstack/cta'; }
    public function enabledByDefault(): bool { return true; }
};
