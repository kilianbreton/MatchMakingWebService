<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class AdminPlayerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $players = Player::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('login', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('login')
            ->paginate(25)
            ->withQueryString();

        return view('admin.players.index', [
            'players' => $players,
            'search' => $search,
        ]);
    }

    public function ban(Request $request, Player $player)
    {
        $data = $request->validate([
            'duration' => ['nullable', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'permanent' => ['nullable', 'boolean'],
        ]);

        $permanent = (bool) ($data['permanent'] ?? false);

        $player->update([
            'is_banned' => true,
            'banned_until' => $permanent ? null : now()->addMinutes((int) ($data['duration'] ?? 60)),
            'ban_reason' => $data['reason'] ?? null,
        ]);

        return back()->with('success', 'Player banned.');
    }

    public function unban(Player $player)
    {
        $player->update([
            'is_banned' => false,
            'banned_until' => null,
            'ban_reason' => null,
        ]);

        return back()->with('success', 'Player unbanned.');
    }

    public function mute(Request $request, Player $player)
    {
        $data = $request->validate([
            'duration' => ['nullable', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'permanent' => ['nullable', 'boolean'],
        ]);

        $permanent = (bool) ($data['permanent'] ?? false);

        $player->update([
            'is_muted' => true,
            'muted_until' => $permanent ? null : now()->addMinutes((int) ($data['duration'] ?? 60)),
            'mute_reason' => $data['reason'] ?? null,
        ]);

        return back()->with('success', 'Player muted.');
    }

    public function unmute(Player $player)
    {
        $player->update([
            'is_muted' => false,
            'muted_until' => null,
            'mute_reason' => null,
        ]);

        return back()->with('success', 'Player unmuted.');
    }
}