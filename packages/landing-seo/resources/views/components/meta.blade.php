@if ($includeTitle && $data['title'])
    <title>{{ $data['title'] }}</title>
@endif

@if ($data['description'])
    <meta name="description" content="{{ $data['description'] }}">
@endif

@if ($data['robots'])
    <meta name="robots" content="{{ $data['robots'] }}">
@endif

@if ($data['canonical_url'])
    <link rel="canonical" href="{{ $data['canonical_url'] }}">
@endif

@if ($data['favicon_url'])
    <link rel="icon" href="{{ $data['favicon_url'] }}">
@endif

@if ($data['open_graph_enabled'])
    @if ($data['locale'])
        <meta property="og:locale" content="{{ $data['locale'] }}">
    @endif

    @if ($data['site_name'])
        <meta property="og:site_name" content="{{ $data['site_name'] }}">
    @endif

    <meta property="og:type" content="{{ $data['og_type'] }}">

    @if ($data['og_title'])
        <meta property="og:title" content="{{ $data['og_title'] }}">
    @endif

    @if ($data['og_description'])
        <meta property="og:description" content="{{ $data['og_description'] }}">
    @endif

    @if ($data['canonical_url'])
        <meta property="og:url" content="{{ $data['canonical_url'] }}">
    @endif

    @if ($data['og_image'])
        <meta property="og:image" content="{{ $data['og_image'] }}">
    @endif
@endif

@if ($data['twitter_enabled'])
    <meta name="twitter:card" content="{{ $data['twitter_card'] }}">

    @if ($data['twitter_site'])
        <meta name="twitter:site" content="{{ $data['twitter_site'] }}">
    @endif

    @if ($data['twitter_title'])
        <meta name="twitter:title" content="{{ $data['twitter_title'] }}">
    @endif

    @if ($data['twitter_description'])
        <meta name="twitter:description" content="{{ $data['twitter_description'] }}">
    @endif

    @if ($data['twitter_image'])
        <meta name="twitter:image" content="{{ $data['twitter_image'] }}">
    @endif
@endif

@if ($schemaJson)
    <script type="application/ld+json">{!! $schemaJson !!}</script>
@endif
