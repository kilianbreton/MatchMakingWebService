<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ManiaplanetAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('maniaplanet_oauth_state', $state);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.maniaplanet.client_id'),
            'redirect_uri' => config('services.maniaplanet.redirect'),
            'scope' => 'basic dedicated',
            'state' => $state,
        ]);

        return redirect(config('services.maniaplanet.authorize_url') . '?' . $query);
    }

    public function callback(Request $request)
    {
        $expectedState = $request->session()->pull('maniaplanet_oauth_state');

        if (!$request->has('code') || !$request->has('state') || $request->state !== $expectedState) {
            return redirect('/')->with('error', 'OAuth state invalide.');
        }

        $tokenResponse = Http::asForm()->post(config('services.maniaplanet.token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.maniaplanet.client_id'),
            'client_secret' => config('services.maniaplanet.client_secret'),
            'code' => $request->code,
            'redirect_uri' => config('services.maniaplanet.redirect'),
        ]);

        if (!$tokenResponse->successful()) {
            return redirect('/')->with('error', 'Impossible de récupérer le token Maniaplanet.');
        }

        $tokenData = $tokenResponse->json();

        $accessToken = $tokenData['access_token'] ?? null;
        $refreshToken = $tokenData['refresh_token'] ?? null;
        $expiresIn = $tokenData['expires_in'] ?? null;

        if (!$accessToken) {
            return redirect('/')->with('error', 'Token d’accès manquant.');
        }



        // À adapter selon l’endpoint user info que tu veux utiliser côté Maniaplanet WS.
        // L'idée est d'appeler un endpoint protégé avec Bearer access_token
        // pour récupérer au minimum login + nickname.
        $meResponse = Http::withToken($accessToken)->get('https://prod.live.maniaplanet.com/webservices/me');

        if (!$meResponse->successful()) {
            return redirect('/')->with('error', 'Impossible de récupérer le profil utilisateur.');
        }

        $me = $meResponse->json();
        
        $login = $me['login'] ?? null;
        $nickname = $me['nickname'] ?? null;
        $location = $me['path'] ?? null;

        if (!$login) {
            return redirect('/')->with('error', 'Login Maniaplanet introuvable.');
        }

        $player = Player::updateOrCreate(
            ['login' => $login],
            [
                'name' => $nickname ?: $login,
                'token' => $accessToken,
                'location' => $location,
                'refresh' => $refreshToken,
                'updated_at' => now(),
            ]
        );

        auth()->login($player);

        return redirect('/')->with('success', 'Connexion réussie.');
    }
}