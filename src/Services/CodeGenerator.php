<?php

namespace Akshay\FilamentFormGenerator\Services;

class CodeGenerator
{
    protected string $importStyle;
    protected int $indentSpaces;
    protected string $indentUnit;

    public function __construct(string $importStyle = 'imported', int $indentSpaces = 4)
    {
        $this->importStyle = $importStyle;
        $this->indentSpaces = $indentSpaces;
        $this->indentUnit = str_repeat(' ', $this->indentSpaces);
    }

    /**
     * Generate complete Filament Form PHP code.
     *
     * @param array $nodes
     * @param bool $wrapInMethod Whether to wrap in `public static function form(Form $form): Form`
     * @return string
     */
    public function generate(array $nodes, bool $wrapInMethod = false): string
    {
        if (empty($nodes)) {
            return "// No components added yet.\n// Drag and drop components from the palette into the canvas.";
        }

        $baseIndent = $wrapInMethod ? 2 : 1;
        $schemaLines = $this->renderNodes($nodes, $baseIndent);

        if (!$wrapInMethod) {
            $imports = $this->renderImports($nodes);
            $output = '';

            if (!empty($imports)) {
                $output .= $imports . "\n\n";
            }

            $output .= "[\n";
            $output .= $schemaLines;
            $output .= "\n]";

            return $output;
        }

        // Wrap in full Filament Resource form method
        $methodIndent = str_repeat($this->indentUnit, 1);
        $bodyIndent = str_repeat($this->indentUnit, 2);

        $code = "use Filament\Forms;\nuse Filament\Forms\Form;\n";
        $imports = $this->renderImports($nodes, true);
        if (!empty($imports)) {
            $code .= $imports . "\n";
        }
        $code .= "\n";
        $code .= "public static function form(Form \$form): Form\n";
        $code .= "{\n";
        $code .= "{$methodIndent}return \$form\n";
        $code .= "{$bodyIndent}->schema([\n";
        $code .= $schemaLines . "\n";
        $code .= "{$bodyIndent}]);\n";
        $code .= "}\n";

        return $code;
    }

    /**
     * Render a list of nodes recursively.
     */
    protected function renderNodes(array $nodes, int $level): string
    {
        $rendered = [];
        $indent = str_repeat($this->indentUnit, $level);

        foreach ($nodes as $node) {
            $rendered[] = $this->renderNode($node, $level);
        }

        return implode(",\n\n", $rendered);
    }

    /**
     * Render an individual node.
     */
    protected function renderNode(array $node, int $level): string
    {
        $type = $node['type'] ?? 'TextInput';
        $indent = str_repeat($this->indentUnit, $level);
        $methodIndent = str_repeat($this->indentUnit, $level + 1);

        $className = $this->resolveClassName($type);

        switch ($type) {
            case 'Section':
                return $this->renderSection($node, $className, $indent, $methodIndent, $level);

            case 'Grid':
                return $this->renderGrid($node, $className, $indent, $methodIndent, $level);

            case 'Fieldset':
                return $this->renderFieldset($node, $className, $indent, $methodIndent, $level);

            case 'TextInput':
                return $this->renderTextInput($node, $className, $indent, $methodIndent);

            case 'Textarea':
                return $this->renderTextarea($node, $className, $indent, $methodIndent);

            case 'Select':
                return $this->renderSelect($node, $className, $indent, $methodIndent, $level);

            case 'Toggle':
                return $this->renderToggle($node, $className, $indent, $methodIndent);

            case 'Checkbox':
                return $this->renderCheckbox($node, $className, $indent, $methodIndent);

            case 'Radio':
                return $this->renderRadio($node, $className, $indent, $methodIndent, $level);

            case 'DatePicker':
                return $this->renderDatePicker($node, $className, $indent, $methodIndent);

            default:
                return $indent . "{$className}::make('" . ($node['name'] ?? 'field') . "')";
        }
    }

