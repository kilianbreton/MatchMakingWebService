<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    public function show()
    {
        $player = auth()->user();

        $servers = [];
        $error = null;

        if (!$player->token) {
            $error = 'Missing Maniaplanet access token.';
        } else {
            $response = Http::withToken($player->token)
                ->acceptJson()
                ->get('https://prod.live.maniaplanet.com/webservices/me/dedicated');

         
            if ($response->successful()) {
                $servers = $response->json();
            } else {
                $error = 'Unable to fetch dedicated servers from Maniaplanet.';
            }
        }

        return view('profile.show', [
            'player' => $player,
            'servers' => $servers,
            'error' => $error,
        ]);
    }
}