<?php

namespace Akshay\FilamentFormGenerator\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Akshay\FilamentFormGenerator\FilamentFormGeneratorPlugin;
use Akshay\FilamentFormGenerator\Services\CodeGenerator;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Renderless;

class FormGeneratorPage extends Page
{
    protected static string $view = 'filament-form-generator::pages.form-generator';

    protected static ?string $slug = 'filament-form-generator';

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::Full;
    }

    public static function canAccess(): bool
    {
        try {
            $plugin = FilamentFormGeneratorPlugin::get();
            return $plugin->isAuthorized();
        } catch (\Throwable $e) {
            return app()->environment(['local', 'testing']);
        }
    }

    public static function getNavigationGroup(): ?string
    {
        try {
            return FilamentFormGeneratorPlugin::get()->getNavigationGroup();
        } catch (\Throwable $e) {
            return config('filament-form-generator.navigation.group', 'Development');
        }
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        try {
            return FilamentFormGeneratorPlugin::get()->getNavigationIcon();
        } catch (\Throwable $e) {
            return config('filament-form-generator.navigation.icon', 'heroicon-o-wrench-screwdriver');
        }
    }

    public static function getNavigationLabel(): string
    {
        try {
            return FilamentFormGeneratorPlugin::get()->getNavigationLabel();
        } catch (\Throwable $e) {
            return config('filament-form-generator.navigation.label', 'Filament Form Generator');
        }
    }

    public static function getNavigationSort(): ?int
    {
        try {
            return FilamentFormGeneratorPlugin::get()->getNavigationSort();
        } catch (\Throwable $e) {
            return config('filament-form-generator.navigation.sort', 99);
        }
    }

    public function getTitle(): string
    {
        return static::getNavigationLabel();
    }

    /**
     * Renderless so Livewire does not morph the Alpine canvas on every keystroke.
     */
    #[Renderless]
    public function generateCode(array $nodes, bool $wrapInMethod = false, bool $useFqcn = false): string
    {
        $generator = new CodeGenerator(
            importStyle: $useFqcn ? 'fqcn' : 'imported',
            indentSpaces: (int) config('filament-form-generator.code_generation.indent_spaces', 4),
        );

        return $generator->generate($nodes, $wrapInMethod);
    }
}

