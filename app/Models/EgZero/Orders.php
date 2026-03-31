<?php

namespace App\Models\EgZero;

class Orders extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';


    const INSIDE_WEBIKE_ORDER = 0;
    const OUTSIDE_WEBIKE_ORDER = 1;

    const ALL_ORDER = "";

    const STOCK_PURCHASE = 2;
}
