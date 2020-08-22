<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Webhook extends Component
{
    public $webhook;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.webhook');
    }
}
