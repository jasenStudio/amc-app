<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        $redirectUrl = $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : null;

        if ($redirectUrl) {
            return $redirectUrl;
        }

        return Gate::allows('manage-posts', $request->user())
            ? redirect()->intended(route('blog.index'))
            : redirect()->intended(route('dashboard'));
    }
}
