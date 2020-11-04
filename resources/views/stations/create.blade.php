@extends('layouts.backendpage')

@section('article')
    <div class="column is-offset-3-desktop is-6-desktop is-12-tablet">
        <div class="title">{{ __('Create a weather station') }}</div>
        <form method="POST" action="{{ route('stations.store') }}" class="box">
            @csrf
            {{method_field('POST')}}
            <div class="field">
                <label for="code" class="label">{{ __('Code') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="text" name="code" placeholder="{{ __('e.g. BM1') }}"
                           class="input @error('code') is-danger @enderror"
                           required autocomplete="name" id="code" autofocus>
                    <span class="icon is-small is-left">
                    <i class="fas fa-barcode"></i>
                </span>
                    @error('code')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('code')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="city" class="label">{{ __('City') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type="text" name="city" placeholder="{{ __('e.g. New York') }}"
                           class="input @error('city') is-danger @enderror"
                           required autocomplete="city" id="city">
                    <span class="icon is-small is-left">
                    <i class="fas fa-city"></i>
                </span>
                    @error('city')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('city')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="name" class="label">{{ __('Name') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type=text name="name" placeholder="{{ __('e.g. Washington Street') }}"
                           class="input @error('name') is-danger @enderror"
                           required autocomplete="name" id="name">
                    <span class="icon is-small is-left">
                    <i class="fas fa-home"></i>
                </span>
                    @error('name')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('name')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="chartColor" class="label">{{ __('Color') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type=color name="chartColor"
                           class="input @error('chartColor') is-danger @enderror"
                           required autocomplete="chartColor" id="chartColor">
                    <span class="icon is-small is-left">
                    <i class="fas fa-palette"></i>
                </span>
                    @error('chartColor')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('chartColor')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="latitude" class="label">{{ __('Latitude') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type=number name="latitude" placeholder="{{ __('e.g. 123') }}"
                           class="input @error('latitude') is-danger @enderror"
                           required autocomplete="latitude" id="latitude" step="any">
                    <span class="icon is-small is-left">
                    <i class="fas fa-map-marked-alt"></i>
                </span>
                    @error('latitude')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('latitude')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="longitude" class="label">{{ __('Longitude') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <input type=number name="longitude" placeholder="{{ __('e.g. 123') }}"
                           class="input @error('longitude') is-danger @enderror"
                           required autocomplete="longitude" id="longitude" step="any">
                    <span class="icon is-small is-left">
                    <i class="fas fa-map-marked-alt"></i>
                </span>
                    @error('longitude')
                    <span class="icon is-small is-right">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                    @enderror
                </div>
                @error('longitude')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="timezone" class="label">{{ __('Timezone') }}</label>
                <div class="control has-icons-left has-icons-right">
                    <select name="timezone" class="pl-5 mt-1 select @error('timezone') is-danger @enderror"
                            required id="timezone">
                        @foreach($timezones as $continent => $continent_timezones)
                            {{-- Add an option group for each continent --}}
                            <optgroup label="{{ __($continent) }}">
                                @foreach($continent_timezones as $timezone_name => $timezone_value)
                                    {{-- Add an option for each city of a continent --}}
                                    <option value="{{ $timezone_value }}"
                                        @if($timezone_value === 'Europe/Amsterdam') selected @endif>
                                        {{ __($timezone_name) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <span class="icon is-small is-left">
                        <i class="fas fa-globe"></i>
                    </span>
                    @error('timezone')
                    <span class="icon is-small is-right">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    @enderror
                </div>
                @error('timezone')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field">
                <label for="enabled" class="label">{{ __('Enabled') }}?</label>
                <div class="control">
                    <div class="radio">
                        <input name="enabled" type="radio" checked="checked" value="1">
                            {{ __('Yes') }}
                        <input name="enabled" type="radio" value="0">
                            {{ __('No') }}
                    </div>
                </div>
                @error('enabled')
                <p class="help is-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="field is-grouped">
                {{-- Here are the form buttons: save, reset and cancel --}}
                <div class="control">
                    <button type="submit" class="button is-primary">{{ __('Create') }}</button>
                </div>
                <div class="control">
                    <button type="reset" class="button is-warning">{{ __('Reset') }}</button>
                </div>
                <div class="control">
                    <a type="button" href="{{route('home')}}" class="button is-light">{{ __('Cancel') }}</a>
                </div>
            </div>
        </form>
@endsection
