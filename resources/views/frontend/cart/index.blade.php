@extends('frontend.layouts.main')

@section('main-content')
{{-- ============================================= --}}
{{-- DARAZ-STYLE CART PAGE - MODERN DESIGN --}}
{{-- ============================================= --}}

<style>
/* ==========================================
   GROCEMATE CART - HOME THEME CONSISTENCY
   ========================================== */
:root {
    --gm-primary: #2E7D32;
    --gm-primary-dark: #1B5E20;
    --gm-primary-light: #4CAF50;
    --gm-accent: #FF6B35;
    --gm-accent-dark: #E55A2B;
    --gm-white: #FFFFFF;
    --gm-light: #F8FBF8;
    --gm-gray: #6B7280;
    --gm-gray-light: #E5E7EB;
    --gm-dark: #1F2937;
    --gm-star: #FACA51;
    --gm-shadow: 0 6px 24px rgba(27, 94, 32, 0.08);
    --gm-shadow-hover: 0 12px 30px rgba(27, 94, 32, 0.12);
    --gm-radius: 16px;
    --gm-border-soft: rgba(46, 125, 50, 0.18);
    --gm-surface: linear-gradient(135deg, #ffffff 0%, #f8fdf9 100%);
}

.cart-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px 16px;
    background:
        radial-gradient(circle at 12% 8%, rgba(46, 125, 50, 0.08), transparent 34%),
        radial-gradient(circle at 90% 22%, rgba(255, 107, 53, 0.07), transparent 30%),
        linear-gradient(180deg, #f6faf6 0%, #f9fcf9 100%);
    min-height: 100vh;
}

.cart-header,
.cart-items-section,
.order-summary,
.empty-cart {
    background: var(--gm-surface);
    border: 2px solid var(--gm-border-soft);
    border-radius: var(--gm-radius);
    box-shadow: var(--gm-shadow);
    position: relative;
    overflow: hidden;
}

.cart-header::before,
.cart-items-section::before,
.order-summary::before,
.empty-cart::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gm-primary), #1e7e34 50%, var(--gm-primary-light));
    background-size: 200% 100%;
    animation: cartGradientShift 4s ease infinite;
}

@keyframes cartGradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.cart-header {
    padding: 20px 24px;
    margin-bottom: 18px;
}

.cart-header h1 {
    margin: 0;
    font-size: clamp(1.35rem, 3vw, 1.95rem);
    color: var(--gm-dark);
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cart-header h1 i {
    color: var(--gm-primary);
}

.cart-breadcrumb {
    margin-top: 8px;
    font-size: 0.9rem;
    color: var(--gm-gray);
}

.cart-breadcrumb a {
    color: var(--gm-primary);
    text-decoration: none;
    font-weight: 600;
}

.cart-breadcrumb a:hover {
    color: var(--gm-primary-dark);
    text-decoration: underline;
}

.cart-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 18px;
}

.cart-items-section {
    padding: 20px;
}

.cart-items-header {
    border-bottom: 1px solid var(--gm-gray-light);
    padding-bottom: 14px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.cart-items-header h2 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--gm-dark);
    font-weight: 800;
}

.cart-items-count {
    font-size: 0.9rem;
    color: var(--gm-gray);
    font-weight: 600;
}

.select-all-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: linear-gradient(135deg, #f1f8e9 0%, #ffffff 100%);
    border: 2px solid rgba(46, 125, 50, 0.2);
    border-radius: 12px;
    margin-bottom: 14px;
}

.select-all-bar input[type="checkbox"],
.item-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--gm-primary);
}

.select-all-bar label {
    flex: 1;
    margin: 0;
    font-size: 0.92rem;
    color: var(--gm-dark);
    font-weight: 600;
    cursor: pointer;
}

.delete-selected {
    background: transparent;
    border: none;
    color: var(--gm-gray);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
}

.delete-selected:hover {
    color: var(--gm-accent);
}

.cart-item {
    display: grid;
    grid-template-areas: "check image details";
    grid-template-columns: 26px 110px minmax(0, 1fr);
    gap: 16px;
    padding: 14px;
    border: 1px solid rgba(46, 125, 50, 0.16);
    border-radius: 14px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbf8 100%);
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transition: opacity 0.3s, transform 0.3s;
}

.cart-item:last-child {
    margin-bottom: 0;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--gm-shadow-hover);
}

.item-checkbox {
    grid-area: check;
    display: flex;
    align-items: flex-start;
    padding-top: 8px;
}

.item-image {
    grid-area: image;
    width: 110px;
    height: 110px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(46, 125, 50, 0.15);
    background: #f9fafb;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    grid-area: details;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
}

