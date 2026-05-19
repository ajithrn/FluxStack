<header class="site-header">
  <div class="site-header__inner container">
    <a class="site-header__brand" href="{{ home_url('/') }}">
      <?php $logo = \App\site_setting('logo'); ?>
      @if ($logo)
        <img src="{{ $logo }}" alt="{{ $siteName }}" class="site-header__logo">
      @else
        <span class="site-header__name">{!! $siteName !!}</span>
      @endif
    </a>

    @if (has_nav_menu('primary_navigation'))
      <nav class="site-header__nav" aria-label="{{ wp_get_nav_menu_name('primary_navigation') ?: __('Main Navigation', 'fluxstack') }}">
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
      </nav>
    @endif

    <?php
      $cta_text = \App\site_setting('header_cta_text');
      $cta_url = \App\site_setting('header_cta_url');
    ?>

    @if (!empty($cta_text) && !empty($cta_url))
      <a href="{{ $cta_url }}" class="site-header__cta">{{ $cta_text }}</a>
    @endif

    <button class="site-header__toggle" data-open-nav aria-label="{{ __('Open menu', 'fluxstack') }}">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>
  </div>
</header>

@include('partials.navigation')
