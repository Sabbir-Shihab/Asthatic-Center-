@if(($focusAreas ?? collect())->isNotEmpty())
<label>{{ t('Focus area') }}
    <select name="focus_area_id" class="input focus-area-select" required>
        <option value="">{{ t('Select a focus area') }}</option>
        @foreach($focusAreas as $option)
            <option value="{{ $option->id }}" @selected((string) old('focus_area_id', $selectedFocusId ?? '') === (string) $option->id)>{{ $option->localized('name') }}</option>
        @endforeach
    </select>
</label>
@endif
