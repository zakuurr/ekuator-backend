<?php

namespace App\Repositories;

use App\Models\Products;
use App\Models\Transactions;
use App\Statics\Table;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository
{
    protected  $model;
    protected  $modelProduct;
    public function __construct()
    {
        $this->model = new Transactions;
        $this->modelProduct = new Products();
    }



    public function orderByUser(string $id,string $order = 'asc',int $limit = self::LIMIT,

    string $sortby = 'name'): ?object
    {
        return $this->model = $this->model->where('user_id', $id)->orderBy($sortby,$order)->limit($limit)->get();

    }

    public function getTransactionUserById(string $id,string $userId,array $columns = ["*"]): ?object
    {
        return $this->model->select($columns)
            ->join(Table::PRODUCTS, Table::PRODUCTS . ".id", Table::TRANSACTIONS . ".product_id")
            ->where(Table::TRANSACTIONS . ".id", $id)->where("user_id", $userId)
            ->first();
    }

    public function getTransactionAdminById(string $id,array $columns = ["*"]): ?object
    {
        return $this->model->select($columns)
            ->join(Table::PRODUCTS, Table::PRODUCTS . ".id", Table::TRANSACTIONS . ".product_id")
            ->where(Table::TRANSACTIONS . ".id", $id)
            ->first();
    }




    public function addTransaction(string $id,array $requestedData): object
    {
        return $this->model->create($requestedData);
    }
}

?>
