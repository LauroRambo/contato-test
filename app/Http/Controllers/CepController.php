<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ViaCepService;
    

class CepController extends Controller
{
    protected $viaCepService;

    public function __construct(ViaCepService $viaCepService)
    {
        $this->viaCepService = $viaCepService;
    }

    public function buscarEndereco(Request $request)
    {
        $request->validate([
            'cep' => 'required|regex:/^\d{5}-?\d{3}$/',
        ]);

        try {
            $cep = $request->input('cep');
            $endereco = $this->viaCepService->buscarEnderecoPorCep($cep);

            return response()->json($endereco);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
