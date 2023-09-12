<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Core;
use App\Models\Position;
use App\Repositories\ProfileRepositories\PositionRepositoryInterface;

class PositionService {
    protected $positionRepository;

    public function __construct(PositionRepositoryInterface $positionRepository) {
        $this->positionRepository = $positionRepository;
    }

    public function getAllPositions() {
        $cores = Position::with('core.department')->get();
        return [$cores];
    }

    public function createPosition(array $data) {
        if (Core::find($data['core_id'])) {
            return $this->positionRepository->create($data);
        }
        return null;
    }

    public function updatePosition(int $id, array $data) {
        if (Core::find($data['core_id'])) {
            return $this->positionRepository->update($id, $data);
        }
        return false;
    }

    public function deletePosition(int $id) {
        return $this->positionRepository->delete($id);
    }
}
