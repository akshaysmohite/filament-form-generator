<?php

namespace Akshay\FilamentFormGenerator\Tests;

use Akshay\FilamentFormGenerator\Services\CodeGenerator;

class CodeGeneratorTest
{
    public function runAllTests(): void
    {
        $this->testEmptyNodesReturnsFallback();
        $this->testSingleTextInputGeneratesCorrectCode();
        $this->testSectionWithNestedFields();
        $this->testSelectWithOptions();
        $this->testWrappedInFormMethod();
        $this->testFqcnImportStyle();

        echo "All CodeGenerator tests passed successfully!\n";
    }

    public function testEmptyNodesReturnsFallback(): void
    {
        $generator = new CodeGenerator();
        $code = $generator->generate([]);
        assert(str_contains($code, 'No components added yet'), 'Empty nodes should return fallback message');
    }

    public function testSingleTextInputGeneratesCorrectCode(): void
    {
        $generator = new CodeGenerator('imported', 4);
        $nodes = [
            [
                'id' => 'node_1',
                'type' => 'TextInput',
                'name' => 'email',
                'label' => 'Email Address',
                'subType' => 'email',
                'required' => true,
                'placeholder' => 'user@example.com',
                'columnSpan' => 'full',
            ]
        ];

        $code = $generator->generate($nodes);

        assert(str_contains($code, "use Filament\Forms\Components\TextInput;"), 'Should include use statement');
        assert(str_contains($code, "TextInput::make('email')"), 'Should instantiate TextInput with field name');
        assert(str_contains($code, "->label('Email Address')"), 'Should include label');
        assert(str_contains($code, "->email()"), 'Should include email subtype');
        assert(str_contains($code, "->required()"), 'Should include required modifier');
        assert(str_contains($code, "->columnSpanFull()"), 'Should include columnSpanFull');
    }

    public function testSectionWithNestedFields(): void
    {
        $generator = new CodeGenerator('imported', 4);
        $nodes = [
            [
                'id' => 'sec_1',
                'type' => 'Section',
                'title' => 'User Details',
                'description' => 'Account info',
                'columns' => 2,
                'collapsible' => true,
                'children' => [
                    [
                        'id' => 'f_1',
                        'type' => 'TextInput',
                        'name' => 'username',
                        'required' => true,
                    ]
                ]
            ]
        ];

        $code = $generator->generate($nodes);

        assert(str_contains($code, "Section::make('User Details')"), 'Should instantiate Section');
        assert(str_contains($code, "->description('Account info')"), 'Should include description');
        assert(str_contains($code, "->columns(2)"), 'Should include columns');
        assert(str_contains($code, "->collapsible()"), 'Should be collapsible');
        assert(str_contains($code, "->schema(["), 'Should nest children in schema');
        assert(str_contains($code, "TextInput::make('username')"), 'Should include child component inside section');
    }

    public function testSelectWithOptions(): void
    {
        $generator = new CodeGenerator('imported', 4);
        $nodes = [
            [
                'id' => 'sel_1',
                'type' => 'Select',
                'name' => 'status',
                'searchable' => true,
                'options' => [
                    ['value' => 'active', 'label' => 'Active Account'],
                    ['value' => 'pending', 'label' => 'Pending Review'],
                ]
            ]
        ];

        $code = $generator->generate($nodes);

        assert(str_contains($code, "Select::make('status')"), 'Should instantiate Select');
        assert(str_contains($code, "'active' => 'Active Account'"), 'Should include options key-value');
        assert(str_contains($code, "'pending' => 'Pending Review'"), 'Should include options key-value');
        assert(str_contains($code, "->searchable()"), 'Should include searchable');
    }

    public function testWrappedInFormMethod(): void
    {
        $generator = new CodeGenerator('imported', 4);
        $nodes = [
            [
                'id' => 't_1',
                'type' => 'Toggle',
                'name' => 'is_admin',
            ]
        ];

        $code = $generator->generate($nodes, wrapInMethod: true);

        assert(str_contains($code, 'public static function form(Form $form): Form'), 'Should include method signature');
        assert(str_contains($code, 'return $form'), 'Should return $form');
        assert(str_contains($code, '->schema(['), 'Should call ->schema()');
        assert(str_contains($code, "Toggle::make('is_admin')"), 'Should contain Toggle');
    }

    public function testFqcnImportStyle(): void
    {
        $generator = new CodeGenerator('fqcn', 4);
        $nodes = [
            [
                'id' => 'd_1',
                'type' => 'DatePicker',
                'name' => 'published_at',
            ]
        ];

        $code = $generator->generate($nodes);

        assert(str_contains($code, "Forms\Components\DatePicker::make('published_at')"), 'Should use FQCN prefix');
        assert(!str_contains($code, "use Filament\Forms\Components\DatePicker;"), 'FQCN style should omit use statements');
    }
}

// Standalone runner execution
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'] ?? '')) {
    require_once __DIR__ . '/../src/Services/CodeGenerator.php';
    (new CodeGeneratorTest())->runAllTests();
}

