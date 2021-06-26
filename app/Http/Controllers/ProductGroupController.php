<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\ProductGroupRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductGroupController extends Controller
{
    private $productGroupRepository;

    public function __construct()
    {
        $this->productGroupRepository = App::make(RepositoryFactory::class,['class' => ProductGroupRepository::class]);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request,[
                'name' => 'required'
            ]);
            DB::beginTransaction();
                $data = (object) $request->all();
                $ret = $this->productGroupRepository->save($data);
            DB::commit();
            if($ret){
                return $this->success([],'Grupo de produto salvo com sucesso',200);
            }
        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (\Exception $exc) {
            DB::rollBack();
            return $this->error('Erro ao salvar grupo de produto','480');
        }
    }

    public function search(Request $request)
    {
        try {
            $data = filterRequestAll($request->all());

            if($data == null || count($data) == 0){
                $result = $this->productGroupRepository->findAll(true,true);
                return $this->success($result,'success',200);
            }
            $result = $this->productGroupRepository->get($data,[],true,false,true);
            return $this->success($result,'success',200);
        } catch (\Exception $exc){
            return $this->error($exc->getMessage(),480);
        }
    }

    public function update($id,Request $request)
    {
        try {
            if($id != '' && $id != null){
                $this->validate($request,[
                    'name' => 'required'
                ]);
                DB::beginTransaction();
                    $data = (object) $request->all();
                    $ret = $this->productGroupRepository->update($id,$data);
                 if($ret){
                     DB::commit();
                     return $this->success([],'Grupo de produto atualizado com sucesso',200);
                 }
                 DB::rollBack();
                 return $this->success([],'Erro ao atualizar grupo de produto',215);
            }
            return $this->success([],'Erro Id nulo',215);
        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),480);
        }
    }

    public function delete($id)
    {
        try{
          if($id != '' && $id != null){
              DB::beginTransaction();
                $ret = $this->productGroupRepository->remove($id);

                if($ret){
                    DB::commit();
                    return $this->success([],'Grupo de produto deletado com sucesso',200);
                }
                DB::rollBack();
                return $this->success([],'Erro ao deletar grupo de produto',215);
          }
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),'Erro ao deletar grupo de produto',480);
        }
    }

    public function listProductGroup()
    {
        try {
            $ret = $this->productGroupRepository->findAll(false,true);

            if(!is_null($ret) &&  count($ret) > 0){
                return $this->success($ret,'success',200);
            }
            return $this->success([],'Grupo de produtos não encotrados',215);
        } catch (\Exception $exc){
            return $this->error('Erro ao listar Grupo de produtos',480,);
        }
    }

}
