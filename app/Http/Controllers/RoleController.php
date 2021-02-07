<?php

namespace App\Http\Controllers;

use App\Role;
use App\Utilities;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getRoles()
    {
        try {
            $roles = Role::all();

            return Utilities::sendMessage(
                Utilities::COD_RESPONSE_SUCCESS,
                'Datos traídos con éxito',
                false,
                Utilities::COD_RESPONSE_HTTP_OK,
                $roles
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
