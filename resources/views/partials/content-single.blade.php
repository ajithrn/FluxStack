<article @php(post_class('single-post container'))>
  <header class="single-post__header">
    @include('partials.entry-meta')

    <h1 class="single-post__title">
      {!! $title !!}
    </h1>

    @if (has_post_thumbnail())
      <figure class="single-post__featured">
        {!! get_the_post_thumbnail(null, 'large', ['class' => 'single-post__img']) !!}
      </figure>
    @endif
  </header>

  <div class="single-post__content">
    @php(the_content())
  </div>

  @if (has_tag())
    <div class="single-post__tags">
      <div class="entry-tags">
        {!! get_the_tag_list('<span class="entry-tags__label">' . __('Tags:', 'fluxstack') . '</span> ') !!}
      </div>
    </div>
  @endif

  <nav class="post-navigation">
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

  <div class="single-post__comments">
    @php(comments_template())
  </div>
</article>
