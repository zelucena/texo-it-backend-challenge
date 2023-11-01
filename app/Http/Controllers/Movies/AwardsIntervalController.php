<?php

namespace App\Http\Controllers\Movies;

use App\Http\Controllers\Controller;
use App\Services\Movies\MinMaxAwardIntervalsService;
use Illuminate\Http\Response;

class AwardsIntervalController extends Controller
{
    public function show(MinMaxAwardIntervalsService $service)
    {
        $minMax = $service->getMinMaxAwardIntervals();
        return response()->json($minMax);
    }
}
