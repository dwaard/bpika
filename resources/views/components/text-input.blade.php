@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-primary-light focus:ring-primary-light rounded-md shadow-sm']) !!}>
