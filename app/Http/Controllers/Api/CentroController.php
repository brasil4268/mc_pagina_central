<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Centro;
use Illuminate\Http\Request;

class CentroController extends Controller
{
    //
    public function index()
    {
        return response()->json([
            'status' => 'sucesso',
            'dados' => Centro::all()
        ]);
    }
}
