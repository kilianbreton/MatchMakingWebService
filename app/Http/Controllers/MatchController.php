<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matche;
use Illuminate\Support\Carbon;

class MatchController extends Controller
{

    public function index()
    {
        $matches = Matche::query()
            ->where(function ($query)
            {
                $query->where('finished', 0)
                    ->orWhere(function ($subQuery)
                    {
                        $subQuery->where('finished', 1)
                            ->where('updated_at', '>=', now()->subMinutes(5));
                    });
            })
            ->with(['playersA', 'playersB', 'server', 'gamemode'])
            ->orderByDesc('updated_at')
            ->get();

        return view('matches.index', compact('matches'));
    }
}
