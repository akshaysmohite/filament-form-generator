# Filament Visual Form Generator Plugin

A developer-focused, visual drag-and-drop form schema builder and code generator for **Filament v3**.

Build complex, production-ready form schemas visually in seconds and copy-paste clean, idiomatic Filament PHP code (`Forms\Components\...`) directly into your Resources, Pages, or Livewire components.

---

## Features

- 🎨 **Visual Drag-and-Drop Canvas:** Drag, reorder, nest, and duplicate form components with instant visual feedback.
- 📦 **Layout Containers:** Full support for `Section` (with title, description, collapsible, columns) and `Grid`.
- 🧩 **Core Form Fields:** `TextInput` (with email, numeric, password, tel, url subtypes), `Textarea`, `Select` (dynamic options), `Toggle`, `Checkbox`, `Radio`, and `DatePicker`.
- ⚙️ **Properties Inspector:** Live-edit labels, field names, placeholders, helper texts, required rules, disabled state, column spans, and options lists.
- 💻 **Zero-Latency PHP Code Exporter:** Outputs PSR-12 formatted Filament PHP code in real-time.
  - Option to output pure array schema `[...]` or wrapped in `public static function form(Form $form): Form`.
  - Toggle between imported classes (`TextInput::make()`) and FQCN (`Forms\Components\TextInput::make()`).
  - 1-click **Copy to Clipboard**.
- 📋 **JSON Schema Import & Export:** Save form designs to JSON and reload them anytime.
- ⚡ **Starter Templates:** One-click pre-built schemas for User Profile, Contact Form, and E-Commerce Product.

---

## Installation

### For Local Plugin Development
To use this plugin locally in an existing Laravel Filament project:

1. Add the path repository to your Laravel project's `composer.json`:
```json
"repositories": [
    {
        "type": "path",
        "url": "../form-generator"
    }
],
"require": {
    "akshay/filament-form-generator": "@dev"
}
```

2. Run `composer update akshay/filament-form-generator`.

---

## Register in Your Panel

Add `FilamentFormGeneratorPlugin` to your panel provider (e.g. `app/Providers/Filament/AdminPanelProvider.php`):

```php
use Akshay\FilamentFormGenerator\FilamentFormGeneratorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        // ...
        ->plugins([
            FilamentFormGeneratorPlugin::make()
                ->navigationGroup('Development')
                ->navigationIcon('heroicon-o-wrench-screwdriver')
                ->navigationLabel('Form Generator'),
        ]);
}
```

Visit your panel and click on **Form Generator** in the sidebar.

---

## Configuration & Environment Protection

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag="filament-form-generator-config"
```

By default, the generator is restricted to `local` and `testing` environments:

```php
// config/filament-form-generator.php
'allowed_environments' => [
    'local',
    'testing',
],
```

You can also customize authorization dynamically in your panel provider:

```php
FilamentFormGeneratorPlugin::make()
    ->authorize(fn () => auth()->user()?->isSuperAdmin()),
```

---

## Running Tests

Run the standalone unit test suite:

```bash
php tests/CodeGeneratorTest.php
```

---

## License

The MIT License (MIT).

