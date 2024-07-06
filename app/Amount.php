<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amount extends Model
{
    protected $attributes = [
        "currencyCode" => "AUD",
        "value" => "0.00",
        "valueInBaseUnits" => 0
    ];

    protected $fillable = [
        "currencyCode",
        "value",
        "valueInBaseUnits"
    ];
}
