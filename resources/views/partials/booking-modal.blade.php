@php
    $bookingServices = \App\Models\ServiceCategory::query()
        ->where('is_active', true)
        ->with(['focusAreas' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get()
        ->map(fn ($service) => [
            'slug' => $service->slug,
            'name' => t($service->name),
            'focus' => $service->focusAreas->map(fn ($area) => [
                'id' => $area->id,
                'name' => $area->localized('name'),
            ])->values(),
        ])->values();
@endphp
<div
    x-show="bookingOpen"
    x-cloak
    style="display: none;"
    x-transition.opacity
    class="fixed inset-0 z-[60] grid place-items-center bg-black/65 p-4"
    @click.self="bookingOpen=false; reset()"
    x-data="{
        step: 1,
        category: '',
        categoryLabel: '',
        focusId: '',
        focusLabel: '',
        services: {{ \Illuminate\Support\Js::from($bookingServices) }},
        areas() {
            const service = this.services.find((item) => item.slug === this.category);
            return service ? service.focus : [];
        },
        chooseService(service) {
            this.category = service.slug;
            this.categoryLabel = service.name;
            this.focusId = '';
            this.focusLabel = '';
            this.step = service.focus.length ? 2 : 3;
        },
        chooseFocus(area) {
            this.focusId = String(area.id);
            this.focusLabel = area.name;
            this.step = 3;
        },
        reset() {
            this.step = 1;
            this.category = '';
            this.categoryLabel = '';
            this.focusId = '';
            this.focusLabel = '';
        }
    }"
>
    <div class="max-h-[94vh] w-full max-w-2xl overflow-auto rounded-3xl bg-[#fbf9f6] p-6 text-[#201624] sm:p-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest text-[#ac7b52]">{{ t('Book consultation') }}</span>
                <h2 class="mt-2 font-serif text-3xl" x-text="step===1 ? @js(t('Choose a service')) : (step===2 ? @js(t('Choose a focus area')) : @js(t('Request your appointment.')))"></h2>
            </div>
            <button type="button" @click="bookingOpen=false; reset()" class="text-2xl" aria-label="{{ t('Close') }}">×</button>
        </div>
        <form action="{{ route('appointments.store') }}" method="POST" class="appointment-mobile-form mt-6">
            @csrf
            <input type="hidden" name="category" :value="category">
            <input type="hidden" name="focus_area_id" :value="focusId">
            <div x-show="step===1">
                <h3 class="text-sm font-semibold">{{ t('Select a service') }}</h3>
                <div class="booking-step-grid sm:grid-cols-2">
                    <template x-for="service in services" :key="service.slug">
                        <button type="button" class="booking-category-card" @click="chooseService(service)">
                            <span x-text="service.name"></span>
                            <small>{{ t('Choose focus') }}</small>
                        </button>
                    </template>
                </div>
            </div>
            <div x-show="step===2" x-cloak>
                <button type="button" class="booking-back" @click="step=1; focusId=''; focusLabel=''">{{ t('Back to services') }}</button>
                <div class="booking-selected">
                    <span class="text-xs uppercase tracking-widest text-[#ac7b52]">{{ t('Selected service') }}</span>
                    <strong class="mt-1 block font-serif text-2xl" x-text="categoryLabel"></strong>
                </div>
                <h3 class="text-sm font-semibold">{{ t('Select a focus area') }}</h3>
                <div class="booking-step-grid">
                    <template x-for="area in areas()" :key="area.id">
                        <button type="button" class="booking-focus-card" @click="chooseFocus(area)">
                            <span x-text="area.name"></span>
                            <small>{{ t('Book now') }}</small>
                        </button>
                    </template>
                </div>
            </div>
            <div x-show="step===3" x-cloak>
                <button type="button" class="booking-back" @click="areas().length ? step=2 : step=1">{{ t('Back to focus areas') }}</button>
                <div class="booking-selected">
                    <span class="text-xs uppercase tracking-widest text-[#ac7b52]">{{ t('Selected service') }}</span>
                    <strong class="mt-1 block font-serif text-2xl" x-text="categoryLabel"></strong>
                    <template x-if="focusLabel">
                        <p class="mt-3 text-sm text-[#5c465f]"><span class="text-xs uppercase tracking-widest text-[#ac7b52]">{{ t('Selected focus area') }}</span><br><b x-text="focusLabel"></b></p>
                    </template>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-xs sm:col-span-2" x-show="focusLabel">{{ t('Focus area') }}
                        <select class="input mt-2 w-full" disabled>
                            <option :value="focusId" x-text="focusLabel" selected></option>
                        </select>
                    </label>
                    <label class="text-xs">{{ t('Full name') }}<input class="input mt-2 w-full" name="client_name" required></label>
                    <label class="text-xs">{{ t('Phone') }}<input class="input mt-2 w-full" name="phone" required></label>
                    <label class="text-xs">{{ t('Email') }}<input class="input mt-2 w-full" type="email" name="email" required></label>
                    <label class="text-xs">{{ t('Preferred date/time') }}<input class="input mt-2 w-full" type="datetime-local" name="appointment_at" required></label>
                    <label class="text-xs sm:col-span-2">{{ t('Preferred specialist') }}
                        <select name="doctor_id" class="input mt-2 w-full">
                            <option value="">{{ t('Any available expert') }}</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ t($doctor->name) }} - {{ t($doctor->designation) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs sm:col-span-2">{{ t('Message') }}
                        <textarea class="input mt-2 w-full" name="notes" rows="3" :placeholder="focusLabel ? focusLabel : @js(t('Tell us what you need help with'))"></textarea>
                    </label>
                </div>
                <button class="gold-button mt-5 w-full rounded-xl py-3 text-sm font-semibold">{{ t('Request appointment') }}</button>
            </div>
        </form>
    </div>
</div>
