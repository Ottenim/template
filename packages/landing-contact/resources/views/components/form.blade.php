@once('landing-contact-styles')
    <x-contact::styles />
@endonce

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<form
    {{ $attributes
        ->class(['lp-card', 'lp-contact-form'])
        ->merge([
            'method' => 'POST',
            'action' => $action,
            'data-landing-contact-event' => $trackingEnabled ? $trackingEvent : null,
        ]) }}
>
    @csrf

    @if (session('landing_contact_success'))
        <p class="lp-success lp-contact-feedback" role="status">
            {{ session('landing_contact_success') }}
        </p>
    @endif

    @if ($errors->any())
        <p class="lp-error lp-contact-feedback" role="alert">
            Revise os campos destacados e tente novamente.
        </p>
    @endif

    @foreach ($fields as $name => $field)
        <label class="lp-label lp-contact-field lp-contact-field-{{ $name }}">
            <span>
                {{ $field['label'] }}

                @if ($field['required'])
                    <span class="lp-contact-required" aria-hidden="true">*</span>
                @endif
            </span>

            @if ($field['type'] === 'textarea')
                <textarea
                    class="lp-textarea @error($name) lp-contact-input-invalid @enderror"
                    name="{{ $name }}"
                    @if ($field['placeholder']) placeholder="{{ $field['placeholder'] }}" @endif
                    @if ($field['required']) required @endif
                    rows="{{ $field['rows'] ?? 5 }}"
                >{{ old($name) }}</textarea>
            @elseif ($field['type'] === 'select')
                <select
                    class="lp-select @error($name) lp-contact-input-invalid @enderror"
                    name="{{ $name }}"
                    @if ($field['required']) required @endif
                >
                    @if ($field['placeholder'])
                        <option value="">{{ $field['placeholder'] }}</option>
                    @endif

                    @foreach ($field['options'] as $option)
                        <option value="{{ $option['value'] }}" @selected(old($name) === $option['value'])>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
            @else
                <input
                    class="lp-input @error($name) lp-contact-input-invalid @enderror"
                    type="{{ $field['type'] }}"
                    name="{{ $name }}"
                    value="{{ old($name) }}"
                    @if ($field['placeholder']) placeholder="{{ $field['placeholder'] }}" @endif
                    @if ($field['autocomplete']) autocomplete="{{ $field['autocomplete'] }}" @endif
                    @if ($field['required']) required @endif
                >
            @endif

            @error($name)
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    @endforeach

    @if ($privacyConsent['enabled'])
        <label class="lp-contact-consent">
            <input
                type="checkbox"
                name="privacy_consent"
                value="1"
                @checked(old('privacy_consent'))
                @if ($privacyConsent['required']) required @endif
            >

            <span class="lp-muted">{{ $privacyConsent['label'] }}</span>
        </label>

        @error('privacy_consent')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    @endif

    @if ($sourcePage)
        <input type="hidden" name="source_page" value="{{ $sourcePage }}">
    @endif

    @if ($sourceUrl)
        <input type="hidden" name="source_url" value="{{ $sourceUrl }}">
    @endif

    @if ($honeypotEnabled)
        <label class="lp-contact-honeypot" aria-hidden="true" tabindex="-1">
            Website
            <input type="text" name="{{ $honeypotField }}" value="" autocomplete="off" tabindex="-1">
        </label>
    @endif

    <button class="lp-button lp-button-primary lp-contact-submit" type="submit">
        {{ $buttonLabel }}
    </button>
</form>
