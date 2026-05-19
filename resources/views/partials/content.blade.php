<article @php(post_class('post-card'))>
  @if (has_post_thumbnail())
    <a href="{{ get_permalink() }}" class="post-card__image">
      {!! get_the_post_thumbnail(null, 'medium_large', ['class' => 'post-card__img']) !!}
    </a>
  @endif

  <div class="post-card__body">
    <header class="post-card__header">
      @include('partials.entry-meta')

      <h2 class="post-card__title">
        <a href="{{ get_permalink() }}">
          {!! $title !!}
        </a>
      </h2>
    </header>

    <div class="post-card__excerpt">
      @php(the_excerpt())
    </div>

    <a href="{{ get_permalink() }}" class="post-card__link">
      {{ __('Read more', 'fluxstack') }} &rarr;
    </a>
  </div>
</article>
