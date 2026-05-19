<article @php(post_class('single-post'))>
  <header class="single-post__header container--narrow">
    @include('partials.entry-meta')

    <h1 class="single-post__title p-name">
      {!! $title !!}
    </h1>

    @if (has_post_thumbnail())
      <figure class="single-post__featured">
        {!! get_the_post_thumbnail(null, 'large', ['class' => 'single-post__img']) !!}
      </figure>
    @endif
  </header>

  <div class="single-post__content container--narrow e-content">
    @php(the_content())
  </div>

  <div class="single-post__tags container--narrow">
    @if (has_tag())
      <div class="entry-tags">
        {!! get_the_tag_list('<span class="entry-tags__label">' . __('Tags:', 'fluxstack') . '</span> ') !!}
      </div>
    @endif
  </div>

  <nav class="post-navigation container--narrow">
    <div class="post-navigation__inner">
      <?php $prev = get_previous_post(); ?>
      <?php $next = get_next_post(); ?>

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

  @php(comments_template())
</article>
