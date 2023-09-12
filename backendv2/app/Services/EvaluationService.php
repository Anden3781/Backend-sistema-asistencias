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
        $eva = Evaluation::with(['user', 'evaluationType', 'notes'])->get();
        return $eva;
    }

    public function createEvaluations(array $data) {
        $date = date('Y-m-d');
        $data['date'] = $date; 
        return $this->evaluationRepository->create($data);
    }

    public function storeEvaluationNotes(array $data, $id) {
        // Buscar si ya existe una nota para la evaluación con el ID dado
        $existingNote = Note::where('evaluation_id', $id)->first();
    
        if ($existingNote) {
            // Ya existe una nota para esta evaluación, devolver un mensaje de error
            return response()->json(['message' => 'Ya se ha registrado una nota para esta evaluación']);
        }
    
        // Si no existe una nota, crear una nueva
        $newNote = new Note();
        $newNote->evaluation_id = $id;
        $newNote->note = $data['note'];
        $newNote->save();
    
        return response()->json(['message' => 'Nota registrada con exito', 'data' => $newNote]);
    }
    
}
