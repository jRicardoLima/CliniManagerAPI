<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\BussinessUnitRepository;
use App\Repository\RepositoryFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Psy\Util\Str;

class BussinessUnitsController extends Controller
{
    private $bussinessRepository;

    public function __construct()
    {
        $this->bussinessRepository = App::make(RepositoryFactory::class,['class' => BussinessUnitRepository::class]);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request,[
                'company_name' => 'required|min:3',
                'fantasy_name' => 'required|min:3',
                'cpf_cnpj' => 'required',
                'city' => 'required',
                'neighborhood' => 'required',
                'street' => 'required',
            ]);
            DB::beginTransaction();
                $bussiness = new \stdClass();

                $bussiness->company_name = $request->company_name;
                $bussiness->fantasy_name = $request->fantasy_name;
                $bussiness->cpf_cnpj = $request->cpf_cnpj;
                $bussiness->country = $request->country;
                $bussiness->state = $request->state;
                $bussiness->zipcode = $request->zipcode;
                $bussiness->city = $request->city;
                $bussiness->neighborhood = $request->neighborhood;
                $bussiness->street = $request->street;
                $bussiness->number = $request->number;
                $bussiness->telphone = $request->telphone;
                $bussiness->celphone = $request->celphone;
                $bussiness->email = $request->email;
                $bussiness->observation = $request->observation;

                $ret = $this->bussinessRepository->save($bussiness);
                DB::commit();

                if($ret){
                    return $this->success('','Unidade cadastrada com sucesso',200);
                } else{
                    return $this->success('','Erro ao cadastrar a unidade',200);
                }


        }catch (ValidationException $e){
         DB::rollBack();
         return $this->success($e->errors(),'Erro de validação',215);
        }catch (Exception $e){
         DB::rollBack();
         return $this->error('Error',480,$e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->all() == null || $request->all() == ''){
                $result = $this->bussinessRepository->findAll();
                DB::commit();
                var_dump($result);
                exit();
                return $this->success($result,'success',200);
            }
        }catch (Exception $e){
            DB::rollBack();
            return $this->error('Error',480);
        }
    }
}
