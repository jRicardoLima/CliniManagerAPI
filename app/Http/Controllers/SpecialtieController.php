<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\SpecialtieRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SpecialtieController extends Controller
{
    private $specialtieRepository;

    public function __construct()
    {
        $this->specialtieRepository = App::make(RepositoryFactory::class,['class' => SpecialtieRepository::class]);
    }

    public function store(Request $request)
    {
        try {
         $this->validate($request,[
             'name' => 'required|min:3'
         ]);
         DB::beginTransaction();
            $data = (object) $request->all();
            $ret = $this->specialtieRepository->save($data);
            DB::commit();
            if($ret){
                return $this->success([],'Especialidade salva com sucesso',200);
            }
        } catch (ValidationException $e){
            DB::rollBack();
            return $this->success($e->errors(),'Erro de validação',215);
        } catch (\Exception $e){
            DB::rollBack();
            return $this->error('Erro ao salvar especialidade',480,[]);
        }
    }

    public function search(Request $request)
    {
        try{
            $data = filterRequestAll($request->all());

            if($data == null || count($data) == 0){
                $result = $this->specialtieRepository->findAll(true,true);
                return $this->success($result,'success',200);
            }
            $result = $this->specialtieRepository->get($data,[],true,false,true);
            return $this->success($result,'success',200);
        }catch (\Exception $e){
            return $this->error('Erro ao pesquisar especialidades',480,[]);
        }
    }

    public function update($id,Request $request)
    {
        try{
           if($id != null || $id != ""){
               $this->validate($request,[
                   'name' => 'required|min:3'
               ]);
               DB::beginTransaction();
                $data = (object) $request->all();
                $ret = $this->specialtieRepository->update($id,$data);
               DB::commit();
                if($ret){
                    return $this->success([],'Especialidade atualizada com sucesso',200);
                }

           }
           return $this->success([],'ID nulo',215);
        }catch (ValidationException $e){

        }catch (\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage(),480,[]);
        }
    }

    public function delete($id)
    {
       try{
           if($id != null && $id !== ''){
                DB::beginTransaction();
                    $ret = $this->specialtieRepository->remove($id);
                DB::commit();
                if($ret){
                    return $this->success([],'Especilidade excluida com sucesso',200);
                }
                DB::rollBack();
                return $this->success([],'Especialidade não pode ser excluida',215);
           }
           return $this->success([],'Erro id nulo',215);
       }catch (\Exception $e){
           DB::rollBack();
           return $this->error('Erro ao excluir especialidade',480,[$e->getMessage()]);
       }
    }

}
