<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Evaluation;
use App\Models\Note;
use App\Repositories\EvaluationRepositories\EvaluationRepositoryInterface;

class EvaluationService {
    protected $evaluationRepository;

    public function __construct(EvaluationRepositoryInterface $evaluationRepository) {
        $this->evaluationRepository = $evaluationRepository;
    }

    public function getAllEvaluations() {
        $eva = Evaluation::with(['user'])->get();
        return $eva;
    }

    public function createEvaluations(array $data) {
        $date = date('Y-m-d');
        $data['date'] = $date; 
        return $this->evaluationRepository->create($data);
    }

    public function storeEvaluationNotes(array $data, $id) {
        // Buscar si ya existen notas para la evaluación con el ID dado
        $existingEvaluation = Evaluation::find($id);
        if ($existingEvaluation) {
            // Ya existen notas para esta evaluación, devolver un mensaje de error
            return response()->json(['message' => 'Ya se han registrado notas para esta evaluación']);
        }
        // Si no existen notas, actualizar la evaluación con las nuevas notas
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->softskills = $data['softskills'];
        $evaluation->performance = $data['performance'];
        $evaluation->hardskills = $data['hardskills'];
        $evaluation->autoevaluation = $data['autoevaluation'];
        $evaluation->save();
        return response()->json(['message' => 'Notas de evaluación registradas con éxito', 'data' => $evaluation]);
    }
    
}
