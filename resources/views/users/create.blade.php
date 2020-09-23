@extends('layouts.page')

@section('article')
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <input type="text" name="name">
        <input type="text" name="email">
        <input type="submit" value="submit">
    </form>
@endsection
