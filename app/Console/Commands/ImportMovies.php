<?php

namespace App\Console\Commands;

use App\Models\Movies\Movie;
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
        $csvFilePath = $this->ask('Enter the path to the CSV file');

        if (!file_exists($csvFilePath)) {
            $this->error('File not found. Please provide a valid file path.');
            return;
        }

        $csvData = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($csvFilePath));

        if (count($csvData) === 0) {
            $this->error('CSV file is empty.');
            return;
        }

        $header = array_map('trim', array_shift($csvData));

        if (!$this->validateCsvHeader($header)) {
            $this->error('Invalid CSV structure. The CSV file should have columns: year, title, studios, producers, winner.');
            return;
        }

        $this->info('Importing movies from the CSV file...');

        DB::transaction(function() use ($csvData) {
            foreach ($csvData as $row) {
                Movie::create([
                    'year' => $row[0],
                    'title' => $row[1],
                    'studios' => $row[2],
                    'producers' => $row[3],
                    'winner' => $row[4] === 'yes',
                ]);
            }
        });

        $this->info('Movies imported successfully.');
    }

    protected function validateCsvHeader(array $header): bool
    {
        $expectedHeader = ['year', 'title', 'studios', 'producers', 'winner'];
        return count($header) === count($expectedHeader) && empty(array_diff($expectedHeader, $header));
    }
}
