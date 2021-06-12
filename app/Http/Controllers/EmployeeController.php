<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\EmployeeRepository;
use App\Repository\RepositoryFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

class EmployeeController extends Controller
{
    private $employeeRepository;

    public function __construct()
    {
        $this->employeeRepository = App::make(RepositoryFactory::class,['class' => EmployeeRepository::class]);
    }

    public function store(Request $request)
    {
        try {

            $this->validate($request,[
                'name' => 'required|min:3',
                'birth_date' => 'required',
                'cpf_cnpj' => 'required',
                'type' => [
                    'required',
                    Rule::notIn(['none'])
                ],
                'professional_register' => 'required_if:type,health_professional',
                'bussiness_id' => [
                    'required',
                    Rule::notIn(['none'])
                ],
                'occupation_id' => [
                    'required',
                    Rule::notIn(['none'])
                ],
                'city' => 'required',
                'neighborhood' => 'required',
                'street' => 'required',
            ]);
            DB::beginTransaction();
            $image = null;
            if(isset($request->file) && $request->file != "" && $request->exists('file')){
                $nameFile = 'avatar_'.$request->cpf_cnpj;

                $image = decodeBase64ToImage($request->file,$nameFile);
                Storage::put("public/photos_users/{$image['image']}",$image['raw']);
                $request->file = "public/storage/photos_users/{$image['image']}";
            }

                $data = (object) RequestAllCustom($request->all(),function($item,$key) use ($image) {
                    if($key == 'file' && $image != null){
                        $item = "public/storage/photos_users/{$image['image']}";
                    }
                    return $item;
                });
                $ret = $this->employeeRepository->save($data);
            DB::commit();
                if($ret){
                    return $this->success([],'Funcionario salvo com sucesso',200);
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
            $data = filterRequestAll($request->all());
            if($data == null || count($data) == 0){
                $result = $this->employeeRepository->findAll(true,true);

                return $this->success($result,'success',200);
            } else {
                $result = $this->employeeRepository->get($data,[],true,false,true);

                return $this->success($result,'success',200);
            }

        }catch (Exception $e){
            return $this->error('Error',480,$e->getMessage());
        }
    }

    public function update(Request $request,$id)
    {
        try{
            $this->validate($request,[
                'name' => 'required|min:3',
                'birth_date' => 'required',
                'cpf_cnpj' => 'required',
                'type' => [
                    'required',
                    Rule::notIn(['none'])
                ],
                'professional_register' => 'required_if:type,health_professional',
                'bussiness_id' => [
                    'required',
                    Rule::notIn(['none'])
                ],
                'occupation_id' => [
                    'required',
                    Rule::notIn(['none'])
                ],
                'city' => 'required',
                'neighborhood' => 'required',
                'street' => 'required',
            ]);
            DB::beginTransaction();
                $image = null;
            if(isset($request->file) && $request->file != "" && $request->exists('file')){
                $nameFile = 'avatar_'.$request->cpf_cnpj;

                $image = decodeBase64ToImage($request->file,$nameFile);
                if(Storage::exists("public/photos_users/{$image['image']}")){
                    Storage::delete("public/photos_users/{$image['image']}");
                }
                Storage::put("public/photos_users/{$image['image']}",$image['raw']);
                $request->file = "public/storage/photos_users/{$image['image']}";
            }
            $data = (object) RequestAllCustom($request->all(),function($item,$key) use ($image) {
                if($key == 'file' && $image != null){
                    $item = "public/storage/photos_users/{$image['image']}";
                }
                return $item;
            });
            $ret = $this->employeeRepository->update($id,$data);
            DB::commit();
            if($ret){
                return $this->success([],'Funcionario atualizado com sucesso',200);
            }
        }catch (ValidationException $e){
            DB::rollBack();
            return $this->success($e->errors(),'Erro de validação',215);
        }catch (Exception $e){
            DB::rollBack();
            return $this->error('Erro ao atualizar',480,$e->getMessage());
        }
    }

    public function delete($id)
    {
        try{
            if($id != null && $id != ""){
                DB::beginTransaction();
                    $ret = $this->employeeRepository->remove($id);
                DB::commit();
                    if($ret){
                        return $this->success([],'Funcionario excluido com sucesso',200);
                    }
                    DB::rollBack();
                    return $this->success([],'Funcionario não pode ser excluido',215);

            }
            return $this->success([],'Erro id nulo',215);
        }catch(Exception $e){
            DB::rollBack();
            return $this->error('Erro ao excluir funcionario',480,[$e->getMessage()]);
        }
    }

    public function photoUser($id)
    {
        if(!is_null($id) && $id != ""){
            $pathPhoto = $this->employeeRepository->get(['id' => $id],['photo'],false,true);
            if(!is_null($pathPhoto->photo) && $pathPhoto->photo != ""){
                return $this->success(['photo' => $pathPhoto->photo],'success',200);
            }
            return $this->success([],'Funcionário não possui foto',215);
        }
        return $this->error('ID é nulo',480,[]);
    }

    public function listEmployee($type)
    {
        if(!is_null($type) && $type !== ""){
            $employees = $this->employeeRepository->get(['type' => $type]);

            if(!is_null($employees) && $employees != ""){
                return $this->success(['employees' => $employees],'success',200);
            }
            return $this->success([],'Profissionais de saude não encontrado',215);
        }
        return $this->error('Tipo é nulo',480);
    }

}
