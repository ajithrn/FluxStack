@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (! have_posts())
    <div class="container">
      <p>{{ __('No posts found.', 'fluxstack') }}</p>
    </div>
  @endif

  <div class="post-grid container">
    @while(have_posts()) @php(the_post())
      @includeFirst(['partials.content-' . get_post_type(), 'partials.content'])
    @endwhile
  </div>

  <div class="container">
    {!! get_the_posts_navigation() !!}
  </div>
@endsection
