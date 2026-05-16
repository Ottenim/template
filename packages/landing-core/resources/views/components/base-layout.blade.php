<!DOCTYPE html>
<html lang="{{ $lang }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }}</title>

        <x-landing-core::theme-variables />
        <x-landing-core::core-styles />

        {{ $head ?? '' }}

        @stack('landing-core-head')
    </head>
    <body class="lp-body {{ $bodyClass }}">
        {{ $slot }}

        @stack('landing-core-scripts')
    </body>
</html>
