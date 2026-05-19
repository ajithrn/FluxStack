{{-- Mobile Navigation --}}
<div class="mobile-nav" id="mobile-nav" aria-hidden="true">
  <div class="mobile-nav__overlay" data-close-nav></div>
  <div class="mobile-nav__panel">
    <div class="mobile-nav__header">
      <a class="mobile-nav__brand" href="{{ home_url('/') }}">
        {!! $siteName !!}
      </a>
      <button class="mobile-nav__close" data-close-nav aria-label="{{ __('Close menu', 'fluxstack') }}">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>

    @if (has_nav_menu('primary_navigation'))
      <nav class="mobile-nav__menu" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'mobile-nav__list', 'echo' => false]) !!}
      </nav>
    @endif

    @php
      $cta_text = \App\site_setting('header_cta_text');
      $cta_url = \App\site_setting('header_cta_url');
    @endphp

    @if ($cta_text && $cta_url)
      <div class="mobile-nav__cta">
        <a href="{{ $cta_url }}" class="mobile-nav__btn">{{ $cta_text }}</a>
      </div>
    @endif
  </div>
</div>
