@extends('layouts.page')

@section('article')
    <section class="box content">
        <h2 class="has-text-centered is-large">{{ $station->name }}</h2>
        <p>{{ __('The station has the identifying code of: :code.',
                    ['code' => $station->code]) }}</p>
        <p>{{ __('The station is in the city of: :city.',
                    ['city' => $station->city]) }}</p>
        <p>{{ __('The name of the station is: :name.',
                    ['name' => $station->name]) }}</p>
        <p>{{ __('The station uses this color in charts: :color.',
                    ['color' => $station->chart_color]) }}</p>
        <p>{{ __('The station has the following coordinates: latitude: :latitude, longitude: :longitude.',
                    ['latitude' => $station->latitude,
                    'longitude' => $station->longitude]) }}</p>
        <p>{{ __('The station is in the timezone of: :timezone.',
                    ['timezone' => $station->timezone]) }}</p>
        <p>{{ __('The station is visible in charts: :enabled.',
                    ['enabled' => $station->enabled === 1 ? __('Yes') : __('No')]) }}</p>
        <div class="is-grouped has-text-centered">
            <a class="button is-primary" href="{{ route('stations.index') }}">
                <i class="fas fa-list mr-2"></i>{{ __('Back to index') }}
            </a>
            <a class="button is-primary" href="{{ route('stations.edit', $station->code) }}">
                <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
            </a>
            <button class="button is-danger modal-button" data-target="delete-{{ $station->id }}">
                <i class="fas fa-trash-alt mr-2"></i>{{ __('Delete') }}
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
        </div>
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
