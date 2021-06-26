<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\SupplierRepository;
use App\Repository\RepositoryFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    protected $supplierRepository;

    public function __construct()
    {
        $this->supplierRepository = App::make(RepositoryFactory::class,['class' => SupplierRepository::class]);
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
                $data = (object) $request->all();

                $ret = $this->supplierRepository->save($data);
            DB::commit();

            if($ret){
                return $this->success([],'Fornecedor salvo com sucesso',200);
            }

        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),480,[]);
        }
    }

    public function search(Request $request)
    {
        try {
               $data = filterRequestAll($request->all());

               if($data == null || count($data) == 0){
                   $result = $this->supplierRepository->findAll(true,true);
                   return $this->success($result,'success',200);
               }
               $result = $this->supplierRepository->get($data,[],true,false,true);
               return $this->success($result,'success',200);
        } catch (Exception $exc){
            return $this->error('Erro ao pesquisar Fornecedores',480);
        }
    }

    public function update($id,Request $request)
    {
        try{
            if($id != '' && $id != null){
                $this->validate($request,[
                    'company_name' => 'required|min:3',
                    'fantasy_name' => 'required|min:3',
                    'cpf_cnpj' => 'required',
                    'city' => 'required',
                    'neighborhood' => 'required',
                    'street' => 'required',
                ]);
                DB::beginTransaction();
                  $data = (object) $request->all();
                  $ret = $this->supplierRepository->update($id,$data);
                DB::commit();
                if($ret){
                    return $this->success([],'Fornecedor atualizado com sucesso',200);
                }
            }
            return $this->success([],'Erro ID nulo',215);
        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),480);
        }
    }

    public function delete($id)
    {
        try {
            if($id != '' && $id != null){
                DB::beginTransaction();
                    $ret = $this->supplierRepository->remove($id);
                DB::commit();

                if($ret){
                    return $this->success([],'Fornecedor excluido com sucesso',200);
                }
                DB::rollBack();
                return $this->success([],'Fornecedor não pode ser excluido',215);
            }
            return $this->success([],'Erro ID nulo',215);
        } catch (Exception $exc){
            DB::rollBack();
            return $this->error('Erro ao excluir Fornecedor',480);
        }
    }

    public function listSuppliers()
    {
        try{
            $result = $this->supplierRepository->get([]);
            if($result != null &&  count($result) > 0){
                return $this->success($result,'success',200);
            }
            return $this->success([],'Fornecedores não encontrados',215);
        } catch (Exception $exc){
            return $this->error('Erro ao listar Forncedores',480);
        }
    }
}
