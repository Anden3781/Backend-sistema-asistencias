<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class BirthdayController extends Controller
{
    //
    public function getbirthday(){
        return response()->json(User::all('birthday'),200);
    }

    public function detailsbirthdayMonth(Request $request) {
        $month = $request->input('m'); // Obtener el valor del parámetro "m" (mes)
        $day = $request->input('d');   // Obtener el valor del parámetro "d" (día), si está presente

        $query = User::whereMonth('birthday', $month)->with('position.core.department');

        if (!empty($day)) {
            $query->whereDay('birthday', $day);
        }

        $upcomingBirthdays = $query->orderByRaw('DAY(birthday)')->get(); // 'EXTRACT(DAY FROM birthday)'

        // Agregar la URL de la imagen a cada usuario
        foreach ($upcomingBirthdays as $user) {
            $user->image_url = $user->getImageUrlAttribute();
        }

        return response()->json($upcomingBirthdays, 200);
    }

    public function getUpcomingBirthdaysWithUsers() {
        $currentDate = now();
        $userShift = auth()->user()->shift; // Obtener el turno del usuario logeado

        $upcomingBirthdays = User::whereMonth('birthday', $currentDate->month)->with('position.core.department')
            ->whereDay('birthday', '>=', $currentDate->day)
            ->where('shift', $userShift) // Filtrar por turno
            ->orderByRaw('DAY(birthday)') // 'EXTRACT(DAY FROM birthday)'
            ->get();
        // Agregar la URL de la imagen a cada usuario
        foreach ($upcomingBirthdays as $user) {
            $user->image_url = $user->getImageUrlAttribute();
        }

        return response()->json($upcomingBirthdays, 200);
    }

}
