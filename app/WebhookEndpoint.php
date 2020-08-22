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

    protected $table = 'webhooks';
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getActionFriendlyAttribute() {
        switch($this->action_type) {
            case "google_script_get":
                return 'Google Script GET';
            case "google_script_post":
                return 'Google Script POST';
            case "discord":
                return 'Discord Notification';
            default:
                return $this->action_type;
        }
    }
}
