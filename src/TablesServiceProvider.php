<?php

namespace Honda\Table;

use Honda\Table\Commands\TableMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Symfony\Component\Finder\SplFileInfo;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tables');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'tables');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tables');
        $this->mergeConfigFrom(__DIR__ . '/../config/tables.php', 'tables');

        $this->commands([TableMakeCommand::class]);

        if (File::exists(config('tables.path'))) {
            collect(File::files(config('tables.path')))->each(function (SplFileInfo $file) {
                $alias = strtolower(
                    preg_replace(
                        '/[A-Z]/',
                        '-$0',
                        lcfirst($file->getFilenameWithoutExtension())
                    ) ?? ''
                );

                Livewire::component($alias, config('tables.namespace') . '\\' . $file->getFilenameWithoutExtension());
            });
        }

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/tables'),
        ], 'tables-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/tables'),
        ], 'tables-views');

        $this->publishes([
            __DIR__ . '/../config/tables.php' => config_path('tables.php'),
        ], 'tables-config');
    }
}
