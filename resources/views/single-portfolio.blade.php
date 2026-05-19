@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <?php
      $basic = get_field('basic_info');
      $project_info = get_field('project_info');
      $features_section = get_field('features_section');
      $key_details_section = get_field('key_details_section');
      $gallery_section = get_field('gallery_section');

      $client = $basic['client'] ?? '';
      $year = $basic['year'] ?? '';
      $location = $basic['location'] ?? '';
      $website = $basic['website'] ?? '';
      $description = $project_info['project_description'] ?? '';
      $features = $features_section['features'] ?? [];
      $key_details = $key_details_section['key_details'] ?? [];
      $gallery = $gallery_section['gallery'] ?? [];
      $types = get_the_terms(get_the_ID(), 'portfolio_type');
    ?>

    <article @php(post_class('portfolio-single'))>
      {{-- Hero / Header --}}
      <header class="portfolio-single__header">
        <div class="container--narrow">
          @if (!empty($types) && !is_wp_error($types))
            <div class="portfolio-single__types">
              @foreach ($types as $type)
                <span class="portfolio-single__type">{{ $type->name }}</span>
              @endforeach
            </div>
          @endif

          <h1 class="portfolio-single__title">{!! get_the_title() !!}</h1>

          @if (has_excerpt())
            <p class="portfolio-single__excerpt">{{ get_the_excerpt() }}</p>
          @endif
        </div>
      </header>

      {{-- Featured Image --}}
      @if (has_post_thumbnail())
        <div class="portfolio-single__hero container">
          <figure class="portfolio-single__featured">
            {!! get_the_post_thumbnail(null, 'large', ['class' => 'portfolio-single__img']) !!}
          </figure>
        </div>
      @endif

      <div class="portfolio-single__body container--narrow">
        {{-- Meta / Details Sidebar --}}
        @if ($client || $year || $location || $website)
          <aside class="portfolio-single__meta">
            @if ($client)
              <div class="portfolio-single__meta-item">
                <span class="portfolio-single__meta-label">{{ __('Client', 'fluxstack') }}</span>
                <span class="portfolio-single__meta-value">{{ $client }}</span>
              </div>
            @endif
            @if ($year)
              <div class="portfolio-single__meta-item">
                <span class="portfolio-single__meta-label">{{ __('Year', 'fluxstack') }}</span>
                <span class="portfolio-single__meta-value">{{ $year }}</span>
              </div>
            @endif
            @if ($location)
              <div class="portfolio-single__meta-item">
                <span class="portfolio-single__meta-label">{{ __('Location', 'fluxstack') }}</span>
                <span class="portfolio-single__meta-value">{{ $location }}</span>
              </div>
            @endif
            @if ($website)
              <div class="portfolio-single__meta-item">
                <span class="portfolio-single__meta-label">{{ __('Website', 'fluxstack') }}</span>
                <a href="{{ $website }}" class="portfolio-single__meta-link" target="_blank" rel="noopener">{{ preg_replace('#^https?://#', '', $website) }}</a>
              </div>
            @endif
          </aside>
        @endif

        {{-- Project Description --}}
        @if ($description)
          <div class="portfolio-single__content">
            {!! $description !!}
          </div>
        @endif

        {{-- Features --}}
        @if (!empty($features))
          <section class="portfolio-single__section">
            <h2 class="portfolio-single__section-title">{{ __('Features', 'fluxstack') }}</h2>
            <div class="portfolio-single__features">
              @foreach ($features as $feature)
                @if (!empty($feature['title']))
                  <div class="portfolio-single__feature">
                    <h3 class="portfolio-single__feature-title">{{ $feature['title'] }}</h3>
                    @if (!empty($feature['description']))
                      <p class="portfolio-single__feature-desc">{!! $feature['description'] !!}</p>
                    @endif
                  </div>
                @endif
              @endforeach
            </div>
          </section>
        @endif

        {{-- Key Details --}}
        @if (!empty($key_details))
          <section class="portfolio-single__section">
            <h2 class="portfolio-single__section-title">{{ __('Key Details', 'fluxstack') }}</h2>
            <dl class="portfolio-single__details">
              @foreach ($key_details as $detail)
                @if (!empty($detail['title']))
                  <div class="portfolio-single__detail">
                    <dt>{{ $detail['title'] }}</dt>
                    @if (!empty($detail['description']))
                      <dd>{!! $detail['description'] !!}</dd>
                    @endif
                  </div>
                @endif
              @endforeach
            </dl>
          </section>
        @endif
      </div>

      {{-- Gallery --}}
      @if (!empty($gallery))
        <section class="portfolio-single__gallery container">
          <h2 class="portfolio-single__section-title">{{ __('Gallery', 'fluxstack') }}</h2>
          <div class="portfolio-single__gallery-grid">
            @foreach ($gallery as $image)
              <figure class="portfolio-single__gallery-item">
                <img src="{{ $image['sizes']['medium_large'] ?? $image['url'] }}" alt="{{ $image['alt'] ?? '' }}" loading="lazy">
              </figure>
            @endforeach
          </div>
        </section>
      @endif

      {{-- Navigation --}}
      <nav class="post-navigation container--narrow">
        <div class="post-navigation__inner">
          <?php $prev = get_adjacent_post(false, '', true); ?>
          <?php $next = get_adjacent_post(false, '', false); ?>

          @if (!empty($prev))
            <a href="{{ get_permalink($prev) }}" class="post-navigation__link post-navigation__link--prev">
              <span class="post-navigation__label">&larr; {{ __('Previous', 'fluxstack') }}</span>
              <span class="post-navigation__title">{{ get_the_title($prev) }}</span>
            </a>
          @endif

          @if (!empty($next))
            <a href="{{ get_permalink($next) }}" class="post-navigation__link post-navigation__link--next">
              <span class="post-navigation__label">{{ __('Next', 'fluxstack') }} &rarr;</span>
              <span class="post-navigation__title">{{ get_the_title($next) }}</span>
            </a>
          @endif
        </div>
      </nav>
    </article>
  @endwhile
@endsection
