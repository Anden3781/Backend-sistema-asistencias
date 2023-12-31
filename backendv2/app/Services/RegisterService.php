<?php

declare(strict_types=1);

namespace App\Services;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Repositories\UserRepositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class RegisterService {
    protected $userRepository;


    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User {
        $data['password'] = Hash::make($data['dni']);
        $data['username'] = $data['dni'];
        $data['status'] = "1";

        $user = $this->userRepository->create($data)->assignRole(3);

        $request = app('request'); // Obtiene la instancia del objeto Request

        if ($request->hasFile('image')) {
            $archivo = $request->file('image');
            $ruta = public_path() . '/photos/' . $user->id . '/';
            $archivo->move($ruta, $archivo->getClientOriginalName());
            $user->image = $archivo->getClientOriginalName();

            $user->save();
        }
        return $user;
    }

}
