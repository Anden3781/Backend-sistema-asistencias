<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attendance;
use App\Repositories\AttendanceRepositories\AttendanceRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Justification;
use App\Models\User;

class AttendanceService {
    protected $attendanceRepository;

    public function __construct(AttendanceRepositoryInterface $attendanceRepository) {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function getFilteredAttendances(array $filters): LengthAwarePaginator
    {
        return Attendance::filter($filters)->paginate(10);
    }

    private function isLateForCheckIn($stime) {
        $currentTime = now();

        // if ($currentTime->format('H:i') > '13:00') {
        //     $checkInLimit = new DateTime('14:11', new DateTimeZone('America/Lima'));
        // } else {
        //     $checkInLimit = new DateTime('08:11', new DateTimeZone('America/Lima'));
        // }
        
        //$checkInTime = new DateTime($checkInTime, new DateTimeZone('America/Lima'));

        return $currentTime > $stime;
    }

    private function uploadImage($image) {
        // Subir imagen al servidor
        $file = $image;
        $folderName = date("Y-m-d");
        $path = "attendances/" . $folderName;
        $filename = time() . "-" . $file->getClientOriginalName();
        $file->move($path, $filename);

        return $path . "/" . $filename;
    }

    private function hasJustification() {
        $flag = 2;
        $today = date('Y-m-d');
        $authUser = auth()->user();

        $justificationExists = Justification::where('user_id', $authUser->id)
            ->whereDate('justification_date', $today)
            ->first();

        //print($justificationExists);
        //exit();

        if (!is_null($justificationExists->justification_date_end)){
            $user = User::where('id', $justificationExists->user_id);
            if ($user) {
                $user->update(['status' => false]);
            }
        } else {
            if (is_null($justificationExists)){
                return $flag;
            } else {
                return $justificationExists->type; // 0 | 1
            }
        }
    }

    public function store($data, $stime, $etime)
    {
        $authUser = auth()->id();
        $currentTime = now();
        
        //Validamos si ya hay un registro
        $attendance = Attendance::where('user_id', $authUser)
            ->whereDate('date', $currentTime->toDateString())
            ->firstOrNew();

        //Validacion de base de datos
        if ($attendance->attendance == 0 && $attendance->delay == 0) {
            $this->updateCheckIn($attendance, $stime, $data['admission_image']);
        } else {
            $this->updateCheckOut($attendance, $data['departure_image']);
        }

        return $attendance;
    }
    
    protected function updateCheckIn($attendance, $stime, $imagePath)
    {
        $authUser = auth()->id();
        $currentTime = now();

        //Asignamos los datos a los campos del attendance
        $attendance->admission_time = $currentTime->format('H:i');
        $attendance->admission_image = $this->uploadImage($imagePath);
        $attendance->user_id = $authUser;
        $attendance->date = $currentTime->format('Y-m-d');
    
        if ($this->isLateForCheckIn($stime)) {
            $type = $this->hasJustification();

            if ($type == 2) {
                $attendance->delay = 1;
            } else {
                $attendance->justification = 1;
                $attendance->delay = 1;
            }
            
        } else {
            $attendance->attendance = 1;
        }

        $attendance->save();
    }

    protected function updateCheckOut($attendance, $imagePath)
    {   
        $currentTime = now();

        $attendance->departure_time = $currentTime->format('H:i');
        $attendance->departure_image = $this->uploadImage($imagePath);
        $attendance->save();
    }

}
