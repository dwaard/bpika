@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-primary-light focus:ring focus:ring-primary-light focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
