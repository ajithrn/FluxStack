<footer class="content-info bg-gray-900 text-white mt-auto">
  <div class="container mx-auto px-4 py-8">
    @php(dynamic_sidebar('sidebar-footer'))

    <div class="text-center text-sm text-gray-400 mt-8">
      &copy; {{ date('Y') }} {{ get_bloginfo('name') }}
    </div>
  </div>
</footer>
