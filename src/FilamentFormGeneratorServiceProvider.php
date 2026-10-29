<?php

namespace Akshay\FilamentFormGenerator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFormGeneratorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-form-generator';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        // Bind singleton if needed
        $this->app->singleton(Services\CodeGenerator::class, function () {
            return new Services\CodeGenerator(
                importStyle: config('filament-form-generator.code_generation.import_style', 'imported'),
                indentSpaces: (int) config('filament-form-generator.code_generation.indent_spaces', 4)
            );
        });
    }
}

