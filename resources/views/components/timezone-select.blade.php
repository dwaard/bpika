@props(['disabled' => false, 'value'])

<select {{ $disabled ? 'disabled' : '' }}
{!! $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}>
@foreach($timezones as $continent => $continent_timezones)
{{-- Add an option group for each continent --}}
<optgroup label="{{ __($continent) }}">
    @foreach($continent_timezones as $timezone_name => $timezone_value)
        {{-- Add an option for each city of a continent --}}
        <option value="{{ $timezone_value }}"
                @if($timezone_value === $value) selected @endif>
            {{ __($timezone_name) }}
        </option>
    @endforeach
</optgroup>
@endforeach

</select>
