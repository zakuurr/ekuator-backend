<?php

namespace App\Repositories;

use App\Models\Products;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    protected  $model;
    public function __construct()
    {
        $this->model = new Products;
    }
}

?>