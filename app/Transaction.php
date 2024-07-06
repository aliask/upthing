<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;

    public function __construct($upid, $attributes)
    {
        $this->upid = $upid;
        $this->forceFill((array)$attributes);
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
}
