<?php
namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use App\Repositories\UserRepositories\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function getFilteredUsers(array $filters): LengthAwarePaginator
    {
        $query = User::query()->with('position.core.department', 'roles');

        if (!empty($filters['shift'])) {
            $query->whereHas('position', fn ($q) => $q->where('shift', $filters['shift']));
        }
        if (!empty($filters['position'])) {
            $query->whereHas('position', fn ($q) => $q->where('id', $filters['position']));
        }

        if (!empty($filters['department'])) {
            $query->whereHas('position.core.department', fn ($q) => $q->where('id', $filters['department']));
        }

        if (!empty($filters['name'])) {
            $query->where(function ($q) use ($filters) {
                $q->where(function ($innerQ) use ($filters) {
                    $innerQ->where('name', 'LIKE', '%' . $filters['name'] . '%')
                        ->orWhere('surname', 'LIKE', '%' . $filters['name'] . '%');
                });
            });
        }

        if (!empty($filters['core'])) {
            $query->whereHas('position.core', fn ($q) => $q->where('id', $filters['core']));
        }

        $users = $query->paginate(10);
        foreach ($users as $user) {
            $user->image_url = $user->getImageUrlAttribute();
        }

        return $users;
    }

    public function getUserDetails($id)
    {
        $user = User::with('position.core.department', 'roles')->find($id);

        if (is_null($user)) {
            return null;
        }

        $attendanceData = Attendance::where('user_id', $id)->get();
        $attendance = $attendanceData->where("attendance", "1")->count();
        $absence = $attendanceData->where("absence", "1")->where("justification","0")->count();
        $delay = $attendanceData->where("delay", "1")->where("justification","0")->count();
        $justification = $attendanceData->where("justification", "1")->count();
        $user->image_url = $user->getImageUrlAttribute();


        return [
            "usuario" => $user,
            "Asistencia" => $attendance,
            "Faltas" => $absence,
            "Tardanzas" => $delay,
            "Justificaciones" => $justification,
        ];
    }

    public function update($user, array $data): User
    {
        $user = User::find($user);
        if (isset($data['dni'])) {
            $data['password'] = Hash::make($data['dni']);
            $data['username'] = $data['dni'];
        }

        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role) {
                $user->syncRoles([$role->name]); // Asignar el nuevo rol
            }
        }

        if (isset($data['image'])) {
            $archivo = $data['image'];

            $ruta = 'photos/' . $user->id; // Ruta con el ID del usuario
            $nombreArchivo = $archivo->getClientOriginalName();
            $archivo->move($ruta, $nombreArchivo);

            if ($user->image && $user->image !== $nombreArchivo) {
                $ruta_anterior = public_path('photos/' . $user->id . '/' . $user->image);
                if (file_exists($ruta_anterior)) {
                    unlink($ruta_anterior); // Eliminar la imagen anterior
                }
            }

            $data['image'] = $nombreArchivo;
        }
        $user->update($data);

        return $user;

    }
}
