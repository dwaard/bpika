@extends('layouts.page')

@section('article')
    <section class="box">
        <h1 class="title">{{ __('Stations') }}</h1>
        <a class="button is-primary" href="{{ route('stations.create') }}">
            <i class="fas fa-plus"></i>
        </a>
        <table class="table">
            <thead>
            <tr>
                <th><abbr title="Identifier">{{ __('Code') }}</abbr></th>
                <th>{{ __('City') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Color') }}</th>
                <th>{{ __('Latitude') }}</th>
                <th>{{ __('Longitude') }}</th>
                <th>{{ __('Timezone') }}</th>
                <th>{{ __('Enabled') }}?</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($stations as $station)
                <tr>
                    <td>
                        <a href="{{ route('stations.show', $station->code) }}">{{ $station->code }}</a>
                    </td>
                    <td>{{ $station->city }}</td>
                    <td>{{ $station->name }}</td>
                    <td style="background-color: {{ $station->chart_color }}"></td>
                    <td>{{ $station->latitude }}</td>
                    <td>{{ $station->longitude }}</td>
                    <td>{{ $station->timezone }}</td>
                    <td>{{ $station->enabled === 1 ? __('Yes') : __('No') }}</td>
                    <td>
                        <a class="button is-primary is-small" href="{{ route('stations.edit', $station->code) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        <button class="button is-danger is-small modal-button" data-target="delete-{{ $station->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <div class="modal" id="delete-{{ $station->id }}">
                            <div class="modal-background"></div>
                            <div class="modal-card">
                                <form action="{{ route('stations.destroy', $station) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <header class="modal-card-head has-background-danger-dark">
                                        <p class="modal-card-title has-text-white">{{ __('Delete :name?', ['name'=> $station->name]) }}</p>
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
