<?php

namespace Movies;

use App\Actions\Movies\ImportMoviesAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportMoviesActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_movies_successfully(): void
    {
        ImportMoviesAction::execute();
        $this->assertDatabaseCount('movielist_integrations', 1);
        $this->assertDatabaseCount('movies', 206);
    }
}
