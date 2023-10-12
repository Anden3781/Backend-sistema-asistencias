<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Services\EvaluationService;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{     
    protected $evaluationService;

    public function __construct(EvaluationService $evaluationService) {
        $this->evaluationService = $evaluationService;
    }

    public function getEvaluations()
    {
        return $this->evaluationService->getAllEvaluations();
    }

    public function createEvaluation(Request $request)
    {
        $evaluation = $this->evaluationService->createEvaluations($request->all());
        return response()->json(['message' => 'Evaluacion registrada con exito', 'data' => $evaluation]);
    }

    public function searchEvaluationById($id){
        $evaluation = Evaluation::find($id);

        if ($evaluation) { 
            return response()->json(['data' => $evaluation]);
        } else {  
            return response()->json(['Esta evaluacion no existe, porfavor ingrese otro ID']);
        }
    }

    public function searchEvaluationByUser($userId){
        $evaluation = Evaluation::where('user_id', $userId)->get();

        if (!is_null($evaluation)) { 
            return response()->json(['data' => $evaluation]);
        } else {  
            return response()->json(['message' => 'Esta usuario no tiene asignado ninguna evaluacion']);
        }
    }

    public function storeNotes(Request $request, $id)
    {
        $evaluation = $this->evaluationService->storeEvaluationNotes($request->all(), $id);

        return $evaluation;
    }
}
