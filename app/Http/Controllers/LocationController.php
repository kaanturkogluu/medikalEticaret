<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\District;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getDistricts(Province $province)
    {
        return response()->json($province->districts()->orderBy('name')->get());
    }

    public function getNeighborhoods(District $district)
    {
        return response()->json($district->neighborhoods()->orderBy('name')->get());
    }
}
