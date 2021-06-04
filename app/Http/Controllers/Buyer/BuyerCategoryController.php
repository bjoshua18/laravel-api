<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;
use Illuminate\Database\Eloquent\Collection;

class BuyerCategoryController extends ApiController
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
    public function index(Buyer $buyer)
    {
        $categories = Collection::make($buyer->transactions()->with('product.categories')
            ->get() // Obtenemos todas las transactions con los productos y sus categorias
            ->pluck('product.categories') // Solo obtenemos los vendedores
            ->collapse() // Juntar todas las colecciones de categorias en una
            ->unique('id') // Para que ninguna categoria se repita
            ->values() // Para limpiar los huecos de los vendedores eliminados
        );
        return $this->showAll($categories);
    }
}
