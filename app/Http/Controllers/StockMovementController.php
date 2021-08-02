<?php

namespace App\Http\Controllers;

use App\Exceptions\StockMovementExceptions\StockMovementException;
use App\Repository\Repositories\StockMovementRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    private $stockMovementRepository;

    public function __construct(Container $container)
    {
        $this->stockMovementRepository = $container->make(RepositoryFactory::class,['class' => StockMovementRepository::class]);
    }

    public function store(Request $request)
    {
        try {
            if($request == null || count($request->data) <= 0 ){
                return $this->success([],'Campos obrigatórios em branco',215);
            }
            if($request->type == 'Entrada'){
                $this->validate($request,[
                    'data' => 'required',
                    'data.*' => 'required',
                ]);
            } else {
                $this->validate($request,[
                    'data' => 'required',
                    'data.*.date_movement' => 'required',
                    'data.*.quantity_moved' => 'required',
                    'data.*.product_id' => 'required',
                    'data.*.bussiness_unit_id' => 'required'
                ]);
            }

           $dataStockMovement = new \stdClass();
           $dataStockMovement->type = $request->type;

           if($dataStockMovement->type == 'Entrada'){

               $dataStockMovement->data = RequestAllCustom($request->data,function($item,$key){
                   $item['quantity_moved'] = formatMoneyToSql($item['quantity_moved']);
                   $item['unitary_amount'] = formatMoneyToSql($item['unitary_amount']);

                   return (object) $item;
               });
           } else {
               $dataStockMovement->data = RequestAllCustom($request->data,function($item,$key){
                   $item['quantity_moved'] = formatMoneyToSql($item['quantity_moved']);
                   return (object) $item;
               });
           }

            DB::beginTransaction();
            foreach ($dataStockMovement->data as $item){
                $item->type = $dataStockMovement->type;
                $ret = $this->stockMovementRepository->saveInLoop($item);
                if(!$ret){
                    DB::rollBack();
                    throw new StockMovementException('Erro ao salvar movimento de Estoque');
                }
            }
            DB::commit();
            return $this->success([],'Movimento de estoque salvo com sucesso');

        }catch (ValidationException $exc) {
            DB::rollBack();
            return $this->success($exc->errors(),'Campos obrigatórios',215);
        } catch (StockMovementException $exc){
            DB::rollBack();
            return $this->success([],$exc->getMessage(),215);
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error($exc->getMessage(),480);
        }
    }

    public function search(Request $request)
    {
        try {

            $this->validate($request,[
               'date_movement_ini' => 'required',
               'date_movement_end' => 'required'
            ]);
            $data = filterRequestAll($request->all());

            $result = $this->stockMovementRepository->get($data,[],true,false,false,400);

            return $this->success($result,'success',200);
        } catch (ValidationException $exc){
          return $this->success([],'Data de Inicio e data Final são obrigatórios',215);
        } catch (\Exception $exc){
            $this->error($exc->getMessage(),480);
        }
    }

    public function update($id,Request $request)
    {
        try{
            if($id != null && $id != ''){
                $this->validate($request,[
                    'type' => 'required',
                    'quantity_moved' => 'required',
                    'unitary_amount' => 'required',
                    'product_id' => 'required',
                    'bussiness_unit_id' => 'required',
                    'supplier_id' => 'required'
                ]);
                DB::beginTransaction();
                $data = (object) filterRequestAll($request->all());
                $ret = $this->stockMovementRepository->update($id,$data);

                if($ret){
                    DB::commit();
                    return $this->success([],'Movimento de Estoque atualizado com sucesso',200);
                }
                DB::rollBack();
                return $this->success([],'Erro ao atualizar Movimento de Estoque',215);
            }
            return $this->success([],'ID nulo',215);

        } catch (ValidationException $exc){
            DB::rollBack();
            return $this->success($exc->errors(),'Erro de validação',200);
        } catch (\Exception $exc){
            DB::rollBack();
            return $this->error('Erro ao atualizar Movimento de Estoque',480);
        }
    }

    public function delete($id)
    {
        try{
            if($id != '' && $id != null){
                DB::beginTransaction();
                $ret = $this->stockMovementRepository->remove($id);
                if($ret){
                    DB::commit();
                    return $this->success([],'Movimento de Estoque atualizado com sucesso');
                }
                DB::rollBack();
                return $this->success([],'Erro ao atualizar Movimento de Estoque',200);
            }
            return $this->success([],'ID nulo',200);
        }catch (\Exception $exc){
            DB::rollBack();
            return $this->error('Erro ao excluir Movimento de estoque',480);
        }
    }

    public function listStockMovement()
    {

    }

    public function saveXml(Request $request)
    {

    }
}