    protected function renderSection(array $node, string $class, string $indent, string $mIndent, int $level): string
    {
        $title = var_export($node['title'] ?? 'Section Details', true);
        $lines = ["{$indent}{$class}::make({$title})"];

        if (!empty($node['description'])) {
            $lines[] = "{$mIndent}->description(" . var_export($node['description'], true) . ")";
        }

        if (!empty($node['icon'])) {
            $lines[] = "{$mIndent}->icon('{$node['icon']}')";
        }

        if (!empty($node['collapsible'])) {
            $lines[] = "{$mIndent}->collapsible()";
            if (!empty($node['collapsed'])) {
                $lines[] = "{$mIndent}->collapsed()";
            }
        }

        if (!empty($node['columns']) && (int) $node['columns'] > 1) {
            $lines[] = "{$mIndent}->columns(" . (int) $node['columns'] . ")";
        }

        $children = $node['children'] ?? [];
        if (!empty($children)) {
            $childrenCode = $this->renderNodes($children, $level + 2);
            $lines[] = "{$mIndent}->schema([\n{$childrenCode}\n{$mIndent}])";
        } else {
            $lines[] = "{$mIndent}->schema([])";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderGrid(array $node, string $class, string $indent, string $mIndent, int $level): string
    {
        $cols = (int) ($node['columns'] ?? 2);
        $lines = ["{$indent}{$class}::make({$cols})"];

        $children = $node['children'] ?? [];
        if (!empty($children)) {
            $childrenCode = $this->renderNodes($children, $level + 2);
            $lines[] = "{$mIndent}->schema([\n{$childrenCode}\n{$mIndent}])";
        } else {
            $lines[] = "{$mIndent}->schema([])";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderFieldset(array $node, string $class, string $indent, string $mIndent, int $level): string
    {
        $label = var_export($node['label'] ?? 'Fieldset', true);
        $lines = ["{$indent}{$class}::make({$label})"];

        $children = $node['children'] ?? [];
        if (!empty($children)) {
            $childrenCode = $this->renderNodes($children, $level + 2);
            $lines[] = "{$mIndent}->schema([\n{$childrenCode}\n{$mIndent}])";
        } else {
            $lines[] = "{$mIndent}->schema([])";
        }

        if (!empty($node['columns']) && (int) $node['columns'] > 1) {
            $lines[] = "{$mIndent}->columns(" . (int) $node['columns'] . ")";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderTextInput(array $node, string $class, string $indent, string $mIndent): string
    {
        $name = $node['name'] ?? 'text_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);

        // Subtypes
        $subType = $node['subType'] ?? 'text';
        if ($subType === 'email') {
            $lines[] = "{$mIndent}->email()";
        } elseif ($subType === 'numeric') {
            $lines[] = "{$mIndent}->numeric()";
            if (isset($node['minValue']) && $node['minValue'] !== '') {
                $lines[] = "{$mIndent}->minValue({$node['minValue']})";
            }
            if (isset($node['maxValue']) && $node['maxValue'] !== '') {
                $lines[] = "{$mIndent}->maxValue({$node['maxValue']})";
            }
        } elseif ($subType === 'password') {
            $lines[] = "{$mIndent}->password()";
            $lines[] = "{$mIndent}->revealable()";
        } elseif ($subType === 'tel') {
            $lines[] = "{$mIndent}->tel()";
        } elseif ($subType === 'url') {
            $lines[] = "{$mIndent}->url()";
        }

        if (isset($node['maxLength']) && is_numeric($node['maxLength']) && (int) $node['maxLength'] > 0) {
            $lines[] = "{$mIndent}->maxLength(" . (int) $node['maxLength'] . ")";
        }

        if (isset($node['minLength']) && is_numeric($node['minLength']) && (int) $node['minLength'] > 0) {
            $lines[] = "{$mIndent}->minLength(" . (int) $node['minLength'] . ")";
        }

        if (!empty($node['prefix'])) {
            $lines[] = "{$mIndent}->prefix(" . var_export($node['prefix'], true) . ")";
        }

        if (!empty($node['suffix'])) {
            $lines[] = "{$mIndent}->suffix(" . var_export($node['suffix'], true) . ")";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderTextarea(array $node, string $class, string $indent, string $mIndent): string
    {
        $name = $node['name'] ?? 'textarea_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);

        if (!empty($node['rows'])) {
            $lines[] = "{$mIndent}->rows(" . (int) $node['rows'] . ")";
        }

        if (isset($node['maxLength']) && is_numeric($node['maxLength']) && (int) $node['maxLength'] > 0) {
            $lines[] = "{$mIndent}->maxLength(" . (int) $node['maxLength'] . ")";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderSelect(array $node, string $class, string $indent, string $mIndent, int $level): string
    {
        $name = $node['name'] ?? 'select_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);

        // Options
        $options = $node['options'] ?? [];
        if (!empty($options)) {
            $optIndent = str_repeat($this->indentUnit, $level + 2);
            $optLines = [];
            foreach ($options as $opt) {
                $val = var_export($opt['value'] ?? '', true);
                $lbl = var_export($opt['label'] ?? '', true);
                $optLines[] = "{$optIndent}{$val} => {$lbl}";
            }
            $optCode = implode(",\n", $optLines);
            $lines[] = "{$mIndent}->options([\n{$optCode},\n{$mIndent}])";
        }

        if (!empty($node['searchable'])) {
            $lines[] = "{$mIndent}->searchable()";
        }

        if (!empty($node['multiple'])) {
            $lines[] = "{$mIndent}->multiple()";
        }

        if (!empty($node['native']) === false && isset($node['native'])) {
            $lines[] = "{$mIndent}->native(false)";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderToggle(array $node, string $class, string $indent, string $mIndent): string
    {
        $name = $node['name'] ?? 'toggle_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);

        if (!empty($node['onIcon'])) {
            $lines[] = "{$mIndent}->onIcon('{$node['onIcon']}')";
        }

        if (!empty($node['offIcon'])) {
            $lines[] = "{$mIndent}->offIcon('{$node['offIcon']}')";
        }

        if (!empty($node['onColor'])) {
            $lines[] = "{$mIndent}->onColor('{$node['onColor']}')";
        }

        if (isset($node['inline']) && $node['inline'] === false) {
            $lines[] = "{$mIndent}->inline(false)";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderCheckbox(array $node, string $class, string $indent, string $mIndent): string
    {
        $name = $node['name'] ?? 'checkbox_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);
        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderRadio(array $node, string $class, string $indent, string $mIndent, int $level): string
    {
        $name = $node['name'] ?? 'radio_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);

        $options = $node['options'] ?? [];
        if (!empty($options)) {
            $optIndent = str_repeat($this->indentUnit, $level + 2);
            $optLines = [];
            foreach ($options as $opt) {
                $val = var_export($opt['value'] ?? '', true);
                $lbl = var_export($opt['label'] ?? '', true);
                $optLines[] = "{$optIndent}{$val} => {$lbl}";
            }
            $optCode = implode(",\n", $optLines);
            $lines[] = "{$mIndent}->options([\n{$optCode},\n{$mIndent}])";
        }

        if (!empty($node['inline'])) {
            $lines[] = "{$mIndent}->inline()";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function renderDatePicker(array $node, string $class, string $indent, string $mIndent): string
    {
        $name = $node['name'] ?? 'date_field';
        $lines = ["{$indent}{$class}::make('{$name}')"];

        $this->appendCommonFieldModifiers($node, $lines, $mIndent);

        if (isset($node['native']) && $node['native'] === false) {
            $lines[] = "{$mIndent}->native(false)";
        }

        if (!empty($node['displayFormat'])) {
            $lines[] = "{$mIndent}->displayFormat('{$node['displayFormat']}')";
        }

        $this->appendCommonLayoutModifiers($node, $lines, $mIndent);

        return implode("\n", $lines);
    }

    protected function appendCommonFieldModifiers(array $node, array &$lines, string $mIndent): void
    {
        if (!empty($node['label'])) {
            $lines[] = "{$mIndent}->label(" . var_export($node['label'], true) . ")";
        }

        if (!empty($node['placeholder'])) {
            $lines[] = "{$mIndent}->placeholder(" . var_export($node['placeholder'], true) . ")";
        }

        if (!empty($node['helperText'])) {
            $lines[] = "{$mIndent}->helperText(" . var_export($node['helperText'], true) . ")";
        }

        if (!empty($node['required'])) {
            $lines[] = "{$mIndent}->required()";
        }

        if (isset($node['default']) && $node['default'] !== '') {
            $lines[] = "{$mIndent}->default(" . var_export($node['default'], true) . ")";
        }

        if (!empty($node['disabled'])) {
            $lines[] = "{$mIndent}->disabled()";
        }

        if (!empty($node['readOnly'])) {
            $lines[] = "{$mIndent}->readOnly()";
        }

        if (!empty($node['hidden'])) {
            $lines[] = "{$mIndent}->hidden()";
        }
    }

    protected function appendCommonLayoutModifiers(array $node, array &$lines, string $mIndent): void
    {
        if (isset($node['columnSpan'])) {
            if ($node['columnSpan'] === 'full') {
                $lines[] = "{$mIndent}->columnSpanFull()";
            } elseif (is_numeric($node['columnSpan']) && (int) $node['columnSpan'] > 1) {
                $lines[] = "{$mIndent}->columnSpan(" . (int) $node['columnSpan'] . ")";
            }
        }
    }

    protected function resolveClassName(string $type): string
    {
        if ($this->importStyle === 'fqcn') {
            return "Forms\\Components\\{$type}";
        }

        return $type;
    }

    protected function renderImports(array $nodes, bool $isInsideClass = false): string
    {
        if ($this->importStyle === 'fqcn') {
            return '';
        }

        $usedTypes = [];
        $this->collectComponentTypes($nodes, $usedTypes);

        if (empty($usedTypes)) {
            return '';
        }

        sort($usedTypes);

        $imports = array_map(function ($t) {
            return "use Filament\Forms\Components\\{$t};";
        }, $usedTypes);

        return implode("\n", $imports);
    }

    protected function collectComponentTypes(array $nodes, array &$types): void
    {
        foreach ($nodes as $node) {
            $type = $node['type'] ?? 'TextInput';
            if (!in_array($type, $types, true)) {
                $types[] = $type;
            }

            if (!empty($node['children']) && is_array($node['children'])) {
                $this->collectComponentTypes($node['children'], $types);
            }
        }
    }
}

