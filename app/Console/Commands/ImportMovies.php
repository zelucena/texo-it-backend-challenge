<?php

namespace App\Console\Commands;

use App\Models\Movies\Movie;
use App\Models\Movies\Producer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import movies from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvFilePath = $this->ask('Enter the absolute path to the CSV file');

        if (!file_exists($csvFilePath)) {
            $this->error('File not found. Please type again.');
            return;
        }

        $csvData = collect(file($csvFilePath))->map(function ($line) {
            return str_getcsv($line, ';');
        });

        if (count($csvData) === 0) {
            $this->error('CSV file is empty.');
            return;
        }

        $csvData->shift(); // remove header


        $this->info('Importing movies from the CSV file...');

        DB::transaction(function() use ($csvData) {
            foreach ($csvData as $row) {
                /**
                 * creates a record for each producer
                 */
                $producers_names = $row[3];
                $name_split = explode(" and ", $producers_names);
                $producers = explode(",", $name_split[0]);

                if (isset($name_split[1])) {
                    $producers[] = $name_split[1];
                }

                /**
                 * @var $movie Movie
                 */
                $movie = Movie::query()->create([
                    'year' => $row[0],
                    'title' => $row[1],
                    'studios' => $row[2],
                    'winner' => $row[4] === 'yes',
                ]);

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
        });

        $this->info('Movies imported successfully.');
    }
}
