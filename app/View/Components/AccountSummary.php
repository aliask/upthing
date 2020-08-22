<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AccountSummary extends Component
{

    /**
     * Account name
     * @var string
     */
    public $name;

    /**
     * Account balance, in format 123.45
     * @var string
     */
    public $balance;

    /**
     * UPid
     * @var string
     */
    public $upid;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($account)
    {
        $this->name = $account->attributes->displayName;
        $this->balance = $account->attributes->balance->value;
        $this->upid = $account->id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.account-summary');
    }
}
