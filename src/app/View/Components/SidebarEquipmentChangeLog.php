<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SidebarEquipmentChangeLog extends Component
{
    public $versionDateTime;
    public $changeDates;
    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($versionDateTime, $changeDates, $id)
    {
        $this->versionDateTime = $versionDateTime;
        $this->changeDates = $changeDates;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.sidebar-equipment-change-log');
    }
}
