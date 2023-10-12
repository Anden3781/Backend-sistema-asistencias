<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Evaluation;
use App\Models\ModelHasRole;
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

    public function storeEvaluationNotes(array $data, $id){
        // Buscar si ya existen notas para la evaluación con el ID dado
        $existingEvaluation = Evaluation::find($id);
        //$user_id = auth()->id();

        if (!$existingEvaluation) {
            // Ya existen notas para esta evaluación, devolver un mensaje de error
            return response()->json(['message' => 'Esta evaluacion no existe']);
        } else {

            //Guardamos la información en la evaluacion
            $existingEvaluation->softskills = $data['softskills'];
            $existingEvaluation->performance = $data['performance'];
            $existingEvaluation->hardskills = $data['hardskills'];
            $existingEvaluation->autoevaluation = $data['autoevaluation'];

            //Accedemos al rol del usuario logueado
            $roleId = ModelHasRole::where('model_id', $existingEvaluation->user_id)->get('role_id');
            $rol = $roleId[0]->role_id;

            //Calculamos el promedio en base a los roles del usuario logueado
            if ($rol == 2) {
                $prom = $existingEvaluation->autoevaluation;
            } elseif ($rol == 3) {
                $prom = ($existingEvaluation->hardskills + $existingEvaluation->performance +  $existingEvaluation->softskills) / 3;
            }

            //Guardamos la informacion en base de datos
            $existingEvaluation->save();

            //Retornamos la respuesta en formato JSON
            return response()->json(['message' => 'Notas de evaluación registradas con éxito', 'data' => $existingEvaluation, 'prom' => $prom]);
        }   
    }
    
}
