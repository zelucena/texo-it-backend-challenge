<?php

namespace App\Providers;

use App\Actions\Movies\ImportMoviesAction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ImportMoviesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            ImportMoviesAction::execute();
        } catch (QueryException $e) {
            /**
             * this service is always fired even if the database is not available, yet
             * we can run the migrations by acknowledging this exception
             * other exceptions such as "database does not exist" will still bleed
             */
            Log::warning($e);
            return;
        }
    }
}
