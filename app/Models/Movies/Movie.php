<?php

namespace App\Models\Movies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'title',
        'studios',
        'producers',
        'winner',
    ];

    public static function getMinMaxAwardIntervals() {
        $min = [];
        $max = [];

        $ranking = DB::select("
        WITH PRODUCERS_RANK AS
        (SELECT producers as producer,
                previousWin,
                followingWin, interval
            FROM
                (SELECT producers,
                        YEAR previousWin,
                        LEAD(YEAR) OVER (PARTITION BY PRODUCERS ORDER BY YEAR) followingWin,
                        LEAD(YEAR) OVER (PARTITION BY PRODUCERS ORDER BY YEAR) - YEAR AS interval,
                        COUNT(*) OVER (PARTITION BY PRODUCERS ORDER BY YEAR DESC) AS totalAwards
                    FROM MOVIES
                    WHERE WINNER = true
                    ORDER BY producers,
                        YEAR) SUBQ
            WHERE totalAwards > 1 )
    SELECT *,
        'min' AS RANKING
    FROM PRODUCERS_RANK
    WHERE interval =
            (SELECT MAX(interval)
                FROM PRODUCERS_RANK)
    UNION ALL
    SELECT *,
        'max' AS RANKING
    FROM PRODUCERS_RANK
    WHERE interval =
            (SELECT MIN(interval)
                FROM PRODUCERS_RANK)
        ");

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
