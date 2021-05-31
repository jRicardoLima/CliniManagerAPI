<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\EmployeeRepository;
use App\Repository\RepositoryFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            $file = decodeBase64ToImage($request->file,'nova_imagem');
            Storage::put("public/photos_users/{$file['image']}",$file['raw']);
           exit();
            $this->validate($request,[
                'name' => 'required|min:3',
                'cpf_cnpj' => 'required',
                'professional_register' => 'required_if:type,health_professional',
                'bussiness_id' => 'required',
                'occupation_id' => 'required',
                'city' => 'required',
                'neighborhood' => 'required',
                'street' => 'required',
            ]);
            DB::beginTransaction();

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

    }

    public function update($id)
    {

    }

    public function delete($id)
    {

    }
}
