<?php


namespace App\Services\HttpClientService;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
abstract class HttpClientImplementation implements IHttpClient
{

    public function methodGet(string $url,array $param = []) : ?Response
    {
        return Http::get($url,$param);
    }

    public function methodPost(string $url,array $param = []) : ?Response
    {
       return Http::post($url,$param);
    }

    public function methodPut(string $url,array $param = []) : ?Response
    {
        return Http::put($url,$param);
    }

    public function methodPatch(string $url,array $param = []) : ?Response
    {
       return Http::patch($url,$param);
    }

    public function methodDelete(string $url,array $param = []) : ?Response
    {
        return Http::delete($url,$param);
    }
}
