<h1>Novo lead capturado</h1>

<dl>
    @foreach ($data as $label => $value)
        @continue($value === null || $value === '')

        <dt>{{ ucfirst(str_replace('_', ' ', $label)) }}</dt>
        <dd>
            @if (is_bool($value))
                {{ $value ? 'Sim' : 'Nao' }}
            @else
                {{ $value }}
            @endif
        </dd>
    @endforeach
</dl>

@if ($lead)
    <p>ID do registro: {{ $lead->getKey() }}</p>
@endif
