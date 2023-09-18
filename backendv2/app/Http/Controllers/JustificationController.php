<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Justification;
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
        $query = $request->input('user');
        
        if ($query == ''){
            // Filtrado por default
            $justifications = Justification::get();
        } else {
            // Utiliza el valor 'query' para filtrar las justificaciones
            $justifications = Justification::where('user_id', '!=', $query)->get();
        }
    
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
