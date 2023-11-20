<?php

namespace App\Actions\Movies;

use App\Models\MovielistIntegration;
use App\Models\Movies\Movie;
use App\Models\Movies\Producer;
use Illuminate\Support\Facades\DB;

class ImportMoviesAction {
    /**
     * Load the default file
     * Validate the format
     * Skip if already imported
     * Fail if modified
     * Sanitize and import correct data
     * Save journaling
     * @return void
     * @throws \Exception
     */
    public static function execute() {
        $csvFilePath = __DIR__."/../../../storage/app/dataset/movielist.csv";

        if (!file_exists($csvFilePath)) {
            throw new \Exception('storage/app/dataset/movielist.csv not found');
        }


        $csvData = collect(file($csvFilePath))->map(function ($line) {
            return str_getcsv($line, ';');
        });

        $headers = $csvData->shift();

        $expectedHeaders = ['year', 'title', 'studios', 'producers', 'winner'];
        if ($headers != $expectedHeaders) {
            throw new \Exception('Headers not as expected: '.implode(', ', $expectedHeaders));
        }

        /**
         * Prevents the file from being imported repeatedly
         * Also purposely fails if modified as per last requested
         */
        $movieAlreadyImported = MovielistIntegration::query()->first();

        if ($movieAlreadyImported) {
            if ($movieAlreadyImported->hash !== md5($csvData)) {
                throw new \Exception('Default file has been modified');
            }
            return;
        }

        /**
         * for each row as a movie
         * save the movie
         * save each producer individually
         */
        DB::transaction(function() use ($csvData, $csvFilePath) {
            foreach ($csvData as $row) {
                /**
                 * @var $movie Movie
                 */
                $movie = Movie::query()->create([
                    'year' => $row[0],
                    'title' => $row[1],
                    'studios' => $row[2],
                    'winner' => $row[4] === 'yes',
                ]);

                /**
                 * creates a record for each producer
                 */
                $producers_names = $row[3];
                $name_split = explode(" and ", $producers_names);
                $producers = explode(",", $name_split[0]);

                if (isset($name_split[1])) {
                    $producers[] = $name_split[1];
                }

                foreach ($producers as $producer_name) {
                    // prevents empty entries
                    // such cases as "Debra Hayward, Tim Bevan, Eric Fellner, and Tom Hooper"
                    $producer_name = trim($producer_name);
                    if (strlen($producer_name) === 0) {
                        continue;
                    }
                    $producer = Producer::query()->firstOrCreate([
                        'name' => $producer_name,
                    ], [
                        'name' => $producer_name,
                    ]);
                    $movie->producers()->attach($producer);
                }
            }
            MovielistIntegration::query()->create([
                'file' => realpath($csvFilePath),
                'hash' => md5($csvData),
            ]);
        });
    }
}
