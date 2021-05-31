<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\BussinessUnitRepository;
use App\Repository\RepositoryFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
                    DB::rollBack();
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
            $data = filterRequestAll($request->all());

            if($data == null || count($data) == 0){

                $result = $this->bussinessRepository->findAll(true);
                DB::commit();
                return $this->success($result,'success',200);
            } else {
                $result = $this->bussinessRepository->get($data,[],false,false,true);
                DB::commit();
                return $this->success($result,'success',200);
            }
        }catch (Exception $e){
            DB::rollBack();
            return $this->error('Error',480,$e->getMessage());
        }
    }

    public function update($id,Request $request)
    {

        try {
            $this->validate($request,[
                'id' => 'required',
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
                $bussiness->address_id = $request->address_id;

                $ret = $this->bussinessRepository->update($id,$bussiness);
            DB::commit();

            if($ret){
                return $this->success([],'Unidade atualizado com sucesso',200);
            } else{
                DB::rollBack();
                return $this->success([],'Erro ao atualizar unidade',200);
            }
        }catch (ValidationException $e){
            DB::rollBack();
            return $this->success($e->errors(),'Erro de validação',215);
        } catch (Exception $e){
            DB::rollBack();
            return $this->error('Erro ao atualizar',480,$e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            if($id != null && $id != "" ){
                DB::beginTransaction();
                $ret = $this->bussinessRepository->remove($id);
                DB::commit();
                if($ret){
                    return $this->success([],'Unidade excluida com sucesso',200);
                } else {
                    DB::rollBack();
                    return $this->success([],'Unidade não pode ser excluida',215);
                }
            } else{
                $this->success([],'Erro id nulo',215);
            }
        }catch (Exception $e){
            DB::rollBack();
            $this->error('Erro ao excluir unidade',480);
        }
    }

    public function listBussiness()
    {
        try {
            DB::beginTransaction();
                $coluns=[
                    'id',
                    'fantasy_name'
                ];
                $result = $this->bussinessRepository->get([],$coluns);
            DB::commit();
            return $this->success($result,'success',200);
        }catch (Exception $e){
            DB::rollBack();
            $this->error('Erro ao listar unidades',480);
        }
    }
}
