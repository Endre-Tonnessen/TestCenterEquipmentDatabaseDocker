<?php

namespace App\View\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReasonForChangeModal extends Component
{

    public $modalIdTarget;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $modalIdTarget)
    {
        $this->modalIdTarget = $modalIdTarget;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Application|Factory|View
     */
    public function render()
    {
        return view('components.reason-for-change-modal');
    }
}
