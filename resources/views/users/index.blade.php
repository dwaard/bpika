@extends('layouts.page')

@section('article')
    <section class="box">
        <h1 class="title">{{ __('Users') }}</h1>
        <a class="button is-primary" href="{{ route('users.create') }}">
            <i class="fas fa-user-plus"></i>
        </a>
        <table class="table">
            <thead>
            <tr>
                <th><abbr title="Identifier">ID</abbr></th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('E-Mail Address') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user == Auth::user())
                            <button class="button is-small is-danger is-static" disabled>
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @else
                        <button class="button is-danger is-small modal-button" data-target="delete-{{ $user->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <div class="modal" id="delete-{{ $user->id }}">
                            <div class="modal-background"></div>
                            <div class="modal-card">
                                <form action="{{ route('users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <header class="modal-card-head has-background-danger-dark">
                                        <p class="modal-card-title has-text-white">{{ __('Delete :name?', ['name'=> $user->name]) }}</p>
                                    </header>
                                    <section class="modal-card-body has-background-danger-light">
                                        <!-- Content ... -->
                                        <p>{{ __('This action cannot be undone.') }}</p>
                                    </section>
                                    <footer class="modal-card-foot has-background-danger-dark">
                                        <button class="button is-danger" type="submit">{{ __('Delete') }}</button>
                                        <button class="button modal-cancel" type="button">{{ __('Cancel') }}</button>
                                    </footer>
                                </form>
                            </div>
                            <button class="delete modal-close" aria-label="close"></button>
                        </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection

@push('scripts')
    <script>
        for (let elem of document.querySelectorAll('.modal-button')) {
            console.log(elem);
            elem.addEventListener("click", function () {
                let target = document.getElementById(elem.dataset.target);
                document.documentElement.classList.add("is-clipped");
                target.classList.add("is-active");
            });
        }

        for (let elem of document.querySelectorAll('.modal-close, .modal-cancel')) {
            elem.addEventListener("click", function () {
                let target = elem.closest(".modal");
                document.documentElement.classList.remove("is-clipped");
                target.classList.remove("is-active");
            });
        }
    </script>
@endpush
