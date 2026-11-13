@php
    $rules = [
        ['key' => 'min', 'label' => __('auth-form.passwordRules.min')],
        ['key' => 'uppercase', 'label' => __('auth-form.passwordRules.uppercase')],
        ['key' => 'lowercase', 'label' => __('auth-form.passwordRules.lowercase')],
        ['key' => 'number', 'label' => __('auth-form.passwordRules.number')],
        ['key' => 'symbol', 'label' => __('auth-form.passwordRules.symbol')],
        ['key' => 'match', 'label' => __('auth-form.passwordRules.match')],
    ];
@endphp

<div
    x-data="{
        password: $wire.$entangle('data.password'),
        passwordConfirmation: $wire.$entangle('data.passwordConfirmation'),
        check(rule) {
            const pw = this.password || '';
            const conf = this.passwordConfirmation || '';

            switch (rule) {
                case 'min': return pw.length >= 8;
                case 'uppercase': return /[A-Z]/.test(pw);
                case 'lowercase': return /[a-z]/.test(pw);
                case 'number': return /[0-9]/.test(pw);
                case 'symbol': return /[!@#$%^&*(),.?':{}|<>]/.test(pw);
                case 'match': return pw.length > 0 && pw === conf;
                default: return false;
            }
        }
    }"
    class="py-1 px-2 rounded-2xl border border-primary-600 dark:border-primary-500 shadow-sm"
>
    <ul class="grid grid-cols-2 gap-x-6 gap-y-1 text-xs">
        @foreach ($rules as $rule)
            <li
                class="flex items-center gap-2 transition-colors duration-200"
                :class="check('{{ $rule['key'] }}') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500'"
            >
                <span
                    class="w-4 text-center inline-block font-bold"
                    x-text="check('{{ $rule['key'] }}') ? '✓' : '•'"
                ></span>

                <span class="truncate">{{ $rule['label'] }}</span>
            </li>
        @endforeach
    </ul>
</div>
