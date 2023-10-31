<?php

namespace Tests\Feature\Movies;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;

class AwardsIntervalTest extends TestCase
{
    use DatabaseMigrations;

    public function test_awards_intervals_show(): void
    {
        $response = $this->get('api/movies/awards-intervals');

        $expectedStructure = [
            'min' => [
                '*' => [
                    'producer',
                    'interval',
                    'previousWin',
                    'followingWin',
                ],
            ],
            'max' => [
                '*' => [
                    'producer',
                    'interval',
                    'previousWin',
                    'followingWin',
                ],
            ],
        ];

        $response->assertJsonStructure($expectedStructure);
    }
}
