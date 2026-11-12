<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation Settings
    |--------------------------------------------------------------------------
    |
    | Configure how Filament Form Generator appears in your Filament panel sidebar.
    |
    */
    'navigation' => [
        'group' => 'Development',
        'icon' => 'heroicon-o-wrench-screwdriver',
        'sort' => 99,
        'label' => 'Filament Form Generator',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Environments
    |--------------------------------------------------------------------------
    |
    | Define in which environments the generator should be accessible.
    | By default, restricted to local and testing for security.
    | Set to ['*'] to allow in all environments.
    |
    */
    'allowed_environments' => [
        'local',
        'testing',
    ],

    /*
    |--------------------------------------------------------------------------
    | Code Generation Options
    |--------------------------------------------------------------------------
    |
    | - import_style: 'imported' (e.g. TextInput::make) or 'fqcn' (e.g. Forms\Components\TextInput::make)
    | - indent_spaces: Number of spaces for code indentation (default: 4)
    |
    */
    'code_generation' => [
        'import_style' => 'imported',
        'indent_spaces' => 4,
    ],
];

