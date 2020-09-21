<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{

    /**
     * Show the form for editing the authenticated user.
     *
     * @return Application|Factory|Response|View
     */
    public function edit()
    {
        $user=Auth::user();

        return view('account.edit', compact('user'));
    }


    /**
     * Update the authenticated user in storage.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        Auth::user()->update($validated);

        return redirect('home')
            ->with('success', __("Your settings are updated"));
    }
}
