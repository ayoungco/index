<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostLoginRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->intended(AuthRedirect::resolveTarget($request));
    }
}
