<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
@php(do_action('get_header'))
@php(wp_head())

@vite(['resources/css/app.css', 'resources/js/app.js'])
