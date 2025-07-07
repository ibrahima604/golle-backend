<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        // Retourner les informations de l'utilisateur connecté
        return response()->json([
            'message' => 'Bienvenue sur votre dashboard !',
            'user' => $request->user(),
        ]);
    }
}
