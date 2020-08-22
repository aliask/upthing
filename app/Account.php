<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use NumberFormatter;

class Account extends Model
{
    public $timestamps = false;

    public function __construct($upAccount)
    {
        Log::debug("Creating account from " . json_encode($upAccount));
        $this->rawAccount = $upAccount;
        $this->upid = $upAccount->id;
        $this->name = $upAccount->attributes->displayName;
        $this->forceFill((array)$upAccount->attributes);
    }

    public function getTimestampAttribute() {
        return Carbon::parse($this->createdAt);
    }

    public function getBalanceFormattedAttribute() {
        $fmt = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($this->balance->valueInBaseUnits/100.0, $this->balance->currencyCode);
    }

    public function getIconAttribute() {
        switch($this->accountType) {
            case 'TRANSACTIONAL':
                return 'credit-card';
            case 'SAVER':
                return 'zap';
            default:
                return 'bar-chart-2';
        }
    }
}
