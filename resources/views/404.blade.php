@extends('layouts.app')

@section('content')
  <div class="error-404 container--narrow">
    <h1 class="error-404__title">404</h1>
    <p class="error-404__message">{{ __('The page you are looking for does not exist or has been moved.', 'fluxstack') }}</p>
    <a href="{{ home_url('/') }}" class="error-404__link">
      &larr; {{ __('Back to home', 'fluxstack') }}
    </a>
  </div>
@endsection
