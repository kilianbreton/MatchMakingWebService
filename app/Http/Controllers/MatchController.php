<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matche;

class MatchController extends Controller
{
    public function index()
    {
        // Récupère les matchs en cours 
        $matches = Matche::where('finished', 0)
        ->with(['playersA', 'playersB', 'server'])
        ->get();
        return view('matches.index', compact('matches'));
    }
}