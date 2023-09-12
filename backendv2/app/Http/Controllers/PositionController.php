<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Services\PositionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    protected $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }

    public function getProfiles()
    {
        $profiles = $this->positionService->getAllPositions();

        $combinedProfiles = array_reduce($profiles, function ($carry, $profileCollection) {
            return array_merge($carry, $profileCollection->all());
        }, []);

        return $combinedProfiles;
    }



    public function createProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:positions',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $profile = $this->positionService->createPosition($request->all());
        if (!$profile) {
            return response()->json(['message' => 'Nucleo no encontrado.'], 404);
        }
        return response()->json(['message' => 'Perfil creado exitosamente.', 'data' => $profile], 201);
    }

    public function updateProfile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'string',
                'max:255',
                Rule::unique('positions')->ignore($id),
            ],
            'department_id' => 'integer|exists:departments,id',
            'core_id' => 'integer|exists:cores,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $updated = $this->positionService->updatePosition($id, $request->all());
        if (!$updated) {
            return response()->json(['message' => 'Nucleo no encontrado o perfil no encontrado.'], 404);
        }
        return response()->json(['message' => 'Perfil actualizado exitosamente.']);
    }



    public function deleteProfile($id)
    {
        $deleted = $this->positionService->deletePosition($id);
        if (!$deleted) {
            return response()->json(['message' => 'Perfil no encontrado.'], 404);
        }
        return response()->json(['message' => 'Perfil eliminado exitosamente.']);
    }
}