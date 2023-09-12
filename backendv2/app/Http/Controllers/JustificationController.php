<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\JustificationService;
use Illuminate\Http\Request;

class JustificationController extends Controller
{
    protected $justificationService;

    public function __construct(JustificationService $justificationService)
    {
        $this->justificationService = $justificationService;
    }

    public function getJustifications(Request $request) {
        $justifications = $this->justificationService->getJustifications($request->all());
        return response()->json($justifications);
    }

    public function createJustifications(Request $request) {
        $justification = $this->justificationService->createJustification($request->all());
        return response()->json(['message' => 'Justificacion creada exitosamente.', 'data' => $justification], 201);
    }



    public function acceptJustifications($id) {
        $justification = $this->justificationService->acceptJustification($id);
        return response()->json(['message' => $justification], 201);
    }

    public function declineJustifications(Request $request, $id) {
        $justification = $this->justificationService->declineJustification($request, $id);
        return response()->json(['message' => $justification], 201);
    }

}
