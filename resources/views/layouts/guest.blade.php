<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>{{ config('app.name', 'ForkNow') }}</title>

		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css" crossorigin="anonymous">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

		<style>
			.dark{color-scheme:dark}
			.dark body{background:linear-gradient(135deg,#111827 0%,#1f2937 50%,#111827 100%)!important;color:#f9fafb!important}
			.dark .card{background-color:rgba(31,41,55,.9)!important;border-color:rgba(55,65,81,.2)!important}
		</style>
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="bg-light">
		<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-4">
			<div class="position-absolute top-0 end-0 p-3">
				<button id="darkModeToggle" class="btn btn-light border"><i id="moonIcon" class="fa-regular fa-moon"></i><i id="sunIcon" class="fa-regular fa-sun d-none text-warning"></i></button>
			</div>

			<div class="mb-4">
				<a href="/" class="d-flex align-items-center gap-3 text-decoration-none">
					<div class="d-flex align-items-center justify-content-center rounded-3" style="width:64px;height:64px;background:linear-gradient(90deg,#ea580c,#dc2626);">
						<svg width="36" height="36" fill="#fff" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
					</div>
					<div class="d-flex flex-column">
						<span class="fw-bold fs-3" style="background:linear-gradient(90deg,#ea580c,#dc2626);-webkit-background-clip:text;background-clip:text;color:transparent;">ForkNow</span>
						<span class="small text-muted">Restaurant Management System</span>
					</div>
				</a>
			</div>

			<div class="card shadow-lg rounded-3" style="min-width:320px;max-width:420px;">
				<div class="card-body p-4">
					{{ $slot }}
				</div>
			</div>
		</div>

		<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		<script>
			(function(){const d=localStorage.getItem('darkMode')==='true';document.documentElement.classList.toggle('dark',d);updateIcons();})();
			document.getElementById('darkModeToggle').addEventListener('click',function(){const s=document.documentElement.classList.toggle('dark');localStorage.setItem('darkMode',s);updateIcons();});
			function updateIcons(){const d=document.documentElement.classList.contains('dark');document.getElementById('moonIcon').classList.toggle('d-none',d);document.getElementById('sunIcon').classList.toggle('d-none',!d);}
		</script>
	</body>
</html>
