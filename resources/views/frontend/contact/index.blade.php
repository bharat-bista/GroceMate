@extends('frontend.layouts.main')

@section('main-content')
<style>
    .contact-page {
        --gm-primary: #2e7d32;
        --gm-primary-dark: #1b5e20;
        --gm-primary-soft: #e9f5ea;
        --gm-accent: #ff6b35;
        --gm-text: #1f2937;
        --gm-muted: #6b7280;
        --gm-border: #dce8dd;
        --gm-white: #ffffff;
        --gm-surface: #f5faf6;
        --gm-shadow: 0 14px 35px rgba(46, 125, 50, 0.12);
        padding: 28px 0 60px;
        background: radial-gradient(circle at top right, #e4f3e6 0%, #f9fcf9 48%, #f3f9f4 100%);
        min-height: 80vh;
    }

    .contact-container {
        max-width: 1200px;
        padding: 0 18px;
    }

    .contact-hero {
        background: linear-gradient(130deg, rgba(46, 125, 50, 0.96), rgba(27, 94, 32, 0.95));
        border-radius: 22px;
        color: #fff;
        padding: 22px 24px;
        margin-bottom: 24px;
        box-shadow: var(--gm-shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .contact-hero h1 {
        margin: 0;
        font-size: clamp(1.45rem, 2.4vw, 2rem);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .contact-hero p {
        margin: 6px 0 0;
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.88);
    }

    .contact-hero-badge {
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.45);
        color: #fff;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
        gap: 22px;
        align-items: start;
    }

    .contact-card {
        background: var(--gm-white);
        border: 1px solid var(--gm-border);
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 8px 28px rgba(28, 76, 45, 0.08);
    }

    .contact-card h4 {
        margin-bottom: 18px;
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--gm-text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .contact-card h4 i {
        color: var(--gm-primary);
    }

    .contact-card label {
        color: var(--gm-text);
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .contact-card .form-control {
        border: 1px solid var(--gm-border);
        border-radius: 12px;
        min-height: 46px;
        font-size: 1rem;
        padding: 10px 13px;
    }

    .contact-card .form-control:focus {
        border-color: var(--gm-primary);
        box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.15);
    }

    .contact-submit-btn {
        background: var(--gm-primary);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 12px 18px;
        font-weight: 700;
        font-size: 1rem;
        transition: 0.2s ease;
        width: 100%;
    }

    .contact-submit-btn:hover {
        background: var(--gm-primary-dark);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(46, 125, 50, 0.2);
    }

    .contact-submit-btn:disabled {
        opacity: 0.72;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .contact-feedback:empty,
    .contact-field-error:empty {
        display: none;
    }

    .contact-field-error {
        margin-top: 6px;
        font-size: 0.9rem;
        color: #b42318;
    }

    .contact-info-list {
        display: grid;
        gap: 16px;
    }

    .contact-info-item {
        display: flex;
        gap: 14px;
        padding: 14px 16px;
        border-radius: 14px;
        background: var(--gm-surface);
        border: 1px solid var(--gm-border);
        align-items: center;
    }

    .contact-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(46, 125, 50, 0.12);
        color: var(--gm-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .contact-info-title {
        font-weight: 700;
        color: var(--gm-text);
        margin-bottom: 4px;
    }

    .contact-info-subtext {
        color: var(--gm-muted);
        font-size: 0.95rem;
    }

    .contact-highlight {
        margin-top: 18px;
        border-radius: 16px;
        padding: 16px;
        background: rgba(255, 107, 53, 0.1);
        border: 1px solid rgba(255, 107, 53, 0.22);
        color: var(--gm-text);
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .contact-highlight i {
        color: var(--gm-accent);
        font-size: 1.3rem;
        margin-top: 2px;
    }

    @media (max-width: 991px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="contact-page">
    <div class="container contact-container">
        <div class="contact-hero">
            <div>
                <h1><i class="fas fa-headset"></i> Contact Us</h1>
                <p>We are here to help with orders, product details, and support.</p>
            </div>
            <div class="contact-hero-badge">
                <i class="fas fa-clock"></i> Everyday 5am to 7pm
            </div>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <h4><i class="fas fa-paper-plane"></i> Send us a message</h4>
                <div id="contact-feedback"
                     class="contact-feedback alert mb-3 {{ session('success') ? 'alert-success' : ($errors->any() ? 'alert-danger' : 'd-none') }}">
                    @if(session('success'))
                        {{ session('success') }}
                    @elseif($errors->any())
                        <ul class="mb-0 pl-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <form method="POST" action="{{ route('contact.store') }}" id="contact-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" placeholder="Your full name" required>
                            <div class="contact-field-error" data-error-for="name"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                            <div class="contact-field-error" data-error-for="email"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}" placeholder="Phone number">
                            <div class="contact-field-error" data-error-for="phone"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" class="form-control" id="subject" value="{{ old('subject') }}" placeholder="How can we help?" required>
                            <div class="contact-field-error" data-error-for="subject"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="message">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="4" placeholder="Write your message here" required>{{ old('message') }}</textarea>
                        <div class="contact-field-error" data-error-for="message"></div>
                    </div>

                    <button type="submit" class="contact-submit-btn" id="contact-submit-btn">Send Message</button>
                </form>
            </div>

            <div class="contact-card">
                <h4><i class="fas fa-address-card"></i> Contact info</h4>
                <div class="contact-info-list">
                    <div class="contact-info-item">
                        <div class="contact-icon"><i class="ti-home"></i></div>
                        <div>
                            <div class="contact-info-title">New Road, Sankata Mandir</div>
                            <div class="contact-info-subtext">Kathmandu, Nepal</div>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon"><i class="ti-tablet"></i></div>
                        <div>
                            <div class="contact-info-title">9808520775, 9840707993</div>
                            <div class="contact-info-subtext">Call us for product availability</div>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon"><i class="ti-email"></i></div>
                        <div>
                            <div class="contact-info-title">goldenvisiontraders@gmail.com</div>
                            <div class="contact-info-subtext">Send us your query anytime</div>
                        </div>
                    </div>
                </div>

                <div class="contact-highlight">
                    <i class="fas fa-bolt"></i>
                    <div>
                        <strong>Quick response</strong>
                        <div class="contact-info-subtext">We reply you in gmail within a few hours during business time.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contact-form');
    const submitBtn = document.getElementById('contact-submit-btn');
    const feedback = document.getElementById('contact-feedback');

    if (!form || !submitBtn || !feedback) {
        return;
    }

    const defaultButtonText = submitBtn.textContent.trim();

    function setFeedback(type, html) {
        feedback.className = 'contact-feedback alert mb-3';
        feedback.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        feedback.classList.remove('d-none');
        feedback.innerHTML = html;
    }

    function clearFeedback() {
        feedback.className = 'contact-feedback alert mb-3 d-none';
        feedback.innerHTML = '';
    }

    function clearFieldErrors() {
        form.querySelectorAll('.contact-field-error').forEach(function (item) {
            item.textContent = '';
        });
    }

    function showFieldErrors(errors) {
        Object.entries(errors || {}).forEach(function ([field, messages]) {
            const errorEl = form.querySelector(`[data-error-for="${field}"]`);
            if (errorEl) {
                errorEl.textContent = Array.isArray(messages) ? messages[0] : String(messages);
            }
        });
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        clearFeedback();
        clearFieldErrors();

        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const payload = await response.json().catch(function () {
                return {};
            });

            if (!response.ok) {
                if (response.status === 422) {
                    showFieldErrors(payload.errors || {});
                    setFeedback('error', payload.message || 'Please check the form and try again.');
                } else {
                    throw new Error(payload.message || 'Unable to send your message right now.');
                }
                return;
            }

            form.reset();
            setFeedback('success', payload.message || 'Thanks for contacting us. We will get back to you soon.');
        } catch (error) {
            setFeedback('error', error.message || 'Unable to send your message right now.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = defaultButtonText;
        }
    });
});
</script>
@endsection
