@props([
    'action' => 'submit',
    'formId' => null,
    'theme' => 'dark',
])

@php
    $recaptcha = app(\App\Services\RecaptchaService::class);
@endphp

@if ($recaptcha->isEnabled())
    @php
        $version = $recaptcha->version();
        $siteKey = $recaptcha->siteKey();
    @endphp

    @once('recaptcha-api-script')
        @push('scripts')
            @if ($version === 'v3')
                <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
            @else
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            @endif
        @endpush
    @endonce

    <div {{ $attributes->class(['space-y-2']) }}>
        @if ($version === 'v3')
            <input type="hidden" name="g-recaptcha-response" value="">
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const formId = @json($formId);
                    const form = formId ? document.getElementById(formId) : null;
                    if (!form || form.dataset.recaptchaBound === '1') {
                        return;
                    }
                    form.dataset.recaptchaBound = '1';
                    form.addEventListener('submit', function (event) {
                        if (form.dataset.recaptchaReady === '1') {
                            return;
                        }
                        event.preventDefault();
                        grecaptcha.ready(function () {
                            grecaptcha.execute(@json($siteKey), { action: @json($action) }).then(function (token) {
                                const input = form.querySelector('input[name="g-recaptcha-response"]');
                                if (input) {
                                    input.value = token;
                                }
                                form.dataset.recaptchaReady = '1';
                                if (typeof form.requestSubmit === 'function') {
                                    form.requestSubmit();
                                } else {
                                    form.submit();
                                }
                            });
                        });
                    });
                });
            </script>
            @error('g-recaptcha-response')
                <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        @else
            <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" data-theme="{{ $theme }}"></div>
            @error('g-recaptcha-response')
                <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        @endif
    </div>
@endif