.item-brand {
    font-size: 0.78rem;
    color: var(--gm-primary);
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.item-title {
    margin: 0;
    font-size: 1.05rem;
    color: var(--gm-dark);
    font-weight: 700;
    line-height: 1.35;
}

.item-title:hover {
    color: var(--gm-primary);
    cursor: pointer;
}

.item-rating {
    display: flex;
    align-items: center;
    gap: 6px;
}

.item-rating .stars {
    display: inline-flex;
    gap: 2px;
}

.item-rating .stars i {
    color: var(--gm-star);
    font-size: 0.82rem;
}

.item-rating .count {
    color: var(--gm-gray);
    font-size: 0.85rem;
    font-weight: 600;
}

.item-price-section {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.item-price {
    font-size: 1.35rem;
    color: var(--gm-accent);
    font-weight: 800;
    line-height: 1;
}

.item-original-price {
    font-size: 0.95rem;
    color: #9ca3af;
    text-decoration: line-through;
}

.item-discount {
    font-size: 0.82rem;
    color: var(--gm-primary-dark);
    font-weight: 700;
    background: rgba(46, 125, 50, 0.12);
    padding: 3px 9px;
    border-radius: 999px;
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.item-quantity {
    display: inline-flex;
    align-items: center;
    border: 1px solid rgba(46, 125, 50, 0.22);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}

.qty-btn {
    width: 34px;
    height: 34px;
    border: none;
    background: #fff;
    color: var(--gm-dark);
    cursor: pointer;
    font-size: 1.05rem;
    font-weight: 700;
}

.qty-btn:hover {
    background: var(--gm-light);
    color: var(--gm-primary-dark);
}

.qty-input {
    width: 52px;
    height: 34px;
    border: none;
    border-left: 1px solid rgba(46, 125, 50, 0.18);
    border-right: 1px solid rgba(46, 125, 50, 0.18);
    text-align: center;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--gm-dark);
}

.item-remove {
    background: transparent;
    border: none;
    color: var(--gm-gray);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 2px;
}

.item-remove:hover {
    color: var(--gm-accent);
}

/* Cart item inline stock badge */
.cart-stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 999px;
    border: 1.5px solid transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.cart-stock-ok     { background: rgba(46,125,50,0.08);  color: #1B5E20; border-color: rgba(46,125,50,0.22); }
.cart-stock-low    { background: rgba(255,107,53,0.10); color: #c44b10; border-color: rgba(255,107,53,0.28); }
.cart-stock-critical { background: rgba(220,38,38,0.08); color: #b91c1c; border-color: rgba(220,38,38,0.22); animation: cartStockPulse 1.6s ease-in-out infinite; }
.cart-stock-loading { background: #f3f4f6; color: #9ca3af; border-color: #e5e7eb; }
@keyframes cartStockPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.15); }
    50%     { box-shadow: 0 0 0 5px rgba(220,38,38,0); }
}
.cart-stock-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.cart-stock-ok   .cart-stock-dot { background:#2E7D32; }
.cart-stock-low  .cart-stock-dot { background:#FF6B35; }
.cart-stock-critical .cart-stock-dot { background:#dc2626; }

.order-summary {
    padding: 18px;
    height: fit-content;
    position: sticky;
    top: 22px;
    background: linear-gradient(160deg, #ffffff 0%, #f6fbf7 65%, #ecf8ef 100%);
}

.order-summary h3 {
    margin: 0 0 16px 0;
    color: var(--gm-dark);
    font-size: 1.1rem;
    font-weight: 800;
}

.summary-row,
.summary-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.summary-row {
    margin-bottom: 11px;
    font-size: 0.95rem;
}

.summary-row .label {
    color: var(--gm-gray);
}

.summary-row .value {
    color: var(--gm-dark);
    font-weight: 700;
}

.summary-divider {
    height: 1px;
    background: var(--gm-gray-light);
    margin: 14px 0;
}

.summary-total {
    margin-bottom: 16px;
}

.summary-total .label {
    color: var(--gm-dark);
    font-weight: 800;
    font-size: 1rem;
}

.summary-total .value {
    color: var(--gm-accent);
    font-size: 1.35rem;
    font-weight: 900;
}

.checkout-btn,
.continue-shopping,
.promo-input-group button {
    border: none;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
}

.checkout-btn {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, var(--gm-primary) 0%, #1e7e34 100%);
    color: #fff;
    font-size: 0.98rem;
}

.checkout-btn:hover {
    background: linear-gradient(135deg, var(--gm-primary-dark) 0%, #145a1b 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(27, 94, 32, 0.25);
}

.continue-shopping {
    width: 100%;
    padding: 11px;
    margin-top: 9px;
    background: #fff;
    color: var(--gm-accent);
    border: 1px solid rgba(255, 107, 53, 0.45);
    font-size: 0.92rem;
}

.continue-shopping:hover {
    background: rgba(255, 107, 53, 0.08);
    border-color: var(--gm-accent);
}

.promo-code {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--gm-gray-light);
}

.promo-code h4 {
    margin: 0 0 10px 0;
    color: var(--gm-dark);
    font-size: 0.93rem;
    font-weight: 700;
}

.promo-input-group {
    display: flex;
    gap: 8px;
}

.promo-input-group input {
    flex: 1;
    border: 1px solid rgba(46, 125, 50, 0.24);
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
}

.promo-input-group input:focus {
    border-color: var(--gm-primary);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.14);
}

.promo-input-group button {
    padding: 10px 16px;
    background: linear-gradient(135deg, var(--gm-accent) 0%, var(--gm-accent-dark) 100%);
    color: #fff;
    font-size: 0.88rem;
}

.promo-input-group button:hover {
    filter: brightness(0.95);
}

.empty-cart {
    padding: 54px 20px;
    text-align: center;
}

.empty-cart-icon {
    font-size: 4.4rem;
    color: var(--gm-primary-light);
    margin-bottom: 16px;
}

.empty-cart h2 {
    margin-bottom: 8px;
    color: var(--gm-dark);
    font-size: 1.45rem;
    font-weight: 800;
}

.empty-cart p {
    color: var(--gm-gray);
    margin-bottom: 22px;
}

.empty-cart-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 30px;
    text-decoration: none;
    border-radius: 10px;
    background: var(--gm-primary);
    color: #fff;
    font-weight: 700;
    transition: all 0.25s ease;
}

.empty-cart-btn:hover {
    background: var(--gm-primary-dark);
    color: #fff;
}

/* Responsive */
@media (max-width: 1180px) {
    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 300px;
    }

    .order-summary {
        top: 16px;
    }
}

@media (max-width: 980px) {
    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 240px;
        gap: 12px;
    }
}

@media (max-width: 768px) {
    .cart-container {
        padding: 10px 8px 14px;
    }

    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 190px;
        gap: 9px;
    }

    .cart-header,
    .cart-items-section,
    .order-summary {
        border-radius: 10px;
        padding: 10px;
    }

    .cart-items-header {
        align-items: center;
        flex-direction: row;
        gap: 8px;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }

    .cart-items-header h2 {
        font-size: 0.95rem;
    }

    .cart-items-count {
        font-size: 0.78rem;
    }

    .select-all-bar {
        padding: 8px 10px;
        margin-bottom: 10px;
        gap: 8px;
    }

    .select-all-bar label {
        font-size: 0.78rem;
    }

    .delete-selected {
        font-size: 0.72rem;
    }

    .cart-item {
        grid-template-areas: "check image details";
        grid-template-columns: 20px 70px minmax(0, 1fr);
        gap: 8px;
        padding: 8px;
        margin-bottom: 8px;
    }

    .item-image {
        width: 70px;
        height: 70px;
        border-radius: 8px;
    }

    .item-brand {
        font-size: 0.64rem;
    }

    .item-title {
        font-size: 0.78rem;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .item-rating {
        display: none;
    }

    .item-price {
        font-size: 0.98rem;
    }

    .item-original-price {
        font-size: 0.76rem;
    }

    .item-discount {
        font-size: 0.68rem;
        padding: 2px 6px;
    }

    .item-actions {
        justify-content: space-between;
        gap: 6px;
    }

    .item-quantity {
        border-radius: 8px;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        font-size: 0.86rem;
    }

    .qty-input {
        width: 34px;
        height: 24px;
        font-size: 0.76rem;
    }

    .item-remove {
        font-size: 0.72rem;
        gap: 4px;
        white-space: nowrap;
    }

    .order-summary h3 {
        font-size: 0.88rem;
        margin-bottom: 10px;
    }

    .summary-row {
        font-size: 0.76rem;
        margin-bottom: 7px;
    }

    .summary-total .label {
        font-size: 0.86rem;
    }

    .summary-total .value {
        font-size: 1.02rem;
    }

    .checkout-btn,
    .continue-shopping {
        padding: 9px;
        font-size: 0.74rem;
    }

    .promo-code h4 {
        font-size: 0.78rem;
    }

    .promo-input-group {
        flex-direction: column;
        gap: 6px;
    }

    .promo-input-group input,
    .promo-input-group button {
        width: 100%;
        padding: 8px 9px;
        font-size: 0.72rem;
    }
}

@media (max-width: 560px) {
    .cart-header h1 {
        font-size: 1rem;
    }

    .cart-breadcrumb {
        font-size: 0.74rem;
    }

    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 150px;
        gap: 7px;
    }

    .select-all-bar {
        flex-wrap: nowrap;
        padding: 7px 8px;
    }

    .select-all-bar label {
        font-size: 0.7rem;
    }

    .cart-item {
        grid-template-areas: "check image details";
        grid-template-columns: 16px 56px minmax(0, 1fr);
        gap: 7px;
        padding: 7px;
    }

    .item-checkbox {
        padding-top: 2px;
    }

    .item-image {
        width: 56px;
        height: 56px;
    }

    .item-title {
        font-size: 0.7rem;
    }

    .item-price {
        font-size: 0.84rem;
    }

    .item-original-price,
    .item-discount {
        font-size: 0.64rem;
    }

    .item-actions {
        flex-direction: row;
        align-items: center;
        gap: 6px;
    }

    .qty-btn {
        width: 20px;
        height: 20px;
        font-size: 0.74rem;
    }

    .qty-input {
        width: 28px;
        height: 20px;
        font-size: 0.66rem;
    }

    .item-remove {
        font-size: 0.64rem;
    }

    .order-summary {
        padding: 8px;
    }

    .promo-code {
        display: none;
    }
}

@media (max-width: 420px) {
    .cart-layout {
        grid-template-columns: 1fr;
    }

    .order-summary {
        position: static;
    }
}

/* ==========================================
   CUSTOM MODAL SYSTEM
   ========================================== */
.gm-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.22s ease;
    pointer-events: none;
}
.gm-modal-overlay.gm-modal-visible {
    opacity: 1;
    pointer-events: all;
}
.gm-modal-box {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 24px 60px rgba(27, 94, 32, 0.18), 0 4px 16px rgba(0,0,0,0.10);
    max-width: 420px;
    width: 100%;
    overflow: hidden;
    transform: scale(0.88) translateY(24px);
    transition: transform 0.26s cubic-bezier(.34,1.56,.64,1), opacity 0.22s ease;
    opacity: 0;
    position: relative;
}
.gm-modal-overlay.gm-modal-visible .gm-modal-box {
    transform: scale(1) translateY(0);
    opacity: 1;
}
.gm-modal-stripe {
    height: 5px;
    width: 100%;
    background: linear-gradient(90deg, var(--gm-primary), #1e7e34 50%, var(--gm-primary-light));
    background-size: 200% 100%;
    animation: cartGradientShift 4s ease infinite;
}
.gm-modal-stripe.gm-stripe-warn {
    background: linear-gradient(90deg, var(--gm-accent), #e55a2b 50%, #ff8c5a);
    background-size: 200% 100%;
}
.gm-modal-stripe.gm-stripe-danger {
    background: linear-gradient(90deg, #dc2626, #ef4444 50%, #f87171);
    background-size: 200% 100%;
}
.gm-modal-body {
    padding: 26px 28px 10px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.gm-modal-icon {
    flex-shrink: 0;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-top: 2px;
}
.gm-modal-icon.icon-info  { background: rgba(46,125,50,0.12); color: var(--gm-primary); }
.gm-modal-icon.icon-warn  { background: rgba(255,107,53,0.12); color: var(--gm-accent); }
.gm-modal-icon.icon-danger{ background: rgba(220,38,38,0.1);  color: #dc2626; }
.gm-modal-icon.icon-success{ background: rgba(46,125,50,0.12); color: var(--gm-primary); }
.gm-modal-text {
    flex: 1;
    min-width: 0;
}
.gm-modal-title {
    margin: 0 0 6px;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--gm-dark);
    line-height: 1.3;
}
.gm-modal-message {
    margin: 0;
    font-size: 0.93rem;
    color: #4b5563;
    line-height: 1.55;
}
.gm-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 18px 28px 22px;
}
.gm-btn {
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.gm-btn-cancel {
    background: #f3f4f6;
    color: #374151;
}
.gm-btn-cancel:hover {
    background: #e5e7eb;
}
.gm-btn-primary {
    background: linear-gradient(135deg, var(--gm-primary), #1e7e34);
    color: #fff;
    box-shadow: 0 4px 14px rgba(27,94,32,0.25);
}
.gm-btn-primary:hover {
    background: linear-gradient(135deg, var(--gm-primary-dark), #145a1b);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(27,94,32,0.30);
}
.gm-btn-danger {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    box-shadow: 0 4px 14px rgba(220,38,38,0.22);
}
.gm-btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    transform: translateY(-1px);
}
.gm-btn-warn {
    background: linear-gradient(135deg, var(--gm-accent), var(--gm-accent-dark));
    color: #fff;
    box-shadow: 0 4px 14px rgba(255,107,53,0.22);
}
.gm-btn-warn:hover {
    filter: brightness(0.93);
    transform: translateY(-1px);
}
</style>

<div class="cart-container">
    <!-- Cart Header -->
    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
        <div class="cart-breadcrumb">
            <a href="{{ route('home') }}">Home</a> > Cart
        </div>
    </div>

    <!-- Dynamic Cart Content - populated by JavaScript from localStorage -->
    <div class="cart-layout" id="cart-layout-section" style="display:none;">
        <!-- Cart Items Section -->
        <div class="cart-items-section">
            <div class="cart-items-header">
                <h2>Cart Items</h2>
                <span class="cart-items-count" id="cart-items-count">(0 items)</span>
            </div>

            <!-- Select All Bar -->
            <div class="select-all-bar">
                <input type="checkbox" id="select-all">
                <label for="select-all">Select All</label>
                <button class="delete-selected" id="delete-selected-btn"><i class="fas fa-trash-alt"></i> Delete Selected</button>
            </div>

            <!-- Cart Items List -->
            <div class="cart-items-list" id="cart-items-list"></div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="order-summary">
            <h3>Order Summary</h3>

            <div class="summary-total">
                <span class="label">Total</span>
                <span class="value" id="total">Rs.0</span>
            </div>
            
            <button class="checkout-btn" id="checkout-btn">
                <i class="fas fa-lock"></i> Proceed to Checkout
            </button>
            
            <button class="continue-shopping" onclick="window.location.href='{{ route('home') }}'">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </button>
            
            <!-- Promo Code Section -->
            <div class="promo-code">
                <h4>Have a promo code?</h4>
                <div class="promo-input-group">
                    <input type="text" placeholder="Enter code" id="promo-code-input">
                    <button id="apply-promo">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty Cart -->
    <div class="empty-cart" id="empty-cart-section" style="display:none;">
        <div class="empty-cart-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h2>Your Cart is Empty</h2>
        <p>Looks like you haven't added anything to your cart yet</p>
        <a href="{{ route('home') }}" class="empty-cart-btn">
            <i class="fas fa-shopping-bag"></i> Start Shopping
        </a>
    </div>
</div>

{{-- ==========================================
    CUSTOM MODAL HTML
    ========================================== --}}
<div class="gm-modal-overlay" id="gm-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="gm-modal-title">
    <div class="gm-modal-box" id="gm-modal-box">
        <div class="gm-modal-stripe" id="gm-modal-stripe"></div>
        <div class="gm-modal-body">
            <div class="gm-modal-icon icon-info" id="gm-modal-icon">
                <i class="fas fa-info-circle" id="gm-modal-icon-i"></i>
            </div>
            <div class="gm-modal-text">
                <p class="gm-modal-title" id="gm-modal-title">Notice</p>
                <p class="gm-modal-message" id="gm-modal-message"></p>
            </div>
        </div>
        <div class="gm-modal-footer" id="gm-modal-footer">
            <button class="gm-btn gm-btn-primary" id="gm-modal-ok"><i class="fas fa-check"></i> OK</button>
        </div>
    </div>
</div>

{{-- ==========================================
    CART FUNCTIONALITY JAVASCRIPT
    ========================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CART_KEY = 'gm_cart_items';
    const SELECTED_ITEMS_KEY = 'gm_checkout_selected_items';
    const cartApi = window.GroceMateCart || null;
    const fallbackImage = @json(asset('assets/img/product/product1.jpg'));

    const cartLayoutSection = document.getElementById('cart-layout-section');
    const emptyCartSection = document.getElementById('empty-cart-section');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartItemsCount = document.getElementById('cart-items-count');
    const totalEl = document.getElementById('total');
    const checkoutBtn = document.getElementById('checkout-btn');
    const selectAllCheckbox = document.getElementById('select-all');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    const applyPromoBtn = document.getElementById('apply-promo');
    var selectedItemIds = new Set();

    /* =============================================
       CUSTOM MODAL SYSTEM
       ============================================= */
    var modalOverlay   = document.getElementById('gm-modal-overlay');
    var modalStripe    = document.getElementById('gm-modal-stripe');
    var modalIcon      = document.getElementById('gm-modal-icon');
    var modalIconI     = document.getElementById('gm-modal-icon-i');
    var modalTitle     = document.getElementById('gm-modal-title');
    var modalMessage   = document.getElementById('gm-modal-message');
    var modalFooter    = document.getElementById('gm-modal-footer');
    var modalOkBtn     = document.getElementById('gm-modal-ok');

    /**
     * Show an alert-style modal (single OK button).
     * type: 'info' | 'warn' | 'danger' | 'success'
     */
    function gmAlert(message, options) {
        options = options || {};
        var type    = options.type    || 'info';
        var title   = options.title   || (type === 'danger' ? 'Error' : type === 'warn' ? 'Warning' : 'Notice');
        var btnText = options.btnText || 'OK';
        var btnClass = type === 'danger' ? 'gm-btn-danger' : type === 'warn' ? 'gm-btn-warn' : 'gm-btn-primary';
        var icons = { info: 'fa-info-circle', warn: 'fa-exclamation-triangle', danger: 'fa-times-circle', success: 'fa-check-circle' };
        var stripes = { info: '', warn: 'gm-stripe-warn', danger: 'gm-stripe-danger', success: '' };

        modalStripe.className  = 'gm-modal-stripe ' + (stripes[type] || '');
        modalIcon.className    = 'gm-modal-icon icon-' + type;
        modalIconI.className   = 'fas ' + (icons[type] || 'fa-info-circle');
        modalTitle.textContent = title;
        modalMessage.textContent = message;

        modalFooter.innerHTML =
            '<button class="gm-btn ' + btnClass + '" id="gm-modal-ok"><i class="fas fa-check"></i> ' + btnText + '</button>';

        modalOverlay.classList.add('gm-modal-visible');

        return new Promise(function(resolve) {
            document.getElementById('gm-modal-ok').onclick = function() {
                gmModalClose();
                resolve();
            };
        });
    }

    /**
     * Show a confirm-style modal (Confirm + Cancel buttons).
     * type: 'warn' | 'danger'
     * Returns a Promise<boolean>.
     */
    function gmConfirm(message, options) {
        options = options || {};
        var type        = options.type        || 'warn';
        var title       = options.title       || 'Are you sure?';
        var confirmText = options.confirmText || 'Yes, Confirm';
        var cancelText  = options.cancelText  || 'Cancel';
        var confirmClass = type === 'danger' ? 'gm-btn-danger' : 'gm-btn-warn';
        var icons = { warn: 'fa-exclamation-triangle', danger: 'fa-trash-alt' };
        var stripes = { warn: 'gm-stripe-warn', danger: 'gm-stripe-danger' };

        modalStripe.className  = 'gm-modal-stripe ' + (stripes[type] || 'gm-stripe-warn');
        modalIcon.className    = 'gm-modal-icon icon-' + type;
        modalIconI.className   = 'fas ' + (icons[type] || 'fa-exclamation-triangle');
        modalTitle.textContent = title;
        modalMessage.textContent = message;

        modalFooter.innerHTML =
            '<button class="gm-btn gm-btn-cancel" id="gm-modal-cancel"><i class="fas fa-times"></i> ' + cancelText + '</button>' +
            '<button class="gm-btn ' + confirmClass + '" id="gm-modal-confirm"><i class="fas fa-check"></i> ' + confirmText + '</button>';

        modalOverlay.classList.add('gm-modal-visible');

        return new Promise(function(resolve) {
            document.getElementById('gm-modal-confirm').onclick = function() {
                gmModalClose();
                resolve(true);
            };
            document.getElementById('gm-modal-cancel').onclick = function() {
                gmModalClose();
                resolve(false);
            };
        });
    }

    function gmModalClose() {
        modalOverlay.classList.remove('gm-modal-visible');
    }

    // Close modal when clicking the backdrop
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) gmModalClose();
    });
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('gm-modal-visible')) gmModalClose();
    });

    function getCartItems() {
        if (cartApi && typeof cartApi.getItems === 'function') {
            return cartApi.getItems();
        }
        try {
            return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
        } catch (_) {
            return [];
        }
    }

    function saveCartItems(items) {
        if (cartApi && typeof cartApi.setItems === 'function') {
            cartApi.setItems(items);
        } else {
            localStorage.setItem(CART_KEY, JSON.stringify(items));
        }
        if (cartApi && typeof cartApi.updateBadges === 'function') {
            cartApi.updateBadges();
        }
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function resolveApiErrorMessage(data, fallbackMessage) {
        if (data && data.errors && typeof data.errors === 'object') {
            for (var key in data.errors) {
                if (!Object.prototype.hasOwnProperty.call(data.errors, key)) {
                    continue;
                }

                var fieldError = data.errors[key];
                if (Array.isArray(fieldError) && fieldError.length > 0 && fieldError[0]) {
                    return String(fieldError[0]);
                }

                if (typeof fieldError === 'string' && fieldError.trim() !== '') {
                    return fieldError;
                }
            }
        }

        if (data && typeof data.message === 'string' && data.message.trim() !== '') {
            return data.message;
        }

        return fallbackMessage;
    }

    function getCheckedItemIdsFromDom() {
        return Array.from(cartItemsList.querySelectorAll('.item-check:checked'))
            .map(function(checkbox) {
                var cartItem = checkbox.closest('.cart-item');
                return cartItem ? String(cartItem.dataset.itemId) : '';
            })
            .filter(Boolean);
    }

    function syncSelectedItemIdsFromDom() {
        selectedItemIds = new Set(getCheckedItemIdsFromDom());
    }

    function updateSelectAllState() {
        if (!selectAllCheckbox) return;

        var checks = Array.from(cartItemsList.querySelectorAll('.item-check'));
        var checkedCount = checks.filter(function(cb) { return cb.checked; }).length;

        selectAllCheckbox.checked = checks.length > 0 && checkedCount === checks.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checks.length;
    }

    function renderCart() {
        syncSelectedItemIdsFromDom();

        const items = getCartItems();
        const availableIds = new Set(items.map(function(item) {
            return String(item && item.id != null ? item.id : '').trim();
        }).filter(Boolean));

        selectedItemIds.forEach(function(id) {
            if (!availableIds.has(id)) {
                selectedItemIds.delete(id);
            }
        });

        if (items.length === 0) {
            cartLayoutSection.style.display = 'none';
            emptyCartSection.style.display = '';
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            totalEl.textContent = 'Rs.0';
            return;
        }

        cartLayoutSection.style.display = '';
        emptyCartSection.style.display = 'none';
        cartItemsCount.textContent = '(' + items.length + ' item' + (items.length !== 1 ? 's' : '') + ')';

        cartItemsList.innerHTML = items.map(function(item) {
            var safeName = escapeHtml(item.name);
            var safeImage = escapeHtml(item.image || fallbackImage);
            var price = Number(item.price) || 0;
            var qty = Math.max(1, Number(item.qty) || 1);
            var rawId = String(item && item.id != null ? item.id : '').trim();
            var id = escapeHtml(rawId);
            var checkedAttr = selectedItemIds.has(rawId) ? ' checked' : '';
            var maxStock = item.stock != null ? Number(item.stock) : 99;

            return '<div class="cart-item" data-item-id="' + id + '" data-price="' + price + '" data-max-stock="' + maxStock + '">' +
                '<div class="item-checkbox">' +
                    '<input type="checkbox" class="item-check" data-price="' + price + '" data-qty="' + qty + '"' + checkedAttr + '>' +
                '</div>' +
                '<div class="item-image">' +
                    '<img src="' + safeImage + '" alt="' + safeName + '" onerror="this.src=\'' + fallbackImage + '\'\'>' +
                '</div>' +
                '<div class="item-details">' +
                    '<div>' +
                        '<h3 class="item-title">' + safeName + '</h3>' +
                    '</div>' +
                    '<div class="item-price-section">' +
                        '<span class="item-price">Rs.' + price + '</span>' +
                        '<span class="cart-stock-badge cart-stock-loading" id="stock-badge-' + id + '">' +
                            '<span class="cart-stock-dot"></span>' +
                            '<span class="cart-stock-text">Checking stock...</span>' +
                        '</span>' +
                    '</div>' +
                    '<div class="item-actions">' +
                        '<div class="item-quantity">' +
                            '<button class="qty-btn qty-minus" data-item-id="' + id + '">-</button>' +
                            '<input type="text" class="qty-input" value="' + qty + '" readonly>' +
                            '<button class="qty-btn qty-plus" data-item-id="' + id + '">+</button>' +
                        '</div>' +
                        '<button class="item-remove" data-item-id="' + id + '">' +
                            '<i class="fas fa-trash-alt"></i> Remove' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');

        updateSelectAllState();
        calculateTotals();
        fetchAndApplyLiveStock();
    }

    /* -----------------------------------------------
       LIVE STOCK FETCH — calls /cart/stock API
       and updates data-max-stock + badge on each row
       ----------------------------------------------- */
    var stockFetchUrl = '{{ route("frontend.cart.stock") }}';

    function fetchAndApplyLiveStock() {
        var rows = Array.from(cartItemsList.querySelectorAll('.cart-item[data-item-id]'));
        if (!rows.length) return;

        var ids = rows.map(function(r) { return r.dataset.itemId; }).filter(Boolean);
        var query = ids.map(function(id) { return 'ids[]=' + encodeURIComponent(id); }).join('&');

        fetch(stockFetchUrl + '?' + query)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var stockMap = data.stock || {};
                rows.forEach(function(row) {
                    var productId = String(row.dataset.itemId);
                    var info = stockMap[productId];
                    var badge = document.getElementById('stock-badge-' + productId);
                    if (!badge) return;

                    if (!info) {
                        badge.className = 'cart-stock-badge cart-stock-loading';
                        badge.querySelector('.cart-stock-text').textContent = 'N/A';
                        return;
                    }

                    var stock = info.stock;
                    row.dataset.maxStock = String(stock);

                    var cls, label;
                    if (stock <= 0) {
                        cls = 'cart-stock-critical';
                        label = 'Out of stock';
                    } else if (stock <= 3) {
                        cls = 'cart-stock-critical';
                        label = 'Only ' + Math.floor(stock) + ' left!';
                    } else if (stock <= 10) {
                        cls = 'cart-stock-low';
                        label = Math.floor(stock) + ' items left';
                    } else {
                        cls = 'cart-stock-ok';
                        label = Math.floor(stock) + ' in stock';
                    }

                    badge.className = 'cart-stock-badge ' + cls;
                    badge.querySelector('.cart-stock-text').textContent = label;

                    // Auto-clamp qty if it now exceeds live stock
                    var qtyInput = row.querySelector('.qty-input');
                    var checkbox = row.querySelector('.item-check');
                    if (qtyInput && stock > 0) {
                        var currentQty = parseInt(qtyInput.value) || 1;
                        if (currentQty > stock) {
                            var clamped = Math.max(1, Math.floor(stock));
                            qtyInput.value = clamped;
                            if (checkbox) checkbox.dataset.qty = clamped;
                            var cartItems = getCartItems();
                            var target = cartItems.find(function(i) { return String(i.id) === productId; });
                            if (target) { target.qty = clamped; saveCartItems(cartItems); }
                            calculateTotals();
                        }
                    }
                });
            })
            .catch(function() {
                // Silently fail — stock badges stay in loading state
            });
    }

    function calculateTotals() {
        var total = 0;
        document.querySelectorAll('.item-check:checked').forEach(function(checkbox) {
            var price = parseFloat(checkbox.dataset.price) || 0;
            var qty = parseInt(checkbox.dataset.qty) || 1;
            total += price * qty;
        });
        totalEl.textContent = 'Rs.' + total;
    }

    // Event delegation for dynamically created cart items
    cartItemsList.addEventListener('click', function(e) {
        // Quantity minus
        var minusBtn = e.target.closest('.qty-minus');
        if (minusBtn) {
            var row = minusBtn.closest('.cart-item');
            var input = row.querySelector('.qty-input');
            var checkbox = row.querySelector('.item-check');
            var value = parseInt(input.value);
            if (value > 1) {
                value--;
                input.value = value;
                if (checkbox) checkbox.dataset.qty = value;
                // Update in storage
                var items = getCartItems();
                var target = items.find(function(i) { return String(i.id) === String(row.dataset.itemId); });
                if (target) { target.qty = value; saveCartItems(items); }
                calculateTotals();
            }
            return;
        }

        // Quantity plus
        var plusBtn = e.target.closest('.qty-plus');
        if (plusBtn) {
            var row = plusBtn.closest('.cart-item');
            var input = row.querySelector('.qty-input');
            var checkbox = row.querySelector('.item-check');
            var value = parseInt(input.value);
            var maxStock = row.dataset.maxStock != null && row.dataset.maxStock !== '' && row.dataset.maxStock !== '99'
                ? parseInt(row.dataset.maxStock)
                : 99;

            if (maxStock <= 0) {
                gmAlert('This product is currently out of stock.', {
                    type: 'danger',
                    title: 'Out of Stock',
                    btnText: 'OK'
                });
                return;
            }

            if (value >= maxStock) {
                gmAlert(
                    'Only ' + maxStock + ' unit' + (maxStock !== 1 ? 's' : '') +
                    ' available in stock. You cannot add more than what is available.',
                    {
                        type: 'warn',
                        title: '⚠️ Stock Limit Reached',
                        btnText: 'Got it'
                    }
                );
                return;
            }

            value++;
            input.value = value;
            if (checkbox) checkbox.dataset.qty = value;
            var items = getCartItems();
            var target = items.find(function(i) { return String(i.id) === String(row.dataset.itemId); });
            if (target) { target.qty = value; saveCartItems(items); }
            calculateTotals();
            return;
        }

        // Remove item
        var removeBtn = e.target.closest('.item-remove');
        if (removeBtn) {
            var cartItem = removeBtn.closest('.cart-item');
            var itemId = removeBtn.dataset.itemId;
            gmConfirm('Are you sure you want to remove this item from your cart?', {
                type: 'danger',
                title: 'Remove Item',
                confirmText: 'Yes, Remove',
                cancelText: 'Keep It'
            }).then(function(confirmed) {
                if (confirmed) {
                    cartItem.style.opacity = '0';
                    cartItem.style.transform = 'translateX(20px)';
                    setTimeout(function() {
                        var items = getCartItems().filter(function(i) { return String(i.id) !== String(itemId); });
                        saveCartItems(items);
                        renderCart();
                    }, 300);
                }
            });
            return;
        }
    });

    // Checkbox change events via delegation
    cartItemsList.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-check')) {
            syncSelectedItemIdsFromDom();
            calculateTotals();
            updateSelectAllState();
        }
    });

    // Select all
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            var checked = this.checked;
            cartItemsList.querySelectorAll('.item-check').forEach(function(cb) { cb.checked = checked; });
            syncSelectedItemIdsFromDom();
            updateSelectAllState();
            calculateTotals();
        });
    }

    // Delete selected
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            var selectedChecks = document.querySelectorAll('.item-check:checked');
            if (selectedChecks.length === 0) {
                gmAlert('Please select at least one item to delete.', {
                    type: 'warn',
                    title: 'No Items Selected',
                    btnText: 'Got it'
                });
                return;
            }
            gmConfirm('Are you sure you want to remove ' + selectedChecks.length + ' item(s) from your cart?', {
                type: 'danger',
                title: 'Delete Selected Items',
                confirmText: 'Yes, Delete',
                cancelText: 'Cancel'
            }).then(function(confirmed) {
                if (confirmed) {
                    var idsToRemove = [];
                    document.querySelectorAll('.item-check:checked').forEach(function(cb) {
                        var cartItem = cb.closest('.cart-item');
                        if (cartItem) idsToRemove.push(String(cartItem.dataset.itemId));
                    });
                    idsToRemove.forEach(function(id) {
                        selectedItemIds.delete(String(id));
                    });
                    var items = getCartItems().filter(function(i) { return idsToRemove.indexOf(String(i.id)) === -1; });
                    saveCartItems(items);
                    renderCart();
                }
            });
        });
    }

    // Promo code
    if (applyPromoBtn) {
        applyPromoBtn.addEventListener('click', function() {
            var code = document.getElementById('promo-code-input').value.trim();
            if (!code) {
                gmAlert('Please enter a promo code to apply.', {
                    type: 'warn',
                    title: 'Promo Code Required'
                });
                return;
            }
            gmAlert('Promo code feature is coming soon! Stay tuned for exciting discounts.', {
                type: 'info',
                title: '🎉 Coming Soon!',
                btnText: 'Got it'
            });
        });
    }

    // Proceed to checkout
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            var selectedChecks = document.querySelectorAll('.item-check:checked');

            if (selectedChecks.length === 0) {
                gmAlert('Please select at least one item to proceed to checkout.', {
                    type: 'warn',
                    title: 'No Items Selected',
                    btnText: 'Got it'
                });
                return;
            }

            var selectedCartItems = [];
            selectedChecks.forEach(function(checkbox) {
                var cartItem = checkbox.closest('.cart-item');

                if (!cartItem) return;
                var itemId = cartItem.dataset.itemId;
                var rawPrice = cartItem.dataset.price;

                if (itemId && rawPrice) {
                    var qtyInput = cartItem.querySelector('.qty-input');
                    var qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
                    var itemName = cartItem.querySelector('.item-title') ? cartItem.querySelector('.item-title').textContent.trim() : 'Product';
                    var itemImage = cartItem.querySelector('.item-image img') ? cartItem.querySelector('.item-image img').getAttribute('src') : '';
                    
                    selectedCartItems.push({
                        id: String(itemId).trim(),
                        name: itemName,
                        price: parseFloat(String(rawPrice).replace(/[^\d.]/g, '')) || 0,
                        image: itemImage,
                        qty: qty,
                    });
                }
            });

            checkoutBtn.disabled = true;

            fetch('{{ route("frontend.checkout.validate-stock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    items: selectedCartItems
                })
            })
            .then(async function(response) {
                var data = await response.json().catch(function() { return {}; });
                if (!response.ok) {
                    throw new Error(resolveApiErrorMessage(data, 'Unable to validate cart stock.'));
                }
                return data;
            })
            .then(function() {
                try {
                    localStorage.setItem(SELECTED_ITEMS_KEY, JSON.stringify(selectedCartItems));

                    // Small delay to ensure localStorage is persisted before redirect
                    setTimeout(function() {
                        window.location.href = '{{ route("checkout") }}';
                    }, 100);
                } catch (e) {
                    // Fallback: redirect anyway
                    window.location.href = '{{ route("checkout") }}';
                }
            })
            .catch(function(error) {
                gmAlert(error.message || 'Unable to proceed to checkout. Please try again.', {
                    type: 'danger',
                    title: 'Stock Validation Failed',
                    btnText: 'OK'
                });
                checkoutBtn.disabled = false;
            });
        });
    }

    // Listen for cart updates from other tabs/pages
    window.addEventListener('storage', function(event) {
        if (event.key === CART_KEY) {
            renderCart();
        }
    });
    window.addEventListener('gm-cart-updated', renderCart);

    // Initial render
    renderCart();
});
</script>

@endsection
