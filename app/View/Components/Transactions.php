<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Transactions extends Component
{

    public $transactions;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.transactions');
    }
}
