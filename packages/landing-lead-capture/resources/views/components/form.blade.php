@once('landing-lead-capture-styles')
    <x-lead-capture::styles />
@endonce

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<form
    {{ $attributes
        ->class([
            'lp-lead-capture-form',
            'lp-lead-capture-form-'.$variant,
            'lp-card' => $framed,
            'lp-lead-capture-form-framed' => $framed,
        ])
        ->merge([
            'method' => 'POST',
            'action' => $action,
            'data-landing-lead-capture-event' => $trackingEnabled ? $trackingEvent : null,
        ]) }}
>
    @csrf

    @if (session('landing_lead_capture_success'))
        <p class="lp-success lp-lead-capture-feedback" role="status">
            {{ session('landing_lead_capture_success') }}
        </p>
    @endif

    @if ($errors->any())
        <p class="lp-error lp-lead-capture-feedback" role="alert">
            Revise os campos destacados e tente novamente.
        </p>
    @endif

    <div class="lp-lead-capture-fields">
        @foreach ($fields as $name => $field)
            <label class="lp-label lp-lead-capture-field lp-lead-capture-field-{{ $name }}">
                <span>
                    {{ $field['label'] }}

                    @if ($field['required'])
                        <span class="lp-lead-capture-required" aria-hidden="true">*</span>
                    @endif
                </span>

                @if ($field['type'] === 'select')
                    <select
                        class="lp-select @error($name) lp-lead-capture-input-invalid @enderror"
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
                        class="lp-input @error($name) lp-lead-capture-input-invalid @enderror"
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
    </div>

    @if ($privacyConsent['enabled'])
        <label class="lp-lead-capture-consent">
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

    @if ($source)
        <input type="hidden" name="source" value="{{ $source }}">
    @endif

    @if ($campaign)
        <input type="hidden" name="campaign" value="{{ $campaign }}">
    @endif

    @if ($tag)
        <input type="hidden" name="tag" value="{{ $tag }}">
    @endif

    @if ($honeypotEnabled)
        <label class="lp-lead-capture-honeypot" aria-hidden="true" tabindex="-1">
            Website
            <input type="text" name="{{ $honeypotField }}" value="" autocomplete="off" tabindex="-1">
        </label>
    @endif

    <button class="lp-button lp-button-primary lp-lead-capture-submit" type="submit">
        {{ $buttonLabel }}
    </button>
</form>
