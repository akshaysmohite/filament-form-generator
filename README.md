# Filament Visual Form Generator Plugin

A developer-focused, visual drag-and-drop form schema builder and code generator for **Filament v3**.

Build form schemas visually and copy clean Filament PHP (`Forms\Components\...`) into your Resources, Pages, or Livewire components.

---

## Requirements

| Package | Version |
|--------|---------|
| **PHP** | 8.1, 8.2, or 8.3 |
| **Laravel** | 10.x or 11.x |
| **Filament** | **3.2+** (tested on 3.3.x) |

This release targets **Filament 3 only** (not Filament 4 or 5).

---

## Features

- Visual drag-and-drop canvas with reordering and nesting
- Layout containers: `Section`, `Grid`
- Fields: `TextInput` (text, email, numeric, password, tel, url), `Textarea`, `Select`, `Toggle`, `Checkbox`, `Radio`, `DatePicker`
- Properties inspector for labels, names, validation, column span, and options
- PHP code export (array schema or full `form()` method; imported classes or FQCN)
- Copy to clipboard, JSON import/export, full-screen workspace
- Restricted to `local` / `testing` by default (configurable)

---

## Installation

### Packagist (production)

```bash
composer require akshay/filament-form-generator
```

### Path repository (local development)

In your Laravel app `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../form-generator",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "akshay/filament-form-generator": "@dev"
}
```

Then:

```bash
composer update akshay/filament-form-generator
php artisan filament:assets
```

Publish assets whenever you upgrade the plugin or change its JavaScript.

---

## Register in your panel

In `app/Providers/Filament/AdminPanelProvider.php` (or your panel provider):

```php
use Akshay\FilamentFormGenerator\FilamentFormGeneratorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentFormGeneratorPlugin::make()
                ->navigationGroup('Development')
                ->navigationIcon('heroicon-o-wrench-screwdriver')
                ->navigationLabel('Form Generator'),
        ]);
}
```

Open your panel and use **Form Generator** in the sidebar.

---

## Configuration

Optional config publish:

```bash
php artisan vendor:publish --tag="filament-form-generator-config"
```

By default the page is only available in `local` and `testing`:

```php
// config/filament-form-generator.php
'allowed_environments' => [
    'local',
    'testing',
],
```

Allow all environments with `['*']`, or gate access in the panel provider:

```php
FilamentFormGeneratorPlugin::make()
    ->authorize(fn () => auth()->user()?->isSuperAdmin());
```

---

## Tests

```bash
php tests/CodeGeneratorTest.php
```

---

## License

MIT
