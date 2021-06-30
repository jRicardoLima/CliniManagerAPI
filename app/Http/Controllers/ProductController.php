<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\ProductRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private $productRepository;
    public function __construct()
    {
        $this->productRepository = App::make(RepositoryFactory::class,['class' => ProductRepository::class]);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request,[
                'name' => 'required|min:3',
            ]);

            DB::beginTransaction();
            $data = (object) filterRequestAll($request->all());
            $ret = $this->productRepository->save($data);
            if($ret){
                DB::commit();
                return $this->success([],'Produto cadastrado com sucesso',200);
            }
            DB::rollBack();
        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),480);
        }
    }

    public function search(Request $request)
    {
        try{
            $data = filterRequestAll($request->all());

            if($data == null || count($data) == 0){
                $result = $this->productRepository->findAll(true,true);
                return $this->success($result,'success',200);
            }
            $result = $this->productRepository->get($data,[],true,false,true);
            return $this->success($result,'success',200);
        } catch (\Exception $exc){
            return $this->error($exc->getMessage(),480);
        }
    }

    public function update($id,Request $request)
    {
        try {
            if($id != null && $id != ''){
                $this->validate($request,[
                    'name' => 'required|min:3',
                ]);

                DB::beginTransaction();
                $data = (object) $request->all();
                $ret = $this->productRepository->update($id,$data);

                if($ret){
                    DB::commit();
                    return $this->success([],'Produto atualizado com sucesso',200);
                }
            }
            return $this->success([],'ID nulo',215);
        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error('Erro de validação',480);
        }
    }

    public function delete($id)
    {
        try {
            if($id != null && $id != ''){
                DB::beginTransaction();
                    $ret = $this->productRepository->remove($id);

                    if($ret){
                        DB::commit();
                        return $this->success([],'Produto excluido com sucesso',200);
                    }
                    return $this->success([],'Produto não pode ser excluido',215);
            }
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error('Erro ao excluir Produto',480);
        }
    }

    public function listProducts()
    {
        try {
            $ret = $this->productRepository->findAll(false,true);

            if(!is_null($ret) && count($ret) > 0){
                return $this->success($ret,'success',200);
            }
            return $this->success([],'Produtos não encotrados',215);
        } catch (\Exception $exc){
            return $this->error('Erro ao listar Produtos',480);
        }
    }
}
