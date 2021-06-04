<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $transactions = Collection::make($category->products()
            ->whereHas('transactions') // Todos los productos que tengan al menos una transaccion
            ->with('transactions')
            ->get()
            ->pluck('transactions')
            ->collapse()
        );
        return $this->showAll($transactions);
    }
}
