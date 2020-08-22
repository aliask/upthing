<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use NumberFormatter;

class Account extends Model
{
    public $timestamps = false;

    public function __construct($upAccount)
    {
        $this->rawAccount = $upAccount;
        $this->upid = $upAccount->id;
        $this->name = $upAccount->attributes->displayName;
        $this->balance = $upAccount->attributes->balance;
        $this->createdAt = $upAccount->createdAt;
    }

    public function getTimestampAttribute() {
        return Carbon::parse($this->createdAt);
    }

    public function getBalanceFormattedAttribute() {
        $fmt = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($this->balance->valueInBaseUnits/100.0, $this->balance->currencyCode);
    }
}
