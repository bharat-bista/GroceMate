<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>GROCEMATE PVT.LTD </title>
    <meta name="description" content="@yield('meta_description', 'Golden Vision Traders Pvt. Ltd provides premium CCTV systems and electronic goods for homes and businesses')">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'Golden Vision Traders PVT.ltd')">
    <meta property="og:description" content="@yield('og_description', 'Golden Vision Traders Pvt. Ltd provides premium CCTV systems and electronic goods for homes and businesses')">
    <meta property="og:image" content="@yield('og_image', asset('assets/img/logo/logo.png'))">

    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo/golden.png') }}">
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/slicknav.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/flaticon.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/gijgo.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/animated-headline.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/fontawesome-all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" ></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Font Awesome v6 (TikTok Supported) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<!-- Add to your head -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.css">



<meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="whitesmoke-bg">
    <script>
    $(window).on('load', function () {
        $('#preloader-active').delay(300).fadeOut('slow');
    });
</script>

<div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="{{asset('assets/img/logo/logo.png')}}" alt="">
                </div>
            </div>
        </div>
    </div>
<!-- ✅ WhatsApp + Viber Floating Buttons (moved higher) -->
<div class="floating-contact-buttons">
    <!-- WhatsApp -->
    <a href="https://wa.me/9849679085"
       class="contact-btn whatsapp"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Viber -->
    <a href="viber://chat?number=9849679085"
       class="contact-btn viber"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat on Viber">
        <i class="fab fa-viber"></i>
    </a>
</div>

<style>
/* Floating Buttons Wrapper - LIFTED HIGHER */
.floating-contact-buttons {
    position: fixed;
    right: 18px;
    bottom: 110px; /* ✅ moved up to avoid overlapping Go-To-Top button */
    display: flex;
    flex-direction: column;
    gap: 14px;
    z-index: 99999;
}

/* Round Buttons */
.contact-btn {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 28px;
    text-decoration: none;
    transition: transform .2s ease-in-out, box-shadow .2s;
    box-shadow: 0 4px 10px rgba(0,0,0,.25);
    -webkit-tap-highlight-color: transparent;
}

/* WhatsApp Green */
.contact-btn.whatsapp {
    background: #25D366;
}

/* Viber Purple */
.contact-btn.viber {
    background: #7360F2;
}

/* Hover effect */
.contact-btn:hover {
    transform: scale(1.12);
    box-shadow: 0 6px 14px rgba(0,0,0,.32);
}
/* ✅ Stop horizontal scrolling across the website */
html, body {
    overflow-x: hidden !important;
}

/* Mobile scaling */
@media (max-width: 450px) {
    .contact-btn {
        width: 46px;
        height: 46px;
        font-size: 23px;
    }

    /* ✅ also move slightly higher for very small screens */
    .floating-contact-buttons {
        bottom: 120px;
    }
}
</style>

    @include('frontend.layouts.header')
    @yield('main-content')
    @include('frontend.layouts.footer')

</body>
</html>
