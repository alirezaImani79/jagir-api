<?php

namespace App\Http\Controllers;

use App\Models\IranCity;
use App\Models\IranProvince;
use Illuminate\Support\Facades\Cache;

class ConfigController
{
    public function provinces()
    {
        return Cache::remember('config:provinces', 60 * 60 * 24 * 30, function() {
            return response()->json(IranProvince::all());
        });
    }

    public function cities(int $provinceId)
    {
        IranProvince::findOrFail($provinceId);
        return Cache::remember(sprintf('config:provinces-%d:cities', $provinceId), 60 * 60 * 24 * 30, function() use ($provinceId) {
            return response()->json(IranCity::where('province_id', $provinceId)->get());
        });
    }
}
