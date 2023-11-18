<?php

namespace Tests\Feature\Movies;

use App\Models\Movies\Movie;

use App\Models\Movies\Producer;
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
        $movies = Movie::factory()->createMany([
            ["year" => 2000, "winner" => true],
            ["year" => 2008, "winner" => true],
            ["year" => 2009, "winner" => true],
            ["year" => 1950, "winner" => true],
            ["year" => 2000, "winner" => true],
            ["winner" => false],
            ["winner" => false],
            ["winner" => false],
        ]);

        $producers = Producer::factory()->createMany(3);
        $movies[0]->producers()->attach($producers[0]);
        $movies[1]->producers()->attach($producers[0]);
        $movies[2]->producers()->attach($producers[0]);
        $movies[3]->producers()->attach($producers[1]);
        $movies[4]->producers()->attach($producers[1]);
        $movies[5]->producers()->attach($producers[2]);

        $response = $this->get('api/movies/awards-intervals');

        $response->assertOk();

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
            'producer' => $producers[0]->name,
            'interval' => 1,
            'previousWin' => 2008,
            'followingWin' => 2009,
        ];

        $this->assertEquals($expectedMinValue, $response->json()['min'][0]);

        $expectedMaxValue = [
            'producer' => $producers[1]->name,
            'interval' => 50,
            'previousWin' => 1950,
            'followingWin' => 2000,
        ];

        $this->assertEquals($expectedMaxValue, $response->json()['max'][0]);
    }
}
