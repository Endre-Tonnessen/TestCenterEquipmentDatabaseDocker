<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Redirect;

class ArchiveException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function render($request)
    {
        return Redirect::back()->with('modalResponse', [
            'icon' => 'error',
            'title' => 'Failed to update equipment information.',
            'text' => 'Code: Archive01. If issue persists, please contact an Administrator.'
        ]);
    }
}
