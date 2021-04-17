<?php

namespace Honda\Table;

use Honda\Table\Commands\TableMakeCommand;
use Illuminate\Support\ServiceProvider;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tables');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'tables');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tables');
        $this->mergeConfigFrom(__DIR__ . '/../config/tables.php', 'tables');
        $this->commands([TableMakeCommand::class]);

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
