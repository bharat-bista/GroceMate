@extends('inventory.layouts.inventory')

@section('title', 'Contact Messages - Ecommerce')
@section('heading', 'Contact Messages')
@section('subtitle', 'View customer inquiries')

@section('content')
<style>
    #contact-admin-results.is-loading {
        opacity: 0.6;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
</style>

<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Contact Messages</h2>
                    <p class="text-sm text-slate-600 mt-1">Messages submitted from the customer contact page</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.contacts.index') }}" class="space-y-4" id="contact-admin-filter-form">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Name, email, subject..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                            Search
                        </button>
                        <a href="{{ route('inventory.contacts.index') }}"
                           class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div id="contact-admin-results">
            @include('frontend.contact.admin.partials.results', ['messages' => $messages])
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contact-admin-filter-form');
    const results = document.getElementById('contact-admin-results');

    if (!form || !results) {
        return;
    }

    let debounceTimer = null;
    let activeRequest = null;

    function buildUrl() {
        const params = new URLSearchParams(new FormData(form));

        Array.from(params.entries()).forEach(function ([key, value]) {
            if (!String(value).trim()) {
                params.delete(key);
            }
        });

        const query = params.toString();
        return query ? `${form.action}?${query}` : form.action;
    }

    async function fetchResults(url, shouldPushState) {
        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();
        results.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error('Unable to load contact messages.');
            }

            const payload = await response.json();
            results.innerHTML = payload.html || '';

            if (shouldPushState) {
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url;
            }
        } finally {
            results.classList.remove('is-loading');
        }
    }

    function requestResults() {
        fetchResults(buildUrl(), true);
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        requestResults();
    });

    const searchInput = form.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(requestResults, 250);
        });
    }

    form.querySelectorAll('input[type="date"]').forEach(function (input) {
        input.addEventListener('change', requestResults);
    });

    results.addEventListener('click', function (event) {
        const pageLink = event.target.closest('.pagination a');
        if (!pageLink) {
            return;
        }

        event.preventDefault();
        fetchResults(pageLink.href, true);
    });

    window.addEventListener('popstate', function () {
        const currentUrl = new URL(window.location.href);
        form.querySelectorAll('input[name]').forEach(function (input) {
            input.value = currentUrl.searchParams.get(input.name) || '';
        });
        fetchResults(window.location.href, false);
    });
});
</script>
@endsection
