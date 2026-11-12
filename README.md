# Filament Form Generator

Visual drag-and-drop builder that writes Filament form PHP for developers. Arrange fields in the panel, then copy a `Forms\Components` schema into a Resource, Page, or Livewire component.

**This free release supports Filament v3.2+ on Laravel 10 and 11.** Filament 4 and 5 are not part of this version.

It generates code you paste into your app. It does not store form submissions or publish a public form URL.

---

## Requirements

| Package | Version |
|--------|---------|
| **PHP** | 8.1, 8.2, or 8.3 |
| **Laravel** | 10.x or 11.x |
| **Filament** | **3.2+** (tested on 3.3.x) |

---

## Features

- Drag-and-drop canvas with reordering and nesting
- Layout: `Section`, `Grid`
- Fields: `TextInput` (text, email, numeric, password, tel, url), `Textarea`, `Select`, `Toggle`, `Checkbox`, `Radio`, `DatePicker`
- Inspector for labels, names, validation, column span, and options
- PHP export as a schema array or a full `form()` method, with imported classes or FQCN
- Copy to clipboard, JSON import/export, full-screen workspace
- Available in `local` and `testing` by default

---

## Installation

```bash
composer require akshay/filament-form-generator
php artisan filament:assets
```

Run `php artisan filament:assets` again after each upgrade so the builder script stays current.

Register the plugin in `app/Providers/Filament/AdminPanelProvider.php`:

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
                ->navigationLabel('Filament Form Generator'),
        ]);
}
```

Open the panel and choose **Filament Form Generator**. On a default panel the page is `/admin/filament-form-generator`.

---

## Configuration

```bash
php artisan vendor:publish --tag="filament-form-generator-config"
```

The page is limited to `local` and `testing`:

```php
// config/filament-form-generator.php
'allowed_environments' => [
    'local',
    'testing',
],
```

Use `['*']` to allow every environment, or authorize in the panel provider:

```php
FilamentFormGeneratorPlugin::make()
    ->authorize(fn () => auth()->user()?->isSuperAdmin());
```

---

## Planned Pro edition

This package is free while we see whether developers want a fuller builder. Pro would add the pieces that are slow to write by hand:

- Repeater and Builder
- Wizard, Tabs, and Fieldset
- File upload, rich editor, markdown, tags, key-value, and color picker
- Relationship selects
- Conditional visibility

If you would use those, open a GitHub issue titled **Pro interest** and name the fields you need. The planned price is **$19 once**, after those features exist. A Filament 4 build would be a separate release.

---

## Local development

Point a Laravel app at this checkout:

```json
"repositories": [
    {
        "type": "path",
        "url": "../filament-form-generator",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "akshay/filament-form-generator": "@dev"
}
```

```bash
composer update akshay/filament-form-generator
php artisan filament:assets
```

The sibling app `filament-form-generator-demo` is the local preview. Sign in with `preview@example.com` / `password123`, then open `/admin/filament-form-generator`.

---

## Tests

```bash
php tests/CodeGeneratorTest.php
```

---

## License

MIT. See [LICENSE.md](LICENSE.md).
