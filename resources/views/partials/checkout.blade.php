@php
    $checkoutPayments = \App\Models\PaymentMethod::query()
        ->where('is_active', true)
        ->whereNotNull('account_number')
        ->where('account_number', '!=', '')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();
    $checkoutPaymentJson = $checkoutPayments->map(function ($method) {
        return [
            'id' => (string) $method->id,
            'name' => $method->name,
            'number' => $method->account_number,
            'amount' => (float) $method->advance_amount,
            'instructions' => $method->instructions,
        ];
    })->values();
@endphp
<script>window.svPayments = @json($checkoutPaymentJson);</script>
<div x-show="checkoutOpen" x-cloak style="display: none;" x-transition.opacity class="fixed inset-0 z-[60] grid place-items-center bg-black/65 p-4" @click.self="checkoutOpen=false">
    <form x-ref="checkoutForm" action="{{ route('checkout.store') }}" @submit.prevent="checkout($event.target)" class="max-h-[92vh] w-full max-w-xl overflow-auto rounded-3xl bg-[#fbf9f6] p-6 text-[#201624] sm:p-8">
        <div class="flex items-center justify-between">
            <h2 class="font-serif text-3xl">{{ t('Delivery details') }}</h2>
            <button type="button" @click="checkoutOpen=false" class="text-2xl">×</button>
        </div>
        <p class="mt-2 text-sm text-black/55">
            {{ t('Order total') }} <strong x-text="money(cartTotal)"></strong>
            <template x-if="selectedPayment"><span> · {{ t('Send advance') }} <strong x-text="money(selectedPayment.amount)"></strong></span></template>
        </p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <label class="text-xs">{{ t('Full name') }}<input class="input mt-2 w-full" name="customer_name" required></label>
            <label class="text-xs">{{ t('Email') }}<input class="input mt-2 w-full" type="email" name="email" required></label>
            <label class="text-xs">{{ t('Phone') }}<input class="input mt-2 w-full" name="phone" required></label>
            <label class="text-xs">{{ t('City') }}<input class="input mt-2 w-full" name="city" required></label>
            <label class="text-xs sm:col-span-2">{{ t('Delivery address') }}<textarea class="input mt-2 w-full" name="shipping_address" rows="3" required></textarea></label>
        </div>
        @if($checkoutPayments->isNotEmpty())
            <div class="mt-6">
                <p class="text-xs font-semibold">{{ t('Payment method') }}</p>
                <div class="mt-3 grid gap-2">
                    @foreach($checkoutPayments as $method)
                        <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-black/10 bg-white px-4 py-3 text-sm">
                            <span class="flex items-center gap-3">
                                <input type="radio" name="payment_method_id" value="{{ $method->id }}" x-model="paymentMethodId" required class="accent-[#2b1739]">
                                <span>
                                    <span class="block font-semibold">{{ $method->name }}</span>
                                    <span class="block text-xs text-black/50">{{ t('Send advance') }} ৳ {{ number_format($method->advance_amount, 0) }}</span>
                                </span>
                            </span>
                            <span class="text-sm font-semibold tracking-wide">{{ $method->account_number }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-3 rounded-2xl border border-[#e0a96d]/40 bg-[#f7f1ea] p-4" x-show="selectedPayment">
                    <p class="text-xs text-black/55">{{ t('Send this delivery advance, then enter the transaction ID.') }}</p>
                    <p class="mt-2 text-sm">{{ t('Send') }} <strong x-text="selectedPayment ? money(selectedPayment.amount) : ''"></strong> {{ t('to') }} <strong x-text="selectedPayment ? selectedPayment.name : ''"></strong></p>
                    <p class="mt-1 text-lg font-semibold tracking-wide" x-text="selectedPayment ? selectedPayment.number : ''"></p>
                    <p class="mt-1 text-xs text-black/50" x-show="selectedPayment && selectedPayment.instructions" x-text="selectedPayment ? selectedPayment.instructions : ''"></p>
                </div>
                <label class="mt-4 block text-xs">{{ t('Transaction ID') }}
                    <input class="input mt-2 w-full" name="transaction_id" required minlength="4" maxlength="40" pattern="[A-Za-z0-9]+" placeholder="{{ t('Example: 8A12BC34') }}" autocomplete="off">
                </label>
            </div>
            <button class="gold-button mt-5 w-full rounded-xl py-4 text-sm font-semibold" :disabled="checkoutBusy" x-text="checkoutBusy ? @js(t('Placing order…')) : @js(t('Submit transaction and order'))"></button>
        @else
            <input type="hidden" name="payment_method" value="cod">
            <p class="mt-4 text-xs text-black/50">{{ t('Cash on delivery') }}</p>
            <button class="gold-button mt-5 w-full rounded-xl py-4 text-sm font-semibold" :disabled="checkoutBusy" x-text="checkoutBusy ? @js(t('Placing order…')) : @js(t('Place cash-on-delivery order'))"></button>
        @endif
        <p x-show="checkoutMessage" class="site-notice site-notice-success mt-4" role="status"><span class="site-notice-icon">✓</span><span x-text="checkoutMessage"></span></p>
    </form>
</div>
