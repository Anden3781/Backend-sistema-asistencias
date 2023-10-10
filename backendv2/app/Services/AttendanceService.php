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

class AttendanceService {
    protected $attendanceRepository;

    public function __construct(AttendanceRepositoryInterface $attendanceRepository) {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function getFilteredAttendances(array $filters): LengthAwarePaginator
    {
        return Attendance::filter($filters)->paginate(10);
    }

    private function isLateForCheckIn($checkInTime) {
        $currentTime = now();

        if ($currentTime->format('H:i') > '13:00') {
            $checkInLimit = new DateTime('14:11', new DateTimeZone('America/Lima'));
        } else {
            $checkInLimit = new DateTime('08:11', new DateTimeZone('America/Lima'));
        }

        $checkInTime = new DateTime($checkInTime, new DateTimeZone('America/Lima'));

        return $checkInTime > $checkInLimit;
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
            ->whereDate('justification_date', $today) //Falta condicional del status != 3
            ->first('type');

        if (is_null($justificationExists)){
            return $flag;
        } else {
            return $justificationExists->type; // 0 | 1
        }
    }

    public function store(array $data)
    {
        $authUser = auth()->id();
        $currentTime = now();
        $today = date('Y-m-d');

        $attendance = Attendance::where('user_id', $authUser)
            ->whereDate('date', $today)
            ->firstOrNew();

        if ($attendance->attendance == 0 && $attendance->delay == 0) { //Validacion de base de datos
            if ($attendance->admission_time != '00:00:00') {
                $this->updateCheckOut($attendance, $currentTime, $data['departure_image']);
            } else {
                $this->updateCheckIn($attendance, $currentTime, $data['admission_image'], $authUser);
            }
        } else {
            $this->updateCheckOut($attendance, $currentTime, $data['departure_image']);
        }

        return $attendance;
    }
    
    protected function updateCheckIn($attendance, $currentTime, $imagePath, $authUser)
    {
        $attendance->admission_time = $currentTime->format('H:i');
        $attendance->admission_image = $this->uploadImage($imagePath);
        $attendance->user_id = $authUser;
        $attendance->date = $currentTime->format('Y-m-d');
    
        if ($this->isLateForCheckIn($attendance->admission_time)) {
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

    protected function updateCheckOut($attendance, $currentTime, $imagePath)
    {
        $attendance->departure_time = $currentTime->format('H:i');
        $attendance->departure_image = $this->uploadImage($imagePath);
        $attendance->save();
    }

}
