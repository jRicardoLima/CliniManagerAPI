<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\HealthProcedureRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HealthProcedureController extends Controller
{
    protected $healthProcedureRepository;

    public function __construct()
    {
        $this->healthProcedureRepository = App::make(RepositoryFactory::class,['class' => HealthProcedureRepository::class]);
    }

    public function store(Request $request)
    {

        try {
            $this->validate($request,[
                'name' => 'required|min:3',
            ]);
            DB::beginTransaction();
                $data = (object) $request->all();

                $ret = $this->healthProcedureRepository->save($data);
           DB::commit();
           if($ret){
               return $this->success([],'Procedimento salvo com sucesso',200);
           }
        }catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        }catch (\Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),480);
        }
    }

    public function search(Request $request)
    {
        try{

            $data = filterRequestAll($request->all());

            if($data == null || count($data) == 0){
                $result = $this->healthProcedureRepository->findAll(true,true);
                return $this->success($result,'success',200);
            }
            $result = $this->healthProcedureRepository->get($data,[],true,false,true);
            return $this->success($result,'success',200);
        }catch(\Exception $exc){
            return $this->error('Erro ao pesquisar procedimentos',480);
        }
    }

    public function update($id,Request $request)
    {
        try {
            if($id != '' && $id != null){
                $this->validate($request,[
                    'name' => 'required|min:3'
                ]);
                DB::beginTransaction();
                    $data = (object) $request->all();
                    $ret = $this->healthProcedureRepository->update($id,$data);
                DB::commit();
                    if($ret){
                        return $this->success([],'Procedimento atualizado com sucesso',200);
                    }
            }
            return $this->success([],'Erro ID nulo',215);
        }catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',215);
        } catch (\Exception $exc){
            return $this->error('Erro ao atualizar especialidade',480);
        }
    }

    public function delete($id)
    {
        try {
            if($id != null && $id != ''){
                DB::beginTransaction();
                  $ret = $this->healthProcedureRepository->remove($id);
               DB::commit();

               if($ret){
                   return $this->success([],'Procedimento excluido com sucesso',200);
               }
               DB::rollBack();
               return $this->success([],'Procedimento não pode ser excluido',215);
            }
            return $this->success([],'Erro ID nulo',215);
        }catch (\Exception $exc){
            DB::rollBack();
            return $this->error('Erro ao excluir procedimento',480);
        }
    }

    public function listHealthProcedure()
    {

    }
}
