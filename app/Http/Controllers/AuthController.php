<?php

namespace App\Http\Controllers;

use App\Models\Occupation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function register(Request $request)
    {
//        $attr = $request->validate([
//            'user_name' => 'required|min:3',
//            'password' => 'required|min:3',
//
//        ]);

//         $organization = new Organization();
//
//         $organization->uuid = Str::uuid();
//         $organization->cpf_cnpj = $request->cpf_cnpj;
//         $organization->license = $request->license;
//         $organization->qtd_user = $request->qtd_user;
//         $organization->save();

//        $occupation = new Occupation();
//
//        $occupation->name = $request->name;
//        $occupation->uuid = Str::uuid();
//        $occupation->organization_id = 1;
//        $occupation->save();
//        $user = new User();
//        $user->user_name = $attr['user_name'];
//        $user->uuid = Str::uuid();
//        $user->password = bcrypt($attr['password']);
//        $user->organization_id = 2;
////
//        $user->save();
     //   return ['message' => 'minha rola'];
        //return $this->success(['token' => $user->createToken('API Token')->plainTextToken]);
    }

    public function login(Request $request)
    {
        try{
            if(!Auth::attempt(['user_name' => $request->user_name,'password' => $request->password])){
                return $this->error('Credenciais invalidas',215);
            }
            Log::channel('systemLog')->info("Usuário ".auth()->user()->user_name." realizou o login");

           if(count(auth()->user()->tokens) >= 1){
               auth()->user()->tokens()->delete();
           }
            return $this->success(['token' => auth()->user()->createToken($request->user_name)->plainTextToken],'success',200);
        } catch (\Exception $e){
            Log::channel('systemLog')->error("Erro durante o login".$e->getMessage());
            return $this->error('Erro:'.$e->getMessage(),480);
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->tokenExists($request);
            auth()->user()->tokens()->delete();
            return $this->success('','success',200);
        } catch (\Exception $e){
            Log::channel('systemLog')->error('Erro ao realizar logout'.$e->getMessage());
            return $this->error('','error',480);
        }



    }
}
