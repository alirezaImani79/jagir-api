<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\IranCity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccommodationController
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $nameFilter = $request->input('name', null);
        $provinceIdsFilter = $cityIdsFilter = [];
        if($request->input('province_ids')){
            $provinceIdsFilter = explode(',', $request->input('province_ids'));
        }
        if($request->input('city_ids')){
            $cityIdsFilter = explode(',', $request->input('city_ids'));
        }

        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        abort_if(! in_array($orderBy, ['created_at', 'price_per_day']) || ! in_array($orderDirection, ['desc', 'asc']), 400, 'ORDER_PARAMETERS_ARE_NOT_VALID');

        $owner = null;
        if($request->input('owner_id', null)) {
            $owner = User::findOrFail($request->input('owner_id'));
        }

        $accommodations = Accommodation::when($nameFilter, function(Builder $query) use ($nameFilter) {
            $query->where('name', 'like', "%$nameFilter%");
        })
        ->when(count($provinceIdsFilter) > 0, function(Builder $query) use ($provinceIdsFilter) {
            $query->whereIn('province_id', $provinceIdsFilter);
        })
        ->when(count($cityIdsFilter) > 0, function(Builder $query) use ($cityIdsFilter) {
            $query->whereIn('city_id', $cityIdsFilter);
        })
        ->when($owner, function(Builder $query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
        ->orderBy($orderBy, $orderDirection)
        ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'meta' => [
                'current_page' => $accommodations->currentPage(),
                'total' => $accommodations->total(),
                'per_page' => $accommodations->perPage(),
                'last_page' => $accommodations->lastPage(),
            ],
            'data' => $accommodations->items()
        ]);
    }

    public function show(Accommodation $accommodation)
    {
        return $accommodation;
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'owner_id' => [Rule::requiredIf(function() {
                return ! auth('sanctum')->user()->hasOneOfRoles(['service_provider']);
            })],
            'province_id' => ['required', 'exists:iran_provinces,id'],
            'city_id' => ['required', 'exists:iran_cities,id'],
            'address' => ['required', 'string'],
            'price_per_day' => ['required', 'integer', 'min:10000'],
            'specifications' => ['nullable', 'array']
        ]);

        $owner = User::find($request->input('owner_id'));
        abort_if(! $owner->hasAnyOfRoles(['service_provider']), 400, 'SPECIFIED_OWNER_IS_NOT_A_SERVICE_PROVIDER');

        abort_if($request->input('province_id') != IranCity::find($request->input('city_id'))->province_id, 400, 'CITY_DOES_NOT_BELONG_TO_PROVINCE');

        $accommodation = Accommodation::create([
            'name' => $request->input('name'),
            'description' => $request->input('description',null),
            'owner_id' => auth('sanctum')->user()->hasOneOfRoles(['service_provider']) ? auth('sanctum')->user()->id : $request->input('owner_id'),
            'city_id' => $request->input('city_id'),
            'province_id' => $request->input('province_id'),
            'address' => $request->input('address'),
            'price_per_day' => $request->input('price_per_day'),
            'specifications' => $request->input('specifications')
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'accommodation' => $accommodation
        ]);
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'price_per_day' => ['required', 'integer', 'min:10000'],
            'address' => ['required', 'string'],
            'specifications' => ['nullable', 'array']
        ]);

        $accommodation->update([
            'name' => $request->input('name'),
            'description' => $request->input('description',null),
            'specifications' => $request->input('specifications'),
            'address' => $request->input('address'),
            'price_per_day' => $request->input('price_per_day'),
            'is_approved' => false
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'accommodation' => $accommodation->refresh()
        ]);
    }

    public function destroy(int $id)
    {
        Accommodation::findOrFail($id)->delete();

        return response()->json([
            'status' => 'SUCCESS'
        ]);
    }
}
