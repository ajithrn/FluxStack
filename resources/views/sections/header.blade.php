<header class="banner bg-white shadow-sm">
  <div class="container mx-auto px-4 py-4 flex items-center justify-between">
    <a class="brand text-xl font-bold" href="{{ home_url('/') }}">
      {!! $siteName !!}
    </a>

    @if (has_nav_menu('primary_navigation'))
      <nav class="nav-primary hidden md:block" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav flex gap-6', 'echo' => false]) !!}
      </nav>
    @endif

    <button class="mobile-nav__toggle md:hidden" data-open-nav aria-label="{{ __('Open menu', 'fluxstack') }}">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>
  </div>
</header>

@include('partials.navigation')
