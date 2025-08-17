<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'ForkNow') }} - Admin</title>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
	
	<!-- Icons -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css" crossorigin="anonymous">

	<!-- Bootstrap 5 CDN -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Minimal utility CSS to approximate commonly used Tailwind classes so existing views remain readable -->
	<style>
		/* Spacing */
		.p-2{padding:.5rem!important}.p-3{padding:.75rem!important}.p-4{padding:1rem!important}.p-6{padding:1.5rem!important}
		.px-2{padding-left:.5rem!important;padding-right:.5rem!important}.px-3{padding-left:.75rem!important;padding-right:.75rem!important}.px-4{padding-left:1rem!important;padding-right:1rem!important}
		.py-1{padding-top:.25rem!important;padding-bottom:.25rem!important}.py-2{padding-top:.5rem!important;padding-bottom:.5rem!important}.py-3{padding-top:.75rem!important;padding-bottom:.75rem!important}
		.m-0{margin:0!important}.mb-2{margin-bottom:.5rem!important}.mb-3{margin-bottom:1rem!important}.mb-4{margin-bottom:1.5rem!important}.mb-6{margin-bottom:2rem!important}
		.ms-2{margin-left:.5rem!important}.me-2{margin-right:.5rem!important}.mt-2{margin-top:.5rem!important}
		/* Display & flex */
		.flex{display:flex!important}.items-center{align-items:center!important}.justify-between{justify-content:space-between!important}.justify-center{justify-content:center!important}
		.hidden{display:none!important}
		/* Borders & radius */
		.border{border:1px solid #dee2e6!important}.border-gray-200{border-color:#e9ecef!important}.rounded{border-radius:.25rem!important}.rounded-lg{border-radius:.5rem!important}.rounded-xl{border-radius:.75rem!important}
		/* Backgrounds */
		.bg-white{background-color:#fff!important}.bg-gray-50{background-color:#f8f9fa!important}.bg-gray-100{background-color:#f1f3f5!important}.bg-gray-200{background-color:#e9ecef!important}
		/* Text */
		.text-gray-900{color:#212529!important}.text-gray-800{color:#343a40!important}.text-gray-700{color:#495057!important}.text-gray-600{color:#6c757d!important}.text-gray-500{color:#adb5bd!important}.text-gray-400{color:#ced4da!important}
		.text-white{color:#fff!important}
		.text-xs{font-size:.75rem!important}.text-sm{font-size:.875rem!important}.text-base{font-size:1rem!important}.text-lg{font-size:1.125rem!important}.text-xl{font-size:1.25rem!important}.text-2xl{font-size:1.5rem!important}.text-3xl{font-size:1.875rem!important}
		.font-medium{font-weight:500!important}.font-semibold{font-weight:600!important}.font-bold{font-weight:700!important}
		/* Shadows */
		.shadow{box-shadow:0 .5rem 1rem rgba(0,0,0,.15)!important}.shadow-sm{box-shadow:0 .125rem .25rem rgba(0,0,0,.075)!important}.shadow-lg{box-shadow:0 1rem 3rem rgba(0,0,0,.175)!important}
		/* Width helpers */
		.w-8{width:2rem!important}.w-10{width:2.5rem!important}.h-8{height:2rem!important}.h-10{height:2.5rem!important}
		/* Badges mimic */
		.bg-blue-100{background:#cfe2ff!important;color:#084298!important}.bg-yellow-100{background:#fff3cd!important;color:#664d03!important}.bg-purple-100{background:#e2d9f3!important;color:#3d2464!important}.bg-green-100{background:#d1e7dd!important;color:#0f5132!important}.bg-red-100{background:#f8d7da!important;color:#842029!important}
		/* Button colors used inline in views */
		.bg-blue-600{background:#0d6efd!important;color:#fff!important}.hover\:bg-blue-700:hover{background:#0b5ed7!important}
		.bg-green-600{background:#198754!important;color:#fff!important}.hover\:bg-green-700:hover{background:#157347!important}
		.bg-gray-600{background:#6c757d!important;color:#fff!important}.hover\:bg-gray-700:hover{background:#5c636a!important}
		.bg-red-600{background:#dc3545!important;color:#fff!important}.hover\:bg-red-700:hover{background:#bb2d3b!important}
		.bg-yellow-600{background:#ffc107!important;color:#212529!important}.hover\:bg-yellow-700:hover{background:#ffca2c!important}
		.rounded-lg{border-radius:.5rem!important}
		/* Containers */
		.max-w-7xl{max-width:1320px!important}.mx-auto{margin-left:auto!important;margin-right:auto!important}
		/* Utilities used in alerts */
		.transition-colors{transition:color .15s ease,background-color .15s ease,border-color .15s ease}
	</style>

	<!-- Dark mode adjustments (kept) -->
	<style>
		.dark{color-scheme:dark}
		.dark body{background-color:#111827!important;color:#f9fafb!important}
		.dark .bg-white{background-color:#1f2937!important}
		.dark .bg-gray-50{background-color:#111827!important}
		.dark .bg-gray-100{background-color:#1f2937!important}
		.dark .text-gray-900{color:#f9fafb!important}
		.dark .text-gray-800{color:#f3f4f6!important}
		.dark .text-gray-700{color:#d1d5db!important}
		.dark .border-gray-200{border-color:#374151!important}
		.spinner{border:2px solid #f3f3f3;border-top:2px solid #3498db;border-radius:50%;width:20px;height:20px;animation:spin 1s linear infinite}
		@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
		.modal-backdrop{backdrop-filter:blur(4px);background-color:rgba(0,0,0,.5)}
	</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
	<div class="min-h-screen d-flex flex-column bg-gray-50 dark:bg-gray-900">
		<!-- Navigation -->
		@include('layouts.navigation')

		<!-- Main Content -->
		<div class="flex-1">
			<main class="p-4 p-md-6">
				@if(session('success'))
					<div class="mb-3 mb-md-4 bg-green-100 border border-success text-success px-3 px-md-4 py-2 rounded d-flex align-items-center">
						<i class="fas fa-check-circle me-2"></i>
						<span>{{ session('success') }}</span>
					</div>
				@endif

				@if(session('error'))
					<div class="mb-3 mb-md-4 bg-red-100 border border-danger text-danger px-3 px-md-4 py-2 rounded d-flex align-items-center">
						<i class="fas fa-exclamation-circle me-2"></i>
						<span>{{ session('error') }}</span>
					</div>
				@endif

				@yield('content')
			</main>
		</div>
	</div>

	<!-- Loading Overlay -->
	<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-black bg-opacity-50 d-flex align-items-center justify-content-center z-3 d-none">
		<div class="bg-white dark:bg-gray-800 rounded p-4 d-flex align-items-center gap-2">
			<div class="spinner"></div>
			<span class="text-gray-700 dark:text-gray-300">Yuklanmoqda...</span>
		</div>
	</div>

	<!-- Alpine.js -->
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<!-- Bootstrap Bundle JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// Initialize dark mode immediately
		(function() {
			const darkMode = localStorage.getItem('darkMode') === 'true';
			if (darkMode) { document.documentElement.classList.add('dark'); }
			else { document.documentElement.classList.remove('dark'); }
		})();

		window.toggleDarkMode = function() {
			const isDark = document.documentElement.classList.toggle('dark');
			localStorage.setItem('darkMode', isDark);
			return isDark;
		};

		window.showLoading = function() { document.getElementById('loading-overlay').classList.remove('d-none'); }
		window.hideLoading = function() { document.getElementById('loading-overlay').classList.add('d-none'); }
	</script>
</body>
</html> 