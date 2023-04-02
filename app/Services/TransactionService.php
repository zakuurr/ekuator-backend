<?php

namespace App\Services;
use App\Statics\Table;
use App\Exceptions\BadParameter;
use App\Exceptions\BadParameterException;
use App\Exceptions\EmptyDataException;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\RequestErrorException;
use App\Exceptions\StockProductEmpty;
use Illuminate\Support\Facades\DB;

class TransactionService extends BaseService
{
    private const PRODUCT_SELECT_COLUMN = [

        "user_id",
        "product_id",
        "price",
        "quantity",
        "admin_fee",
        "tax",
        "total",
        "created_at"
    ];

    private const PRODUCT_RELATION_SELECT_COLUMN = [

        Table::TRANSACTIONS . ".user_id",
        Table::TRANSACTIONS . ".product_id",
        Table::PRODUCTS .".price",
        Table::PRODUCTS .".name",
        Table::TRANSACTIONS .".quantity",
        Table::TRANSACTIONS .".admin_fee",
        Table::TRANSACTIONS .".tax",
        Table::TRANSACTIONS .".total",
        Table::TRANSACTIONS .".created_at"
    ];



    private object $dataUser;
    private object $congressDay;
    protected $repository;
    protected $userRepo;
    protected $congressDayRepo;

    public function __construct()
    {
        $this->repository = new TransactionRepository();
        $this->congressDayRepo = new ProductRepository();
    }
    /**
     * Description : use to get all data organization
     *
     * @return object of eloquent model
     */
    public function orderByUser(Request $request): object
    {
        $userId = auth()->user()->id;
        $params = $request->only('limit', 'sortby', 'orderby');

        if(!isset($params['orderby'])){
            throw new BadParameterException();
        }

        $data = $this->repository->orderByUser( $userId,$params['orderby'], $params['limit'],  $params['sortby']);
        if ($data->count() == 0) {
            throw new EmptyDataException();
            }


        return $data;
    }

    public function orderBy(Request $request): object
    {
        $params = $request->only('limit', 'sortby', 'orderby');

        if(!isset($params['orderby'])){
            throw new BadParameterException();
        }

        $data = $this->repository->orderBy($params['orderby'], $params['limit'],  $params['sortby']);
        if ($data->count() == 0) {
            throw new EmptyDataException();
            }


        return $data;
    }
    public function getAllDataPaginated(): object
    {
        $data = $this->repository->getAllDataPaginated(self::PRODUCT_SELECT_COLUMN);
        if ($data->count() == 0) {
            throw new EmptyDataException();
        }

        return $data;
    }


    /**
     * Description : use to get organization by id
     *
     * @param int $id of organization
     * @return object of eloquent model
     */
    public function getDataById(string $id): object
    {
        $data = $this->repository->getTransactionAdminById($id, self::PRODUCT_RELATION_SELECT_COLUMN);
        if (empty($data)) {
            throw new EmptyDataException();
        }
        return $data;
    }

    public function getTransactionUserById(string $id): object
    {
        $userId = auth()->user()->id;
        $data = $this->repository->getTransactionUserById($id,  $userId,self::PRODUCT_RELATION_SELECT_COLUMN);
        // var_dump($data);
        if (empty($data)) {
            throw new EmptyDataException();
        }
        return $data;
    }



    /**
     * Description : use to add new organization
     *
     * @param array $requestedData data that want to store
     * @return object of eloquent model
     */
    // public function addNewData(Request $request,array $requestedData): object
    // {
    //     $params = $request->only('product_id');

    //     $congressDay = $this->congressDayRepo->getDataById($id);

    //     $requestedData["user_id"] = auth()->user()->id;
    //     $requestedData["product_id"] =  $params['product_id'];
    //     $requestedData["price"] =  "20000";
    //     return $this->repository->addNewData($requestedData);
    // }

    public function addTransaction(string $id,array $requestedData): object
    {

        $product = $this->congressDayRepo->getDataById($id);



          $price = $product->price * $requestedData["quantity"];

          $tax = $price * 0.1;

          $adminFee = $price * 0.05 + $tax;

          $total = $price + $tax + $adminFee;

          DB::beginTransaction();
            // var_dump($product->price);
            $requestedData["user_id"] = auth()->user()->id;
            $requestedData["product_id"] =  $product->id;
            $requestedData["price"] =  $product->price;
            $requestedData["tax"] =  $tax;
            $requestedData["admin_fee"] =  $adminFee;
            $requestedData["total"] =  $total;

            if ($requestedData["quantity"] > $product->quantity) {

                if($product->quantity == 0){
                    throw new StockProductEmpty();
                }
                throw new StockProductEmpty();
            }

            $product->decrement('quantity', $requestedData["quantity"]);
            DB::commit();
        return $this->repository->addTransaction($id,$requestedData);
    }

    public function updateDataById(string $id, array $requestedData): object
    {
        $this->checkData($id);
        return $this->repository->updateDataById($id, $requestedData);
    }

    public function deleteDataById(string $id): bool
    {
        $this->checkData($id);
        return $this->repository->deleteDataById($id);
    }


    public function setDataCongressDay(object $dataCongressDay): void
    {
        $this->congressDay = $dataCongressDay;
    }

    public function getDataCongressDay(): object
    {
        return $this->congressDay;
    }



}
