<?php

namespace Akshay\FilamentFormGenerator;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Akshay\FilamentFormGenerator\Pages\FormGeneratorPage;

class FilamentFormGeneratorPlugin implements Plugin
{
    protected ?string $navigationGroup = null;
    protected ?string $navigationIcon = null;
    protected ?string $navigationLabel = null;
    protected ?int $navigationSort = null;
    protected ?Closure $authorizeUsing = null;
    protected ?array $allowedEnvironments = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament('filament-form-generator');

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-form-generator';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            FormGeneratorPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Custom boot actions if necessary
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;
        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('filament-form-generator.navigation.group', 'Development');
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;
        return $this;
    }

    public function getNavigationIcon(): string
    {
        return $this->navigationIcon ?? config('filament-form-generator.navigation.icon', 'heroicon-o-wrench-screwdriver');
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;
        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->navigationLabel ?? config('filament-form-generator.navigation.label', 'Form Generator');
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;
        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort ?? config('filament-form-generator.navigation.sort', 99);
    }

    public function allowedEnvironments(array $environments): static
    {
        $this->allowedEnvironments = $environments;
        return $this;
    }

    public function getAllowedEnvironments(): array
    {
        return $this->allowedEnvironments ?? config('filament-form-generator.allowed_environments', ['local', 'testing']);
    }

    public function authorize(Closure $callback): static
    {
        $this->authorizeUsing = $callback;
        return $this;
    }

    public function isAuthorized(): bool
    {
        if ($this->authorizeUsing !== null) {
            return (bool) call_user_func($this->authorizeUsing);
        }

        $allowed = $this->getAllowedEnvironments();

        if (in_array('*', $allowed, true)) {
            return true;
        }

        return app()->environment($allowed);
    }
}

