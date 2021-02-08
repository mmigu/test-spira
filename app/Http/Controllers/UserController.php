<?php

namespace App\Http\Controllers;

use App\User;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function createStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_CREATE,
                'Los datos enviados son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }

        $request->password = bcrypt($request->password);
        $user = new User();
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->phone = $request->phone;
        $user->role_id = 2;
        $user->save();

        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Estudiante creado correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }
    //actualizar usuarios
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'name' => 'nullable|string',
            'lastname' => 'nullable|string',
            'email' => 'nullable|string|email|unique:users',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_CREATE,
                'Los datos actualizados son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }
        $user = User::where('id', $request->user_id)
            ->first();

        $user->name = isset($request->name) ? $request->name : $user->name;
        $user->lastname = isset($request->lastname) ? $request->lastname : $user->lastname;
        $user->email = isset($request->email) ? $request->email : $user->email;
        $user->phone = isset($request->phone) ? $request->phone : $user->phone;
        $user->update();

        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Estudiante actualizado correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }
    //consultar estudiantes
    public function getStudents()
    {
        $users = User::where('role_id', '2')->get();

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'datos traídos correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            $users
        );
    }
    public function showStudent($userId)
    {
        $users = User::find($userId);
        if (!$users) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'La clase no existe en el sistema',
                false,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }
        if ($users->role_id != 2) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'El usuario no es un estudiante ',
                false,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'datos traídos correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            $users
        );
    }

    public function deleteStudent($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_SUCCESS,
                'El Estudiante no existe en el sistema',
                false,
                Utilities::COD_RESPONSE_HTTP_OK,
                null
            );
        }
        if ($user->role_id != 2) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'El usuario no es un estudiante ',
                false,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }

        $user->delete();

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Estudiante Eliminado correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }



    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_CREATE,
                'Los datos enviados son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_UNAUTHORIZED,
                'El usuario o la contraseña son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_UNAUTHORIZED,
                null
            );
        }

        $user = Auth::user();
        // return $user;
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Inicio de sesión exitoso',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            $tokenResult->accessToken
        );
    }

    //consulta cursos por usuarios 
    public function getMyCourses()
    {
        $user = Auth::user();
        
        if ($user->role_id != 2) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'Sin permisos para hacer esta opración',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }

        $student = User::with('courses')
        ->find($user->id);

        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Inicio de sesión exitoso',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            $student
        );

    }
    public function getStudentsCourses()
    {
        $user = Auth::user();
        
        if ($user->role_id != 1) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'Sin permisos para hacer esta opración',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }
        $users = User::where('role_id', '2')->with('courses')->get();

        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Inicio de sesión exitoso',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            $users
        );
    }
}
