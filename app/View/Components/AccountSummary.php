<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AccountSummary extends Component
{

    /**
     * Account model
     * @var App\Account
     */
    public $account;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
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
