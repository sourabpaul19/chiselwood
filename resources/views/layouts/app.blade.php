<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->
  <title>@yield('title', 'Office Portal')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('build/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('build/assets/css/responsive.css') }}">
  <style>
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }
    .stat-icon { font-size: 2rem; opacity: 0.9; }
    .section-divider {
      border-bottom: 1px solid #e9ecef;
      margin: 2rem 0 1rem;
      padding-bottom: .5rem;
    }
  </style>
  @stack('styles')
</head>
<body>
<main>
    @yield('body')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('build/assets/js/custom.js') }}" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this office?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@stack('scripts')
</body>
</html>
