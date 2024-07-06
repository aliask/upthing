<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
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
}
