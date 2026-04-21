@extends('inventory.layouts.inventory')

@section('title', 'Contact Message - Ecommerce')
@section('heading', 'Contact Message')
@section('subtitle', 'View customer inquiry')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('inventory.contacts.index') }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-left"></i> Back to Messages
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">{{ $message->subject }}</h2>
                            <p class="text-sm text-slate-500 mt-1">
                                Received {{ $message->created_at->format('M d, Y \a\t h:i A') }}
                            </p>
                        </div>
                        @if($message->email)
                            <div class="self-end lg:ml-auto">
                                <button type="button" id="sendContactEmailBtn"
                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                                    <i class="fas fa-envelope mr-2"></i> Send Email
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-slate-700 whitespace-pre-line">{{ $message->message }}</div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Sender Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Name</label>
                        <span class="font-medium text-slate-900">{{ $message->name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Email</label>
                        <a href="mailto:{{ $message->email }}" class="font-medium text-emerald-600 hover:text-emerald-700">
                            {{ $message->email }}
                        </a>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Phone</label>
                        <span class="text-slate-700">{{ $message->phone ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Message Modal -->
<div id="contactEmailModal"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
     style="display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 animate-scaleIn"
         style="width: 100%; max-width: 520px; margin: 0 auto;">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Send Message to Customer</h3>
        <p class="text-gray-600 text-sm mb-5">Write a reply and send it to the customer's email.</p>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Email</label>
            <input type="email" id="contactCustomerEmailInput" class="w-full rounded-xl border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:outline-none" readonly>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea id="contactCustomerMessageInput" rows="5"
                      class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                      placeholder="Type your message here..."></textarea>
            <p id="contactEmailError" class="text-sm text-red-600 mt-2 hidden">Please enter a message.</p>
        </div>

        <div class="flex gap-3">
            <button type="button" id="sendContactEmailConfirm" class="flex-1 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition">
                Send Email
            </button>
            <button type="button" id="sendContactEmailCancel" class="flex-1 py-2 rounded-xl bg-gray-600 text-white hover:bg-gray-700 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
const contactEmailModal = document.getElementById('contactEmailModal');
const sendContactEmailBtn = document.getElementById('sendContactEmailBtn');
const sendContactEmailConfirm = document.getElementById('sendContactEmailConfirm');
const sendContactEmailCancel = document.getElementById('sendContactEmailCancel');
const contactCustomerEmailInput = document.getElementById('contactCustomerEmailInput');
const contactCustomerMessageInput = document.getElementById('contactCustomerMessageInput');
const contactEmailError = document.getElementById('contactEmailError');

if (sendContactEmailBtn) {
    sendContactEmailBtn.addEventListener('click', function () {
        contactCustomerEmailInput.value = "{{ $message->email ?? '' }}";
        contactCustomerMessageInput.value = '';
        contactEmailError.classList.add('hidden');
        contactEmailModal.style.display = 'flex';
    });
}

sendContactEmailConfirm?.addEventListener('click', function () {
    const message = contactCustomerMessageInput.value.trim();

    if (!message) {
        contactEmailError.classList.remove('hidden');
        return;
    }

    contactEmailError.classList.add('hidden');
    contactEmailModal.style.display = 'none';

    sendContactEmailBtn.disabled = true;
    sendContactEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';

    fetch('{{ route("inventory.contacts.send-message", $message) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
    })
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('.max-w-6xl');
            if (!container) {
                return;
            }
            const alertDiv = document.createElement('div');
            alertDiv.className = data.success
                ? 'p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm'
                : 'p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm';
            alertDiv.textContent = data.message || 'Unable to send the email.';
            container.insertBefore(alertDiv, container.firstChild);
            setTimeout(() => alertDiv.remove(), 5000);
        })
        .catch(() => {
            const container = document.querySelector('.max-w-6xl');
            if (!container) {
                return;
            }
            const alertDiv = document.createElement('div');
            alertDiv.className = 'p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm';
            alertDiv.textContent = 'Failed to send email. Please try again.';
            container.insertBefore(alertDiv, container.firstChild);
            setTimeout(() => alertDiv.remove(), 5000);
        })
        .finally(() => {
            sendContactEmailBtn.disabled = false;
            sendContactEmailBtn.innerHTML = '<i class="fas fa-envelope mr-2"></i> Send Email';
        });
});

sendContactEmailCancel?.addEventListener('click', function () {
    contactEmailModal.style.display = 'none';
});

contactEmailModal?.addEventListener('click', function (event) {
    if (event.target === contactEmailModal) {
        contactEmailModal.style.display = 'none';
    }
});
</script>
@endsection
