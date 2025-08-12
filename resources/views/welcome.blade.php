<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Primary Meta Tags -->
  <title>ForkNow – Restoran Boshqaruv Tizimi | AI Powered Restaurant Management</title>
  <meta name="title" content="ForkNow – Restoran Boshqaruv Tizimi | AI Powered Restaurant Management" />
  <meta name="description" content="ForkNow – zamonaviy AI texnologiyalari asosida qurilgan restoran boshqaruv tizimi. Buyurtmalar, menyu, mijozlar va kuryerlar boshqaruvi. Bepul sinab ko'ring!" />
  <meta name="keywords" content="restoran boshqaruv tizimi, buyurtma boshqaruvi, menyu boshqaruvi, kuryer boshqaruvi, AI restoran, cloud restoran, restoran texnologiyasi, O'zbekiston restoran, Toshkent restoran" />
  <meta name="author" content="ForkNow" />
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
  <meta name="language" content="uz, ru, en" />
  <meta name="revisit-after" content="7 days" />
  <meta name="rating" content="general" />
  <meta name="distribution" content="global" />
  <meta name="coverage" content="Worldwide" />
  <meta name="target" content="all" />
  <meta name="theme-color" content="#ff6b35" />
  <meta name="msapplication-TileColor" content="#ff6b35" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="ForkNow" />

  <!-- Canonical -->
  <link rel="canonical" href="{{ url()->current() }}" />

  <!-- Favicons -->
  <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml" />
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
  <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}" />
  <link rel="shortcut icon" href="{{ asset('icon.svg') }}" type="image/svg+xml" />

  <!-- Open Graph -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta property="og:title" content="ForkNow – Restoran Boshqaruv Tizimi | AI Powered Restaurant Management" />
  <meta property="og:description" content="ForkNow – zamonaviy AI texnologiyalari asosida qurilgan restoran boshqaruv tizimi. Buyurtmalar, menyu, mijozlar va kuryerlar boshqaruvi." />
  <meta property="og:image" content="{{ asset('icon.svg') }}" />
  <meta property="og:site_name" content="ForkNow" />
  <meta property="og:locale" content="uz_UZ" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:url" content="{{ url()->current() }}" />
  <meta name="twitter:title" content="ForkNow – Restoran Boshqaruv Tizimi | AI Powered Restaurant Management" />
  <meta name="twitter:description" content="ForkNow – zamonaviy AI texnologiyalari asosida qurilgan restoran boshqaruv tizimi." />
  <meta name="twitter:image" content="{{ asset('icon.svg') }}" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..900&display=swap" rel="stylesheet" />

  <!-- CSS Libs -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

  <!-- Styles -->
  <style>
    :root { --primary:#ff6b35; --secondary:#f7931e; --ink:#0d1321; --muted:#6c757d; }
    html,body{height:100%}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Helvetica Neue,Arial,Noto Sans,sans-serif}

    /* Navbar */
    .navbar{background:rgba(255,255,255,.92)!important;backdrop-filter:saturate(180%) blur(12px);transition:box-shadow .2s}
    .navbar.scrolled{box-shadow:0 6px 30px rgba(0,0,0,.08)}
    .logo-icon{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--secondary));display:grid;place-items:center;color:#fff}
    .brand-text{font-weight:800;color:var(--ink)}
    .brand-subtitle{font-size:.72rem;color:var(--muted)}

    /* Hero */
    .hero{position:relative;min-height:100vh;display:grid;place-items:center;background:linear-gradient(180deg,#0a0f1e,#0b1220);color:#fff;overflow:hidden}
    .tech-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(255,107,53,.09) 1px, transparent 1px),linear-gradient(90deg, rgba(255,107,53,.09) 1px, transparent 1px);background-size:48px 48px}

    /* Sections */
    .section{padding-block:88px}
    .section-title{font-weight:800;text-align:center}
    .section-sub{color:var(--muted);text-align:center;margin-bottom:2rem}

    /* Cards */
    .feature-card,.pricing-card{border:none;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s}
    .feature-card:hover,.pricing-card:hover{transform:translateY(-6px);box-shadow:0 20px 45px rgba(0,0,0,.08)}
    .icon-box{width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,var(--primary),var(--secondary));display:grid;place-items:center;margin:0 auto 14px;color:#fff}
    .cta{display:inline-flex;gap:.6rem;align-items:center;padding:.9rem 1.3rem;border-radius:999px;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;text-decoration:none;font-weight:700;box-shadow:0 10px 30px rgba(255,107,53,.35)}

    /* Contact */
    .contact-info-item{display:flex;gap:14px;align-items:center;padding:14px;border-radius:14px;background:linear-gradient(135deg, rgba(255,107,53,.08), rgba(247,147,30,.08));border:1px solid rgba(255,107,53,.15)}
    .contact-info-item .icon-box{margin:0}
    .contact-card{border:none;border-radius:20px;box-shadow:0 15px 45px rgba(0,0,0,.08);background:#fff}
    .form-control{border-radius:12px;border:2px solid #e9ecef;padding:12px 14px}
    .form-control:focus{border-color:var(--primary);box-shadow:0 0 0 .2rem rgba(255,107,53,.15)}
    .btn-gradient{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;border:none;border-radius:999px;padding:14px 22px;font-weight:700;box-shadow:0 10px 30px rgba(255,107,53,.35)}
    .btn-gradient:hover{transform:translateY(-2px)}

    /* Testimonials */
    .testimonial-card{border:none;border-radius:20px;box-shadow:0 12px 36px rgba(0,0,0,.06);height:100%}
    .testimonial-avatar{width:56px;height:56px;border-radius:50%;object-fit:cover}

    /* Footer */
    footer{background:#0d1321;color:#e6eaf2}
    footer a{color:#b7bdc9}
    footer a:hover{color:#fff}

    @media (prefers-reduced-motion: reduce){*{animation:none!important;transition:none!important}}
  </style>

  <!-- JSON-LD (Blade-safe) -->
  @php
    $ld = [
      '@context' => 'https://schema.org',
      '@type' => 'SoftwareApplication',
      'name' => 'ForkNow',
      'applicationCategory' => 'BusinessApplication',
      'operatingSystem' => 'Web',
      'offers' => ['@type' => 'Offer', 'price' => '29', 'priceCurrency' => 'USD'],
      'publisher' => ['@type' => 'Organization', 'name' => 'ForkNow', 'url' => url('/'), 'logo' => asset('icon.svg')],
      'description' => "AI asosida restoran boshqaruv tizimi – buyurtma, menyu, mijozlar va kuryerlar boshqaruvi."
    ];
  @endphp
  <script type="application/ld+json">@json($ld, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)</script>
</head>
<body class="d-flex flex-column h-100">
  <a href="#main" class="visually-hidden-focusable position-absolute top-0 start-0 m-3 p-2 bg-dark text-white rounded-2">Kontentga o'tish</a>

  <!-- Header -->
  <header class="sticky-top" role="banner" aria-label="Main navigation">
    <nav class="navbar navbar-expand-lg  fixed-top" id="topNav">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center text-decoration-none" href="{{ url('/') }}">
          <span class="logo-icon me-3" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" role="img" aria-label="ForkNow logo">
              <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
            </svg>
          </span>
          <span class="d-flex flex-column">
            <strong class="brand-text">ForkNow</strong>
            <small class="brand-subtitle">Restaurant Management</small>
          </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse" aria-controls="navCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navCollapse">
          <ul class="navbar-nav ms-auto align-items-lg-center">
            <li class="nav-item"><a class="nav-link" href="#features">Xususiyatlar</a></li>
            <li class="nav-item"><a class="nav-link" href="#pricing">Narxlar</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact">Aloqa</a></li>
            @if (Route::has('login'))
              <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Kirish</a></li>
              @if (Route::has('register'))
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0"><a class="btn cta px-3" href="{{ route('register') }}"><i class="bi bi-rocket-takeoff me-1"></i>Ro'yxatdan o'tish</a></li>
              @endif
            @endif
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main id="main" class="flex-shrink-0">
    <!-- Hero -->
    <section class="hero" role="region" aria-label="Modern tech restaurant scene">
      <div class="tech-grid" aria-hidden="true"></div>
      <div id="particles-js" class="position-absolute top-0 start-0 w-100 h-100" aria-hidden="true"></div>

      <div class="container position-relative" style="z-index:2">
        <div class="text-center mx-auto" style="max-width:860px">
          <h1 class="display-4 fw-bold" data-aos="fade-up">ForkNow</h1>
          <p class="lead opacity-75 mb-4" data-aos="fade-up" data-aos-delay="100">
            Restoranlaringizni raqamlashtiring – buyurtmalar, menyu, kuryerlar va hisobotlar barchasi bitta joyda.
          </p>
          @if (Route::has('register'))
            <a href="{{ route('register') }}" class="cta" data-aos="zoom-in" data-aos-delay="150" aria-label="Bepul boshlash">
              <i class="bi bi-rocket-takeoff me-1"></i>Bepul boshlash
            </a>
          @endif
        </div>
      </div>

      <div class="position-absolute top-0 start-0 w-100 h-100" id="three-stage" aria-hidden="true"></div>
    </section>

    <!-- Features -->
    <section id="features" class="section bg-light" role="region" aria-labelledby="features-heading">
      <div class="container">
        <div class="mb-4">
          <h2 id="features-heading" class="section-title">Nima uchun ForkNow?</h2>
          <p class="section-sub">Zamonaviy restoran boshqaruvi uchun barcha kerakli vositalar</p>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="50">
            <div class="card feature-card h-100">
              <div class="card-body text-center p-4">
                <div class="icon-box"><i class="bi bi-check-circle" aria-hidden="true"></i></div>
                <h5 class="fw-bold mb-2">Oson buyurtma boshqaruvi</h5>
                <p class="text-muted mb-0">Buyurtmalarni real vaqtda qabul qiling, kuryerlarga yuboring va mijozlar bilan aloqada bo'ling.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="120">
            <div class="card feature-card h-100">
              <div class="card-body text-center p-4">
                <div class="icon-box"><i class="bi bi-gear" aria-hidden="true"></i></div>
                <h5 class="fw-bold mb-2">Menyu boshqaruvi</h5>
                <p class="text-muted mb-0">Mahsulotlarni osongina qo'shing, tahrirlang va narxlarni yangilang.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="190">
            <div class="card feature-card h-100">
              <div class="card-body text-center p-4">
                <div class="icon-box"><i class="bi bi-graph-up" aria-hidden="true"></i></div>
                <h5 class="fw-bold mb-2">Batafsil hisobotlar</h5>
                <p class="text-muted mb-0">Savdo, mijozlar va kuryerlar haqida batafsil statistikalar.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="section" role="region" aria-labelledby="pricing-heading">
      <div class="container">
        <div class="text-center mb-4">
          <h2 id="pricing-heading" class="section-title">Narxlar</h2>
          <p class="section-sub">Har xil o'lchamdagi restoranlar uchun mos rejalardan tanlang</p>
        </div>

        <div class="row g-4 justify-content-center">
          <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="50">
            <div class="card pricing-card h-100">
              <div class="card-body text-center p-4">
                <h5 class="fw-bold">Boshlang'ich</h5>
                <div class="display-5 fw-bold text-warning mb-0">$0</div>
                <small class="text-muted">/ oy</small>
                <ul class="list-unstyled mt-3 mb-4 text-start mx-auto" style="max-width:260px">
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Cheksiz buyurtmalar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Asosiy hisobotlar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Telegram bot integratsiyasi 1 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Restoranlar soni 1 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Taomnomalar soni 5 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Taomlar soni 50 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Email qo'llab-quvvatlash</li>
                </ul>
                @if (Route::has('register'))
                  <a href="{{ route('register') }}" class="cta w-100 justify-content-center">Tanlash</a>
                @endif
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="120">
            <div class="card pricing-card featured h-100 position-relative">
              <span class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle-x mt-3 px-3 py-2">Eng mashhur</span><br>
              <div class="card-body text-center p-4">
                <h5 class="fw-bold">Professional</h5>
                <div class="display-5 fw-bold text-warning mb-0">$12</div>
                <small class="text-muted">/ oy</small>
                <ul class="list-unstyled mt-3 mb-4 text-start mx-auto" style="max-width:260px">
                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Cheksiz buyurtmalar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Asosiy hisobotlar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Telegram bot integratsiya 3 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Restoranlar soni 3 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Taomnomalar soni 10 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Taomlar soni 150 ta</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Email qo'llab-quvvatlash</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>24/7 qo'llab-quvvatlash</li>
                </ul>
                @if (Route::has('register'))
                  <a href="{{ route('register') }}" class="cta w-100 justify-content-center">Tanlash</a>
                @endif
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="190">
            <div class="card pricing-card h-100">
              <div class="card-body text-center p-4">
                <h5 class="fw-bold">Korxona</h5>
                <div class="display-5 fw-bold text-warning mb-0">$30</div>
                <small class="text-muted">/ oy</small>
                <ul class="list-unstyled mt-3 mb-4 text-start mx-auto" style="max-width:260px">
                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Cheksiz buyurtmalar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Asosiy hisobotlar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Telegram bot integratsiya cheksiz</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Restoranlar soni cheksiz</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Taomnomalar soni cheksiz</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Taomlar soni cheksiz</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Email qo'llab-quvvatlash</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>24/7 qo'llab-quvvatlash</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Ko'p filiallar</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>API integratsiyasi</li>
                  <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Maxsus yechimlar</li>
                </ul>
                <a href="#contact" class="cta w-100 justify-content-center">Bog'lanish</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="section bg-light" role="region" aria-labelledby="contact-heading">
      <div class="container">
        <div class="text-center mb-4">
          <h2 id="contact-heading" class="section-title">Biz bilan bog'laning</h2>
          <p class="section-sub">Savollaringiz bormi? Bizga yozing, tezda javob beramiz</p>
        </div>

        <div class="row g-4 align-items-stretch">
          <div class="col-lg-5">
            <div class="d-flex flex-column gap-3">
              <div class="contact-info-item">
                <div class="icon-box"><i class="bi bi-envelope"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Email</h6>
                  <p class="text-muted mb-0">
                    <a class="link-underline link-underline-opacity-0" href="mailto:info@forknow.uz">info@forknow.uz</a>
                  </p>
                </div>
              </div>
              <div class="contact-info-item">
                <div class="icon-box"><i class="bi bi-telephone"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Telefon</h6>
                  <p class="text-muted mb-0">
                    <a class="link-underline link-underline-opacity-0" href="tel:+998901234567">+998 90 123 45 67</a>
                  </p>
                </div>
              </div>
              <div class="contact-info-item">
                <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
                <div>
                  <h6 class="fw-bold mb-1">Manzil</h6>
                  <p class="text-muted mb-0">Toshkent shahri, O'zbekiston</p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="card contact-card h-100">
              <div class="card-body p-4 p-md-5">
                @if(session('status'))
                  <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                @endif
                <form class="contact-form" method="post" action="{{ Route::has('contact.submit') ? route('contact.submit') : '#' }}" novalidate>
                  @csrf
                  <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Ism</label>
                    <input id="name" name="name" type="text" class="form-control" placeholder="Ismingizni kiriting" required />
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" name="email" type="email" class="form-control" placeholder="Email manzilingizni kiriting" required />
                  </div>
                  <div class="mb-4">
                    <label for="message" class="form-label fw-semibold">Xabar</label>
                    <textarea id="message" name="message" class="form-control" rows="4" placeholder="Xabaringizni yozing" required></textarea>
                  </div>
                  <button type="submit" class="btn-gradient w-100">Yuborish</button>
                  <p class="small text-muted mt-2">Bu forma reCAPTCHA bilan himoyalangan bo'lishi mumkin. <a href="#">Maxfiylik</a> va <a href="#">Shartlar</a>.</p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="section" role="region" aria-labelledby="testimonials-heading">
      <div class="container">
        <div class="text-center mb-4">
          <h2 id="testimonials-heading" class="section-title">Mijozlar fikri</h2>
          <p class="section-sub">Biz haqimizda mijozlarimiz nima deyishadi</p>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
            <article class="card testimonial-card h-100 p-3">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <img src="{{ asset('images/review1.jpg') }}" alt="Mijoz surati" class="testimonial-avatar me-3">
                  <div>
                    <h6 class="mb-0">Dilshod R.</h6>
                    <small class="text-muted">Toshkent, Fast Food</small>
                  </div>
                </div>
                <div class="mb-2" aria-label="Reyting: 5 yulduz">
                  <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i>
                </div>
                <p class="mb-0">“ForkNow orqali buyurtmalarni boshqarish ancha tezlashdi. Hisobotlar aniq, xodimlar uchun oson.”</p>
              </div>
            </article>
          </div>

          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="80">
            <article class="card testimonial-card h-100 p-3">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <img src="{{ asset('images/review2.jpg') }}" alt="Mijoz surati" class="testimonial-avatar me-3">
                  <div>
                    <h6 class="mb-0">Madina S.</h6>
                    <small class="text-muted">Samarqand, Kafelar tarmog'i</small>
                  </div>
                </div>
                <div class="mb-2" aria-label="Reyting: 5 yulduz">
                  <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i>
                </div>
                <p class="mb-0">“Telegram bot integratsiyasi zo'r. Mijozlar bilan aloqa avtomatlashtirildi.”</p>
              </div>
            </article>
          </div>

          <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="160">
            <article class="card testimonial-card h-100 p-3">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <img src="{{ asset('images/review3.jpg') }}" alt="Mijoz surati" class="testimonial-avatar me-3">
                  <div>
                    <h6 class="mb-0">Javlon B.</h6>
                    <small class="text-muted">Buxoro, Oilaviy restoran</small>
                  </div>
                </div>
                <div class="mb-2" aria-label="Reyting: 4.5 yulduz">
                  <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-half text-warning"></i>
                </div>
                <p class="mb-0">“Qo'llab-quvvatlash tezkor. Ko'p filiallar uchun boshqaruv yengil bo'ldi.”</p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="section text-white" style="background:linear-gradient(135deg,var(--primary),var(--secondary))" role="region" aria-labelledby="cta-heading">
      <div class="container text-center">
        <h2 id="cta-heading" class="display-6 fw-bold mb-3">Restoranlaringizni bugun raqamlashtiring</h2>
        <p class="lead text-white-50 mb-4">ForkNow bilan birga bo'ling va restoran biznesingizni keyingi darajaga ko'taring</p>
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 py-3 fw-bold"><i class="bi bi-rocket-takeoff me-2"></i>Bepul boshlash</a>
        @endif
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="py-5 mt-auto" role="contentinfo" aria-label="Footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center mb-3">
            <div class="logo-icon me-3" aria-hidden="true"><i class="bi bi-egg-fried"></i></div>
            <span class="h4 fw-bold mb-0">ForkNow</span>
          </div>
          <p class="text-muted">Restoranlaringizni raqamlashtirish uchun zamonaviy yechimlar.</p>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold mb-3">Mahsulot</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#features">Xususiyatlar</a></li>
            <li class="mb-2"><a href="#pricing">Narxlar</a></li>
            <li class="mb-2"><a href="#contact">Aloqa</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold mb-3">Kompaniya</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#">Haqida</a></li>
            <li class="mb-2"><a href="#">Blog</a></li>
            <li class="mb-2"><a href="#">Karyera</a></li>
            <li class="mb-2"><a href="#">Yangiliklar</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold mb-3">Qo'llab-quvvatlash</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#">Yordam markazi</a></li>
            <li class="mb-2"><a href="#contact">Aloqa</a></li>
            <li class="mb-2"><a href="#">Status</a></li>
            <li class="mb-2"><a href="#">Xavfsizlik</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="fw-bold mb-3">Ijtimoiy tarmoqlar</h6>
          <nav class="d-flex gap-2" aria-label="Ijtimoiy tarmoqlar">
            <a href="#" class="text-muted fs-4" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-muted fs-4" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="text-muted fs-4" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-muted fs-4" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          </nav>
        </div>
      </div>

      <hr class="my-4 border-secondary" />

      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="text-muted mb-0">&copy; {{ date('Y') }} ForkNow. Barcha huquqlar himoyalangan.</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <a href="#" class="text-muted me-3">Maxfiylik siyosati</a>
          <a href="#" class="text-muted">Foydalanish shartlari</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- JS (defer) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js" defer></script>

  <script>
    document.addEventListener('DOMContentLoaded',function(){
      const nav=document.getElementById('topNav');
      const onScroll=()=>nav.classList.toggle('scrolled',window.scrollY>10);
      onScroll(); window.addEventListener('scroll',onScroll);

      document.querySelectorAll('a[href^="#"]').forEach(a=>{
        a.addEventListener('click',e=>{
          const id=a.getAttribute('href');
          if(id.length>1){ const el=document.querySelector(id); if(el){ e.preventDefault(); el.scrollIntoView({behavior:'smooth',block:'start'}); } }
        });
      });
    });

    window.addEventListener('load',()=>{ if(window.AOS) AOS.init({duration:800,once:true}); });

    window.addEventListener('load',()=>{ if(window.particlesJS){
      particlesJS('particles-js',{
        particles:{ number:{ value:70, density:{ enable:true, value_area:900 } },
          color:{ value:'#ff6b35' }, shape:{ type:'circle' }, opacity:{ value:.45 },
          size:{ value:3, random:true }, line_linked:{ enable:true, distance:140, color:'#ff6b35', opacity:.35, width:1 },
          move:{ enable:true, speed:1.2 } },
        interactivity:{ events:{ onhover:{ enable:true, mode:'repulse' }, resize:true } },
        retina_detect:true
      });
    }});

    window.addEventListener('load',()=>{ if(window.THREE){
      const mount=document.getElementById('three-stage');
      const scene=new THREE.Scene();
      const camera=new THREE.PerspectiveCamera(70, mount.clientWidth/mount.clientHeight, .1, 1000);
      const renderer=new THREE.WebGLRenderer({alpha:true,antialias:true});
      renderer.setSize(mount.clientWidth,mount.clientHeight);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio,2));
      renderer.setClearColor(0x000000,0);
      mount.appendChild(renderer.domElement);
      const geometry=new THREE.TorusKnotGeometry(3.5,.9,120,16);
      const material=new THREE.MeshBasicMaterial({ color:0xff6b35, wireframe:true, transparent:true, opacity:.25 });
      const mesh=new THREE.Mesh(geometry,material);
      scene.add(mesh); camera.position.z=10;
      function onResize(){ const w=mount.clientWidth, h=mount.clientHeight; renderer.setSize(w,h); camera.aspect=w/h; camera.updateProjectionMatrix(); }
      window.addEventListener('resize',onResize);
      (function animate(){ requestAnimationFrame(animate); mesh.rotation.x+=.004; mesh.rotation.y+=.006; renderer.render(scene,camera); })();
    }});
  </script>

  <noscript><div class="alert alert-warning text-center m-0" role="status">JavaScript o'chirilgan. Ba'zi animatsiyalar va interaktiv elementlar ishlamasligi mumkin.</div></noscript>
</body>
</html>
