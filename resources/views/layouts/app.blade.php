<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>{{ config('app.name', 'ForkNow') }}</title>

		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

		<!-- Icons -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css" crossorigin="anonymous">

		<!-- Bootstrap 5 CDN -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

		<!-- Minimal utility CSS to keep existing classes functional -->
		<style>
			/* Spacing */
			.p-4{padding:1rem!important}.py-6{padding-top:1.5rem!important;padding-bottom:1.5rem!important}
			.px-4{padding-left:1rem!important;padding-right:1rem!important}
			/* Containers */
			.max-w-7xl{max-width:1320px!important}.mx-auto{margin-left:auto!important;margin-right:auto!important}
			/* Backgrounds & text */
			.bg-white{background:#fff!important}.bg-gray-100{background:#f1f3f5!important}.bg-gray-800{background:#343a40!important}.bg-gray-900{background:#212529!important}
			.text-gray-900{color:#212529!important}.text-gray-100{color:#f8f9fa!important}
			/* Borders & shadow */
			.shadow{box-shadow:0 .5rem 1rem rgba(0,0,0,.15)!important}
		</style>

		<!-- Dark mode -->
		<style>
			.dark{color-scheme:dark}
			.dark body{background-color:#111827!important;color:#f9fafb!important}
			.dark .bg-white{background-color:#1f2937!important}
		</style>

		<!-- Scripts -->
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
		<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
			@include('layouts.navigation')

			@isset($header)
				<header class="bg-white shadow">
					<div class="max-w-7xl mx-auto py-6 px-4">
						{{ $header }}
					</div>
				</header>
			@endisset

			<main>
				{{ $slot }}
			</main>
		</div>

		<!-- Alpine.js -->
		<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
		<!-- Bootstrap JS -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		<script>
			(function() {
				const darkMode = localStorage.getItem('darkMode') === 'true';
				if (darkMode) document.documentElement.classList.add('dark');
				else document.documentElement.classList.remove('dark');
			})();
			window.toggleDarkMode = function(){
				const isDark = document.documentElement.classList.toggle('dark');
				localStorage.setItem('darkMode', isDark);
				return isDark;
			}
		</script>
	</body>
</html>
