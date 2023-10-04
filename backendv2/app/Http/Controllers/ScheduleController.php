<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Services\AttendanceService;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function getSchedules()
    {
        return Schedule::get();
    }

    public function createSchedule(Request $request)
    {
        $authUser = auth()->id();

        $request['user_id'] = $authUser; 

        $schedule = Schedule::create($request->all());
        
        return response()->json(['message' => 'Horario creado exitosamente.', 'data' => $schedule], 201);
    }

    public function checkAttendance(DateTime $currentTime, Request $data)
    {
        $authUser = auth()->user();

        print($currentTime->format('w'));

        // Obtener el horario del usuario para el día actual
        $schedule = Schedule::where('day_of_week', $currentTime->format('w'))
                            ->where('user_id', $authUser->id)
                            ->first();
    
        if (!$schedule) {
            // No tiene horario definido para el día actual
            return 'Sin horario';
        } else {
            //Llamar a la funcion attendance store
            $attendanceController = app(AttendanceController::class);

            $stime = $schedule->start_time;
            //$etime = $schedule->end_time;

            //Data -> admission_image
            $attendance = $attendanceController->createAttendance($data, $stime);

            //$attendance = $attendanceService->store($data, $stime, $etime);
            return $attendance;
        }
    }
    
}
