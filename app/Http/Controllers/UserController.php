<?php

namespace App\Http\Controllers;

use App\Mail\UserInvited;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $users = User::all();

        return view('users.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email'
        ]);

        Mail::to($validated['email'])->send(new UserInvited($validated));

        return redirect(route('users.index'))
            ->with('success', __('Email is sent to invited user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\User  $user
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect(route('users.index'))->with(
            'success', __('User :name is successfully deleted.', [
                'name'=>$user->name
            ])
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getCurrentUser(Request $request) {

        return $request->user();
    }
}
