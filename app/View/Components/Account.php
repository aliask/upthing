<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Account extends Component
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
    public function __construct($name, $balance, $upid)
    {
        $this->name = $name;
        $this->balance = $balance;
        $this->upid = $upid;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.account');
    }
}
