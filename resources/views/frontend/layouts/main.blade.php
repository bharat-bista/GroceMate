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
    window.addEventListener('load', function () {
        const preloader = document.getElementById('preloader-active');
        if (!preloader) {
            return;
        }

        window.setTimeout(function () {
            preloader.style.transition = 'opacity 0.3s ease';
            preloader.style.opacity = '0';

            window.setTimeout(function () {
                preloader.style.display = 'none';
            }, 300);
        }, 300);
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

<script>
(function () {
    const CART_KEY = 'gm_cart_items';
    const BUY_NOW_KEY = 'gm_buy_now_item';
    const LEGACY_BUY_NOW_KEY = 'buynow';

    function parseCart(raw) {
        try {
            const parsed = JSON.parse(raw || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (_) {
            return [];
        }
    }

    function parseBuyNow(raw) {
        try {
            const parsed = JSON.parse(raw || '{}');
            return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : null;
        } catch (_) {
            return null;
        }
    }

    function getItems() {
        return parseCart(localStorage.getItem(CART_KEY));
    }

    function saveItems(items) {
        localStorage.setItem(CART_KEY, JSON.stringify(items));
        window.dispatchEvent(new CustomEvent('gm-cart-updated', { detail: { count: items.length } }));
    }

    function normalizeId(value) {
        return String(value ?? '').trim();
    }

    function toMoney(value) {
        return Math.round((Number(value || 0) + Number.EPSILON) * 100) / 100;
    }

    function normalizePrice(value) {
        if (typeof value === 'number') {
            return toMoney(value);
        }
        const numeric = String(value ?? '').replace(/[^0-9.-]/g, '');
        return toMoney(Number.parseFloat(numeric) || 0);
    }

    function normalizeProduct(item) {
        return {
            id: normalizeId(item?.id),
            name: String(item?.name || 'Product'),
            price: normalizePrice(item?.price),
            image: String(item?.image || ''),
            qty: Math.max(1, Number(item?.qty || 1)),
        };
    }

    function showToast(title, icon) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            });
            return;
        }

        alert(title);
    }

    function updateBadges() {
        const count = getItems().length;
        document.querySelectorAll('.gm-cart-count').forEach((badge) => {
            badge.textContent = String(count);
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
        });
    }

    function addItem(item) {
        const items = getItems();
        const normalized = normalizeProduct(item);
        const id = normalized.id;

        if (!id) {
            return { added: false, reason: 'invalid' };
        }

        const exists = items.some((existing) => normalizeId(existing.id) === id);
        if (exists) {
            return { added: false, reason: 'exists' };
        }

        items.push(normalized);

        saveItems(items);
        return { added: true };
    }

    function removeItem(itemId) {
        const id = normalizeId(itemId);
        const items = getItems().filter((item) => normalizeId(item.id) !== id);
        saveItems(items);
    }

    function updateQty(itemId, qty) {
        const id = normalizeId(itemId);
        const parsedQty = Math.max(1, Number(qty || 1));
        const items = getItems();
        const target = items.find((item) => normalizeId(item.id) === id);
        if (!target) {
            return;
        }
        target.qty = parsedQty;
        saveItems(items);
    }

    function setItems(items) {
        const normalizedItems = Array.isArray(items)
            ? items.map(normalizeProduct).filter((item) => item.id)
            : [];
        saveItems(normalizedItems);
    }

    function getBuyNowItem() {
        const directItem = parseBuyNow(localStorage.getItem(BUY_NOW_KEY));
        const legacyItem = parseBuyNow(localStorage.getItem(LEGACY_BUY_NOW_KEY));
        const selected = directItem?.id ? directItem : legacyItem;

        if (!selected || !selected.id) {
            return null;
        }

        return normalizeProduct(selected);
    }

    function setBuyNowItem(item) {
        const normalized = normalizeProduct(item);

        if (!normalized.id) {
            return { saved: false, reason: 'invalid' };
        }

        localStorage.setItem(BUY_NOW_KEY, JSON.stringify(normalized));
        localStorage.removeItem(LEGACY_BUY_NOW_KEY);
        return { saved: true, item: normalized };
    }

    function clearBuyNowItem() {
        localStorage.removeItem(BUY_NOW_KEY);
        localStorage.removeItem(LEGACY_BUY_NOW_KEY);
    }

    window.GroceMateCart = {
        getItems,
        setItems,
        addItem,
        removeItem,
        updateQty,
        updateBadges,
        showToast,
        getBuyNowItem,
        setBuyNowItem,
        clearBuyNowItem,
    };

    function readDatasetItem(source) {
        return {
            id: normalizeId(source.dataset.productId || source.dataset.id),
            name: source.dataset.productName || source.dataset.name || 'Product',
            price: normalizePrice(source.dataset.productPrice || source.dataset.price || 0),
            image: source.dataset.productImage || source.dataset.image || '',
            qty: 1,
        };
    }

    document.addEventListener('click', function (event) {
        const cartIconBtn = event.target.closest('.gm-cart-icon-badge[data-product-id], .gm-cart-icon-badge[data-id]');
        if (!cartIconBtn) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const product = readDatasetItem(cartIconBtn);
        const result = addItem(product);

        if (result.added) {
            showToast('Product added to cart', 'success');
        } else if (result.reason === 'exists') {
            showToast('This product is already in cart', 'warning');
        } else {
            showToast('Unable to add product to cart', 'error');
        }

        updateBadges();
    });

    window.addEventListener('storage', function (event) {
        if (event.key === CART_KEY) {
            updateBadges();
        }
    });

    window.addEventListener('gm-cart-updated', updateBadges);
    document.addEventListener('DOMContentLoaded', updateBadges);
    updateBadges();
})();
</script>

</body>
</html>
