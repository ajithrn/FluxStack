<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class())>
    @php(wp_body_open())

    <div id="app" class="flex flex-col min-h-screen">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content', 'fluxstack') }}
      </a>

      @include('sections.header')

      <main id="main" class="main flex-grow">
