<?php

namespace App\Http\Controllers;

use App\Services\HttpClientService\ApiCep;
use App\Services\HttpClientService\IHttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ApiExternalController extends Controller
{

    public function cep(Request $request)
    {
        $api = App::make(IHttpClient::class,['className' => ApiCep::class]);
        $cep = $request->cep;
        $response = $api->methodGet("https://viacep.com.br/ws/{$cep}/json/");
        return $response->json();
    }
}
