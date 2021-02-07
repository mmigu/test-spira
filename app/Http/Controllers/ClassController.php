<?php

namespace App\Http\Controllers;

use App\Course;
use App\User;
use App\UsersClass;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    public function createClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|unique:courses,name",
            "hourly_intensity" => "required|numeric|min:1"
        ]);
        //SI falla el Validator muestra mensajes de error
        if ($validator->fails()) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_CREATE,
                'Los datos enviados son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }
        $class = new Course();
        $class->name = $request->name;
        $class->hourly_intensity = $request->hourly_intensity;
        $class->save();

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Clase creada correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }

    public function updateClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "class_id" => 'required|numeric|exists:courses,id',
            "name" => "nullable|string|unique:courses,name",
            "hourly_intensity" => "nullable|numeric|min:1"
        ]);
        //SI falla el Validator muestra mensajes de error
        if ($validator->fails()) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_UPDATE,
                'Los datos enviados son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }

        $class = Course::where('id', $request->class_id)
            ->first();
        // $class = Course::find($request->name);



        $class->name = $request->name = isset($request->name) ? $request->name : $class->name;
        $class->hourly_intensity = isset($request->hourly_intensity) ? $request->hourly_intensity : $class->hourly_intensity;

        $class->update();
        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Clase actualizada correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }

    public function getClasses()
    {
        $classes = Course::all();

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'datos traídos correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            $classes
        );
    }

    public function showClass($courseId)
    {
        $class = Course::find($courseId);

        if (!$class) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'La clase no existe en el sistema',
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
            $class
        );
    }

    public function deleteClass($courseId)
    {
        $class = Course::find($courseId);

        if (!$class) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_SUCCESS,
                'La clase no existe en el sistema',
                false,
                Utilities::COD_RESPONSE_HTTP_OK,
                null
            );
        }

        $class->delete();

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'clase eliminada correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }

    public function assignCourseToStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'classes' =>'required|array',
            'classes.*.id' => 'required|numeric|exists:courses,id'
        ]);
        //SI falla el Validator muestra mensajes de error
        if ($validator->fails()) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_UPDATE,
                'Los datos enviados son inválidos',
                true,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                $validator->errors()
            );
        }


        for ($i=0; $i < count($request->classes); $i++) {
            $userClass = new UsersClass();
            $userClass->user_id = $request->user_id;
            $userClass->class_id = $request->classes[$i]['id'];
            $userClass->save();
        }

        // foreach ($request->classes as $value) {
        //     $userClass = new UsersClass();
        //     $userClass->user_id = $request->user_id;
        //     $userClass->class_id = $value->id;
        //     $userClass->save();
        // }


        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'clases asociadas correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );

    }

    public function deleteUserCourse($user_id, $class_id )
    {
        $class = Course::find($class_id);

        if (!$class) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'La clase no existe en el sistema',
                false,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }
        $user = User::find($user_id);

        if (!$user) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'El usuario no existe en el sistema',
                false,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }
        $userClass = UsersClass::where('user_id',$user_id)
        ->where('class_id',$class_id)->first();

        if (!$userClass) {
            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_ERROR_SHOW,
                'El usuario no esta asociado al curso',
                false,
                Utilities::COD_RESPONSE_HTTP_BAD_REQUEST,
                null
            );
        }
        $userClass->delete();

        //Devolver mensaje de respuesta exitosa
        return Utilities::sendMessage(
            Utilities::COD_RESPONSE_SUCCESS,
            'Usuario eliminado del curso correctamente',
            false,
            Utilities::COD_RESPONSE_HTTP_OK,
            null
        );
    }
}
