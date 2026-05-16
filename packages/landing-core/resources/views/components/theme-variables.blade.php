<style id="landing-core-theme-variables" data-theme="{{ $themeName }}">
    :root {
        @foreach ($variables as $name => $value)
            {{ $name }}: {{ $value }};
        @endforeach
    }
</style>
