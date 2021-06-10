<?php


namespace App\Services\HttpClientService;


use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

interface IHttpClient
{
    public function methodGet(string $url,array $param = []) : ?Response;
    public function methodPost(string $url,array $param = []) : ?Response;
    public function methodPut(string $url,array $param = []) : ?Response;
    public function methodPatch(string $url,array $param = []) : ?Response;
    public function methodDelete(string $url, array $param = []) : ?Response;
}
