@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <article @php(post_class('page-content'))>
      <header class="page-content__header container--narrow">
        <h1 class="page-content__title">{!! get_the_title() !!}</h1>
      </header>

      <div class="page-content__body container--narrow">
        @php(the_content())
      </div>
    </article>
  @endwhile
@endsection
