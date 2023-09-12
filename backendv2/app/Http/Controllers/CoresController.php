<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Core;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;


class CoresController extends Controller
{
    // Mostrar todos los nucleos
    public function getCores()
    {
        $cores = Core::with('department')->get();
        return response()->json($cores);
    }

    // Crear un nuevo nucleo en la base de datos
    public function createCore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:cores',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        //Creamos un nuevo objeto Cores
        $core = new Core();

        //Setear los valores en la tabla Cores
        $core->name = $request->input('name');

        //Validamos que el departamento exista en la base de datos
        if (Department::find($request->input('department_id'))) {
            //Setear los valores en la tabla Cores
            $core->department_id = $request->input('department_id');
            //Guardamos los valores en la tabla Cores
            $core->save();
        } else {
            return response()->json(['message' => 'Departamento no encontrado.'], 404);
        }

        //Retornamos la respuesta en formato JSON
        return response()->json(['message' => 'Nucleo creado exitosamente.', 'data' => $core], 201);
    }

    public function updateCore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'string',
                'max:255',
                Rule::unique('cores')->ignore($id),
            ],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        //Buscamos el departmento por id
        $core = Core::find($id);

        //Validamos que el departamento exista en la base de datos
        if (!$core) {
            return response()->json(['message' => 'Nucleo no encontrado.'], 404);
        } else {

            //Setear los valores en la tabla Departments
            $core->name = $request->input('name');

            //Validamos que el departamento exista en la base de datos
            if (Department::find($request->input('department_id'))) {
                //Setear los valores en la tabla Cores
                $core->department_id = $request->input('department_id');
                //Guardamos los valores en la tabla Cores
                $core->save();
            } else {
                return response()->json(['message' => 'Departamento no encontrado.'], 404);
            }

            //Actualizamos los valores en la tabla Departments
            $core->update();

            //Retornamos la respuesta en formato JSON
            return response()->json(['message' => 'Nucleo actualizado exitosamente.', 'data' => $core]);
        }
    }

    // Borrar un nucleo en la base de datos
    public function deleteCore($id)
    {
        $core = Core::find($id);

        //Validamos que el nucleo exista en la base de datos
        if (!$core) {
            return response()->json(['message' => 'Nucleo no encontrado.'], 404);
        } else {
            //Borramos el nucleo
            $core->delete();
            //Retornamos la respuesta en formato JSON
            return response()->json(['message' => 'Nucleo eliminado exitosamente.']);
        }
    }
}