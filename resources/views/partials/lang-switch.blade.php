<div class="lang-switch" role="group" aria-label="{{ t('Language') }}">
    <a href="{{ route('locale.switch', 'bn') }}" class="{{ app()->getLocale() === 'bn' ? 'is-active' : '' }}" hreflang="bn" lang="bn">BN</a>
    <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}" hreflang="en" lang="en">EN</a>
</div>
