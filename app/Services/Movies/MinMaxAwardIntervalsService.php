<?php

namespace App\Services\Movies;

use Illuminate\Support\Facades\DB;

class MinMaxAwardIntervalsService {
    /**
     * To build this query Eloquent on too many DB::raw statements, so I decided to let it off
     * This syntax is 100% compatible with Sqlite and Postgres
     *
     * @return array[]
     */
    public function getMinMaxAwardIntervals() {
        $ranking = DB::select("
        WITH producers_rank AS
        (SELECT producer,
                previousWin,
                followingWin,
                interval
            FROM
                (
                SELECT
                    producers.name as producer,
                    YEAR previousWin,
                    LEAD(YEAR) OVER (PARTITION BY PRODUCERS.id ORDER BY YEAR) followingWin,
                    LEAD(YEAR) OVER (PARTITION BY PRODUCERS.id ORDER BY YEAR) - YEAR AS interval,
                    COUNT(*) OVER (PARTITION BY PRODUCERS.id ORDER BY YEAR DESC) AS totalAwards
                FROM movies
                    inner join producers_movies on movies.id = producers_movies.movie_id
                    inner join producers on producers.id = producers_movies.producer_id
                WHERE winner = true
                ORDER BY producers.name, year
                ) subq
            WHERE totalAwards > 1 )
        SELECT *,
            'min' AS ranking
        FROM producers_rank
        WHERE interval =
                (SELECT MIN(interval)
                    FROM producers_rank)
        UNION ALL
        SELECT *,
            'max' AS ranking
        FROM producers_rank
        WHERE interval =
                (SELECT MAX(interval)
                    FROM producers_rank)
        ");

        $min = [];
        $max = [];

        collect($ranking)->each(function ($row) use (&$min, &$max) {
            $isMinMax = $row->ranking;
            unset($row->ranking);
            if ($isMinMax === "min") {
                $min[] = $row;
            } else {
                $max[] = $row;
            }
        });

        return ["min" => $min, "max" => $max];
    }
}
