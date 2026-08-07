<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;

class StatusArmadaController extends Controller
{
    public function index()
    {
        return redirect()->route('pembayaran.index', ['tab' => 'status']);
    }
}
