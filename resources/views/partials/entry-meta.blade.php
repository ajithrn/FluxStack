<div class="entry-meta">
  <time class="entry-meta__date dt-published" datetime="{{ get_post_time('c', true) }}">
    {{ get_the_date() }}
  </time>

  <span class="entry-meta__sep">&middot;</span>

  <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}" class="entry-meta__author p-author h-card">
    {{ get_the_author() }}
  </a>

  @if (has_category())
    <span class="entry-meta__sep">&middot;</span>
    <span class="entry-meta__categories">{!! get_the_category_list(', ') !!}</span>
  @endif
</div>
