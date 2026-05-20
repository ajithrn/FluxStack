<!doctype html>
<html @php(language_attributes())>
  <head>
    @include('partials.head')
  </head>

  <body @php(body_class())>
    @php(wp_body_open())

    <div id="app" class="site">
      <a class="skip-link" href="#main">
        {{ __('Skip to content', 'fluxstack') }}
      </a>

      @include('sections.header')

      <main id="main" class="site__main">
        @yield('content')
      </main>

      @include('sections.footer')
    </div>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
