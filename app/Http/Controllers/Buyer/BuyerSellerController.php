<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;
use Illuminate\Database\Eloquent\Collection;

class BuyerSellerController extends ApiController
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
        $sellers = Collection::make($buyer->transactions()->with('product.seller')
            ->get() // Obtenemos todas las transactions con los productos y sus vendedores
            ->pluck('product.seller') // Solo obtenemos los vendedores
            ->unique('id') // Para que ningun vendedor se repita
            ->values() // Para limpiar los huecos de los vendedores eliminados
        );
        return $this->showAll($sellers);
    }
}
