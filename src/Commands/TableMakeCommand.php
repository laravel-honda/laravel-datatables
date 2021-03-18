<?php

namespace Honda\Table\Commands;

use Illuminate\Console\Command;

class TableMakeCommand extends Command
{
    /** @var string */
    protected $signature = 'make:table {model}';

    /** @var string */
    protected $description = 'Creates a table programmatically';

    public function handle(): void
    {
        $path = config('tables.path') . $this->getModel() . 'Table.php';

        if (file_exists($path)) {
            $this->error('View already exists!');

            return;
        }

        file_put_contents($path, $this->getTableContents());

        $this->info('Table created successfully.');
    }

    public function getModel(): string
    {
        $model = ucfirst($this->argument('model'));

        if ($model !== 'Table' && str_ends_with($model, 'Table')) {
            return substr($model, 0, -5);
        }

        return $model;
    }

    public function getTableContents(): string
    {
        $model = $this->getModel();
        $class = $model . 'Table';
        $tableNamespace = config('tables.namespace');
        $modelNamespace = config('tables_model_namespace');

        return <<<EOF
<?php

namespace $tableNamespace;

use Starts\Table\Table;
use $modelNamespace\\$model;

class {$class} extends Table
{
    public string \$model = $model::class;

    public function columns(): array {
        return [
            //
        ];
    }

    public function actions(): array {
        return [
            //
        ];
    }
}
EOF;
    }
}
