<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Redirect;

class FailedStoreCalibrationRangeException extends Exception
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
            'title' => 'Failed to add calibration data.',
            'text' => 'If issue persists, please contact Administrator.'
        ]);
    }
}
