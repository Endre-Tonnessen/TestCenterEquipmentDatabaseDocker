<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Redirect;

class EquipmentNotFoundException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function render($request)
    {
        return Redirect::to('/')->with('modalResponse', [
            'icon' => 'error',
            'title' => '404 - Could not find equipment.',
            'text' => 'If issue persists, please contact Administrator.'
        ]);
    }
}
