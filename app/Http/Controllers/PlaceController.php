<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Requests\PlaceCreateRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PlaceController extends Controller
{
    public function index(): JsonResponse
    {
        $places = Place::all();
        return response()->json(['data' => PlaceResource::collection($places)], 200);
    }

    public function store(PlaceCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $place = Place::create($data);
        return (new PlaceResource($place))->response()->setStatusCode(201);
    }

    public function show($id): JsonResponse
    {
        $place = Place::findOrFail($id);
        return (new PlaceResource($place))->response()->setStatusCode(200);
    }

    public function update(PlaceUpdateRequest $request, $id): JsonResponse
    {
        $place = Place::findOrFail($id);
        if ($place->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validated();
        $place->update($data);
        return (new PlaceResource($place))->response()->setStatusCode(200);
    }

    public function destroy($id): JsonResponse
    {
        $place = Place::findOrFail($id);
        if ($place->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $place->delete();
        return response()->json(null, 204);
    }

    

}
