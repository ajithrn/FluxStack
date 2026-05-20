@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (! have_posts())
    <div class="container">
      <p>{{ __('Sorry, no results were found.', 'fluxstack') }}</p>
      {!! get_search_form(false) !!}
    </div>
  @else
    <div class="search-results container">
      @while(have_posts()) @php(the_post())
        @include('partials.content-search')
      @endwhile

      {!! get_the_posts_navigation() !!}
    </div>
  @endif
@endsection
