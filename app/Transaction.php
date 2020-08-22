<?php

namespace App;

use Carbon\Carbon;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use NumberFormatter;

class Transaction extends Model
{
    public $timestamps = false;

    public function __construct($upTransaction)
    {
        Log::debug("Creating Transaction from " . json_encode($upTransaction));
        $this->rawTransaction = $upTransaction;
        $this->upid = $upTransaction->id;
        $this->forceFill((array)$upTransaction->attributes);

        try {
            $this->category = $upTransaction->relationships->category->data->id;
        } catch(ErrorException $e) {
            $this->category = null;
        }

    }

    public function getTimestampAttribute() {
        return Carbon::parse($this->createdAt);
    }

    public function getDateAttribute() {
        return $this->timestamp->format('D M jS');
    }

    public function getTimeAttribute() {
        return $this->timestamp->format('g:i A');
    }

    public function getAmountFormattedAttribute() {
        $fmt = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($this->amount->valueInBaseUnits/100.0, $this->amount->currencyCode);
    }

    public function getDebitCreditAttribute() {
        return ($this->amount->valueInBaseUnits<0)?'debit':'credit';
    }
}
