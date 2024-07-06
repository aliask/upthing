<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'description', 'upid', 'secret_key', 'action_type', 'action_url'
    ];

    public const action_types = [
        'http_get' => [
            'name'      => "HTTP Get Query",
            'handler'   => 'sendGet'
        ],
        'json_post' => [
            'name'      => "JSON POST",
            'handler'   => 'sendPost'
        ],
        'discord' => [
            'name'      => "Discord Notification",
            'handler'   => 'sendDiscord'
        ]
    ];

    protected $table = 'webhooks';
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getActionFriendlyAttribute() {
        if(array_key_exists($this->action_type, self::action_types)) {
            return self::action_types[$this->action_type]['name'];
        } else {
            return "Unknown type";
        }
    }

    public function getHandlerAttribute() {
        if(array_key_exists($this->action_type, self::action_types)) {
            return self::action_types[$this->action_type]['handler'];
        } else {
            return;
        }
    }
}
