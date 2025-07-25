<div>
    @include('components.onboarding-welcome')
    <form wire:submit.prevent="submit">
        {{ $this->form }}
    </form>
</div> 