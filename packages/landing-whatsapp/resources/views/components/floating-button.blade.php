@once('landing-whatsapp-styles')
    <x-whatsapp::styles />
@endonce

<div @class($wrapperClasses)>
    @include('landing-whatsapp::components.partials.button-link')
</div>
