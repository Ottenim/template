{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($entries as $entry)
    <url>
        <loc>{{ $entry['loc'] }}</loc>
        @if (! empty($entry['lastmod']))
            <lastmod>{{ \Illuminate\Support\Carbon::parse($entry['lastmod'])->toDateString() }}</lastmod>
        @endif
        @if (! empty($entry['changefreq']))
            <changefreq>{{ $entry['changefreq'] }}</changefreq>
        @endif
        @if ($entry['priority'] !== null)
            <priority>{{ number_format((float) $entry['priority'], 1, '.', '') }}</priority>
        @endif
    </url>
@endforeach
</urlset>
