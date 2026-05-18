<header class="banner bg-white shadow-sm">
  <div class="container mx-auto px-4 py-4 flex items-center justify-between">
    <a class="brand text-xl font-bold" href="{{ home_url('/') }}">
      {!! $siteName !!}
    </a>

    @if (has_nav_menu('primary_navigation'))
      <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav flex gap-6', 'echo' => false]) !!}
      </nav>
    @endif
  </div>
</header>
