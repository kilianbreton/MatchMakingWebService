<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->all();

        // Validation d’un tableau d’objets
        $validator = Validator::make($data, [
            '*.login'    => 'required|string|max:255',
            '*.name' => 'required|string|max:255',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $users = collect($data)->map(function ($item)
        {
            return [
                'login'      => $item['login'],
                'name'   => $item['name'],
                'updated_at' => now(),
                'created_at' => now(),
            ];
        })->toArray();

        // 🔥 Bulk upsert
        Player::upsert(
            $users,
            ['login'],        // clé unique
            ['name', 'updated_at'] // champs à mettre à jour
        );

        return response()->json([
            'message' => 'Users synchronized',
            'count'   => count($users),
        ]);
    }
}
