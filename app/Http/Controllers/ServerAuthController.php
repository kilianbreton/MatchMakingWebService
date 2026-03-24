<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ServerAuthController extends Controller
{
    public function auth(Request $request)
    {

        $credentials = [
            'login' => $request->login,
            'password' => $request->apikey, // 👈 important
        ];
        if (!$token = Auth::guard('server')->attempt($credentials))
        {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'token' => $token,
            'type'  => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function create()
    {
        $server = new \App\Models\Server();
        $server->login = "yox_elm1";
        $server->name = "yox_elm1";
        $server->gamemode = 1;
        $server->type = 2;
        $server->ownerid = 1;
        $server->apikey = Hash::make("ABCD");
        $server->nbplayers = 0;
        $server->save();

        return response()->json([
            'message' => 'Server created'
        ]);
    }
}
