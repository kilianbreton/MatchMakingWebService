<?php

namespace App\Http\Controllers;

use App\Models\GameMode;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ServerController extends Controller
{
    public function edit(string $login)
    {
        $player = auth()->user();

        if (!$this->playerOwnsDedicated($player->token, $login))
        {
            abort(403, 'This server does not belong to you.');
        }

        $server = Server::where('login', $login)
            ->where('ownerid', $player->id)
            ->first();

        $gamemodes = GameMode::orderBy('name')->get();


        return view('servers.configure', [
            'serverLogin' => $login,
            'server' => $server,
            'gamemodes' => $gamemodes,
        ]);
    }

    public function update(Request $request, string $login)
    {
        $player = auth()->user();
    
        if (!$this->playerOwnsDedicated($player->token, $login)) {
            abort(403, 'This server does not belong to you.');
        }
    
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'gamemode' => ['required', 'exists:gamemodes,id'],
            'type' => ['required', 'in:1,2'],
        ]);
    
        $server = Server::where('login', $login)->first();
        $rawApiKey = null;
    
        if ($server) {
            $server->fill([
                'name' => $validated['name'] ?: $login,
                'gamemode' => (int) $validated['gamemode'],
                'type' => (int) $validated['type'],
                'ownerid' => $player->id,
                'latestping' => now(),
            ]);
    
            if (empty($server->apikey)) {
                $rawApiKey = 'sm_' . Str::random(64);
                $server->apikey = Hash::make($rawApiKey);
            }
    
            $server->save();
        } else {
            $rawApiKey = 'sm_' . Str::random(64);
    
            $server = Server::create([
                'login' => $login,
                'name' => $validated['name'] ?: $login,
                'gamemode' => (int) $validated['gamemode'],
                'type' => (int) $validated['type'],
                'ownerid' => $player->id,
                'latestping' => now(),
                'apikey' => Hash::make($rawApiKey),
            ]);
        }
    
        return redirect()
            ->route('servers.configure', ['login' => $login])
            ->with('success', 'Server configuration saved.')
            ->with('generated_api_key', $rawApiKey);
    }
    public function regenerateKey(string $login)
    {
        $player = auth()->user();
    
        if (!$this->playerOwnsDedicated($player->token, $login)) {
            abort(403, 'This server does not belong to you.');
        }
    
        $server = Server::where('login', $login)
            ->where('ownerid', $player->id)
            ->firstOrFail();
    
        $rawApiKey = 'sm_' . Str::random(64);
    
        $server->update([
            'apikey' => Hash::make($rawApiKey),
        ]);
    
        return redirect()
            ->route('servers.configure', ['login' => $login])
            ->with('success', 'API key regenerated.')
            ->with('generated_api_key', $rawApiKey);
    }
    private function playerOwnsDedicated(string $accessToken, string $serverLogin): bool
    {
        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get('https://prod.live.maniaplanet.com/webservices/me/dedicated');

        if (!$response->successful())
        {
            return false;
        }

        $servers = $response->json();

        if (!is_array($servers))
        {
            return false;
        }

        foreach ($servers as $server)
        {
            if (($server['login'] ?? null) === $serverLogin)
            {
                return true;
            }
        }

        return false;
    }
}
