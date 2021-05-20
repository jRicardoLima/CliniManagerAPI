<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\OccupationRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OccupationController extends Controller
{
    private $occupationRepository;

    public function __construct()
    {
        $this->occupationRepository = App::make(RepositoryFactory::class,['class' => OccupationRepository::class]);
    }

    public function search(Request $request)
    {
        try {
            DB::beginTransaction();
            if($request->name == null || $request->name == ""){
                $result = $this->occupationRepository->findAll();
                DB::commit();
                return $this->success($result,'success',200);
            } else {
                $result = $this->occupationRepository->get(['name' => $request->name],[],false,false);
                DB::commit();
                return $this->success($result,'success',200);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error('Error',480);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request,[
                'name' => 'required|min:3'
            ]);

            DB::beginTransaction();
            $occ = new \stdClass();
            $occ->name = $request->name;
            $ret = $this->occupationRepository->save($occ,true);
            DB::commit();
            return $this->success(['occupation' => $ret],'Função cadastrada com sucesso',200);
        }catch (ValidationException $e){
            DB::rollBack();
            return $this->success($e->errors(),'Erro de validação',215);
        } catch (\Exception $e){
            DB::rollBack();
            return $this->error('Error',480,$e->getMessage());
        }
    }

    public function update(Request $request,int $id)
    {
        try {
            if($id != null && $id != ""){
                $this->validate($request,[
                    'name' => 'min:3'
                ]);

                DB::beginTransaction();
                    $occupation = new \stdClass();
                    $occupation->name = $request->name;
                    $ret = $this->occupationRepository->update($id,$occupation);
                DB::commit();
                    if($ret){
                       return $this->success([],'Função atualizada com sucesso',200);
                    }
            } else {
                DB::rollBack();
                return $this->success([],'Erro id nulo',215);
            }
        }catch (ValidationException $e){
            DB::rollBack();
            return $this->success($e->errors(),'Dados incorretos',215);
        }catch (\Exception $e){
            DB::rollBack();
            return  $this->error('Erro ao atualizar função',480);
        }
    }

    public function delete($id)
    {
        try {
            if($id != null && $id != ""){
                DB::beginTransaction();
                $ret = $this->occupationRepository->remove($id);
                DB::commit();
                if($ret){
                    return $this->success([],'Função excluida com sucesso',200);
                }
                DB::rollBack();
                return $this->success([],'Função não pode ser excluida',215);
            }
            return $this->success([],'Erro id nulo',215);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->error('Erro ao excluir função',480);
        }
    }
}
