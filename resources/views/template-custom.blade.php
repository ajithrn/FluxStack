{{--
  Template Name: Page with Title
  Description: Displays the page title above block content.
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <article @php(post_class('page-content container'))>
      <header class="page-content__header">
        <h1 class="page-content__title">{!! get_the_title() !!}</h1>
      </header>

      <div class="page-content__body">
        @php(the_content())
      </div>
    </article>
  @endwhile
@endsection
