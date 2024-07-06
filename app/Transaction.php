<?php

namespace App;

use App\Amount;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use NumberFormatter;

class Transaction extends Model
{
    public $timestamps = false;

    /* Default values of Attributes */
    protected $attributes = [
        "createdAt"         => "",
        "settledAt"         => "",
        "status"            => "PENDING",
        "amount"            => NULL,
        "description"       => "",
        "rawText"           => "",
        "rawTransaction"    => "",
        "category"          => "",
        "upid"              => ""
    ];

    protected $appends = [
        "date",
        "time",
        "timestamp",
        "debitCredit",
        "amountFormatted"
    ];

    /* Fields allowed to be populated by fill() in fromUpTransaction */
    protected $fillable = [
        "upid",
        "category",
        "createdAt",
        "settledAt",
        "description",
        "rawText",
        "status"
    ];

    /**
     * Build a Transaction from the JSON received from the Up API
     * 
     * @param object $upTransaction JSON object as received from Up API 
     * @return App\Transaction
     */
    public static function fromUpTransaction($upTransaction)
    {
        Log::debug("Creating Transaction from " . json_encode($upTransaction));
        $instance = new self();
        $instance->rawTransaction = $upTransaction;
        $instance->upid = $upTransaction->id;
        $instance->fill((array)$upTransaction->attributes);
        $instance->amount = new Amount((array)$upTransaction->attributes->amount);
        try {
            $instance->category = $upTransaction->relationships->category->data->id;
        } catch(ErrorException $e) {
            $instance->category = null;
        }
        return $instance;
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
        $amt = $this->attributes["amount"];
        return $fmt->formatCurrency($amt->valueInBaseUnits/100.0, $amt->currencyCode);
    }

    public function getDebitCreditAttribute() {
        return ($this->attributes["amount"]->valueInBaseUnits<0)?'debit':'credit';
    }
}
