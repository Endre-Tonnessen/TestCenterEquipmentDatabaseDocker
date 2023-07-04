<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CalibrationTableModal extends Component
{

    public $equipmentID;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($equipmentID)
    {
        $this->equipmentID = $equipmentID;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.calibration-table-modal');
    }
}
