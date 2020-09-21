@extends('layouts.page')

@section('article')
    <table class="table">
        <thead>
        <tr>
            <th><abbr title="Identifier">ID</abbr></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('E-Mail Address') }}</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th><abbr title="Identifier">ID</abbr></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('E-Mail Address') }}</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach($users as $user)
        <tr>
            <th>{{ $user->id }}</th>
            <th>{{ $user->name }}</th>
            <th>{{ $user->email }}</th>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection
