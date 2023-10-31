<?php

namespace App\Http\Controllers\Movies;

use App\Http\Controllers\Controller;
use App\Models\Movies\Movie;
use Illuminate\Support\Facades\DB;

class AwardsIntervalController extends Controller
{
    public function show()
    {
        return Movie::getMinMaxAwardIntervals();
    }
}
