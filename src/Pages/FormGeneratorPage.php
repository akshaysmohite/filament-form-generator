<?php

namespace Akshay\FilamentFormGenerator\Pages;

use Filament\Pages\Page;
use Akshay\FilamentFormGenerator\FilamentFormGeneratorPlugin;
use Akshay\FilamentFormGenerator\Services\CodeGenerator;

class FormGeneratorPage extends Page
{
    protected static string $view = 'filament-form-generator::pages.form-generator';

    protected static ?string $slug = 'form-generator';

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

    public static function getNavigationIcon(): string
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
            return config('filament-form-generator.navigation.label', 'Form Generator');
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
     * Server-side Code Generation endpoint (optional bridge for Livewire)
     */
    public function generatePhpCode(array $nodes): string
    {
        $generator = app(CodeGenerator::class);
        return $generator->generate($nodes);
    }
}

