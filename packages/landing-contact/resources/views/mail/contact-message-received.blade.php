<h1>Nova mensagem de contato</h1>

<dl>
    @foreach ($data as $label => $value)
        @continue($value === null || $value === '')

        <dt>{{ ucfirst(str_replace('_', ' ', $label)) }}</dt>
        <dd>
            @if ($label === 'message')
                {!! nl2br(e($value)) !!}
            @elseif (is_bool($value))
                {{ $value ? 'Sim' : 'Nao' }}
            @else
                {{ $value }}
            @endif
        </dd>
    @endforeach
</dl>

@if ($contactMessage)
    <p>ID do registro: {{ $contactMessage->getKey() }}</p>
@endif
