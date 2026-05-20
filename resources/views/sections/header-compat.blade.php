<!doctype html>
<html @php(language_attributes())>
  <head>
    @include('partials.head')
  </head>

  <body @php(body_class())>
    @php(wp_body_open())

    <div id="app" class="flex flex-col min-h-screen">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content', 'fluxstack') }}
      </a>

      @include('sections.header')

      <main id="main" class="main flex-grow">
