@extends('layouts.app')

@section('content')
  <div class="page-header container">
    <h1 class="page-header__title">{{ post_type_archive_title('', false) }}</h1>
  </div>

  {{-- Taxonomy filter --}}
  <?php $portfolio_types = get_terms(['taxonomy' => 'portfolio_type', 'hide_empty' => true]); ?>
  @if (!empty($portfolio_types) && !is_wp_error($portfolio_types))
    <div class="portfolio-filter container">
      <a href="{{ get_post_type_archive_link('portfolio') }}" class="portfolio-filter__link {{ !is_tax() ? 'is-active' : '' }}">
        {{ __('All', 'fluxstack') }}
      </a>
      @foreach ($portfolio_types as $type)
        <a href="{{ get_term_link($type) }}" class="portfolio-filter__link {{ is_tax('portfolio_type', $type->slug) ? 'is-active' : '' }}">
          {{ $type->name }}
        </a>
      @endforeach
    </div>
  @endif

  @if (! have_posts())
    <div class="container">
      <p>{{ __('No portfolio items found.', 'fluxstack') }}</p>
    </div>
  @endif

  <div class="portfolio-grid container">
    @while(have_posts()) @php(the_post())
      <?php $types = get_the_terms(get_the_ID(), 'portfolio_type'); ?>
      <article @php(post_class('portfolio-card'))>
        <a href="{{ get_permalink() }}" class="portfolio-card__link">
          @if (has_post_thumbnail())
            <div class="portfolio-card__image">
              {!! get_the_post_thumbnail(null, 'medium_large', ['class' => 'portfolio-card__img']) !!}
            </div>
          @else
            <div class="portfolio-card__image portfolio-card__image--placeholder">
              <span class="portfolio-card__placeholder-icon">&#9733;</span>
            </div>
          @endif

          <div class="portfolio-card__overlay">
            <h2 class="portfolio-card__title">{!! get_the_title() !!}</h2>
            @if (!empty($types) && !is_wp_error($types))
              <span class="portfolio-card__type">{{ $types[0]->name }}</span>
            @endif
          </div>
        </a>
      </article>
    @endwhile
  </div>

  <div class="container">
    {!! get_the_posts_navigation() !!}
  </div>
@endsection
