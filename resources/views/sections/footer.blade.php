<footer class="site-footer">
  <div class="site-footer__inner container">
    <div class="site-footer__top">
      {{-- Brand / About --}}
      <div class="site-footer__brand">
        <a href="{{ home_url('/') }}" class="site-footer__logo-link">
          <span class="site-footer__name">{{ get_bloginfo('name') }}</span>
        </a>
        <?php $tagline = get_bloginfo('description'); ?>
        @if ($tagline)
          <p class="site-footer__tagline">{{ $tagline }}</p>
        @endif
      </div>

      {{-- Footer Navigation --}}
      @if (has_nav_menu('footer_navigation'))
        <nav class="site-footer__nav" aria-label="{{ wp_get_nav_menu_name('footer_navigation') }}">
          <h3 class="site-footer__heading">{{ __('Links', 'fluxstack') }}</h3>
          {!! wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'nav', 'echo' => false, 'depth' => 1]) !!}
        </nav>
      @endif

      {{-- Contact Info --}}
      <?php
        $phone = \App\site_setting('phone');
        $email = \App\site_setting('email');
        $address = \App\site_setting('address');
      ?>

      @if (!empty($phone) || !empty($email) || !empty($address))
        <div class="site-footer__contact">
          <h3 class="site-footer__heading">{{ __('Contact', 'fluxstack') }}</h3>
          @if (!empty($phone))
            <p><a href="tel:{{ $phone }}">{{ $phone }}</a></p>
          @endif
          @if (!empty($email))
            <p><a href="mailto:{{ $email }}">{{ $email }}</a></p>
          @endif
          @if (!empty($address))
            <p>{{ $address }}</p>
          @endif
        </div>
      @endif
    </div>

    <div class="site-footer__bottom">
      <p class="site-footer__copyright">
        &copy; {{ date('Y') }} {{ get_bloginfo('name') }}. {{ __('All rights reserved.', 'fluxstack') }}
      </p>
    </div>
  </div>
</footer>
