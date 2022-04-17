<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amount extends Model
{

    /**
     * Currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Decimal notation of dollar value
     *
     * @var string
     */
    protected $value;

    /**
     * How many cents
     *
     * @var int
     */
    protected $valueInBaseUnits;

    protected $fillable = [
        "currencyCode",
        "value",
        "valueInBaseUnits"
    ];
}
