<?php

namespace Tests\Feature\Movies;

use App\Models\Movies\Movie;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AwardsIntervalTest extends TestCase
{
    use RefreshDatabase;

    public function test_min_max_awards_intervals_show(): void
    {
        /**
         * should get producer one as minimum and producer 2 as maximum
         */
        Movie::factory()->createMany([
            ["year" => 2000, "producers" => "Producer 1", "winner" => true],
            ["year" => 2008, "producers" => "Producer 1", "winner" => true],
            ["year" => 2009, "producers" => "Producer 1", "winner" => true],
            ["year" => 1950, "producers" => "Producer 2", "winner" => true],
            ["year" => 2000, "producers" => "Producer 2", "winner" => true],
            ["winner" => false],
            ["winner" => false],
            ["winner" => false],
        ]);

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

        $data = $response->json();

        $this->assertCount(1, $data['min']);
        $this->assertCount(1, $data['max']);

        $expectedMinValue = [
            'producer' => "Producer 1",
            'interval' => 1,
            'previousWin' => 2008,
            'followingWin' => 2009,
        ];

        $this->assertEquals($expectedMinValue, $response->json()['min'][0]);

        $expectedMaxValue = [
            'producer' => "Producer 2",
            'interval' => 50,
            'previousWin' => 1950,
            'followingWin' => 2000,
        ];

        $this->assertEquals($expectedMaxValue, $response->json()['max'][0]);
    }
}
