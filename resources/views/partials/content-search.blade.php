<article @php(post_class('search-result'))>
  <header class="search-result__header">
    <h2 class="search-result__title">
      <a href="{{ get_permalink() }}">
        {!! $title !!}
      </a>
    </h2>

    @includeWhen(get_post_type() === 'post', 'partials.entry-meta')
  </header>

  <div class="search-result__excerpt">
    @php(the_excerpt())
  </div>
</article>
