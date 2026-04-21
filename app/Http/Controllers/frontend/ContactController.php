<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check the highlighted fields and try again.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        ContactMessage::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thanks for contacting us. We will get back to you soon.',
            ]);
        }

        return back()->with('success', 'Thanks for contacting us. We will get back to you soon.');
    }

    public function adminIndex(Request $request)
    {
        $messages = ContactMessage::query()
            ->select(['id', 'name', 'email', 'phone', 'subject', 'message', 'created_at'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%' . $request->input('search') . '%';
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('message', 'like', $term);
                });
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('to'));
            })
            ->latest()
            ->paginate(15);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('frontend.contact.admin.partials.results', compact('messages'))->render(),
            ]);
        }

        return view('frontend.contact.admin.index', compact('messages'));
    }

    public function adminShow(ContactMessage $contactMessage)
    {
        return view('frontend.contact.admin.show', [
            'message' => $contactMessage,
        ]);
    }

    public function sendMessage(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if (!$contactMessage->email) {
            return response()->json([
                'success' => false,
                'message' => 'Customer does not have an email address.',
            ], 400);
        }

        try {
            Mail::to($contactMessage->email)->send(new ContactReplyMail($contactMessage, $validated['message']));

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully to ' . $contactMessage->email,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send contact reply: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please check your email configuration.',
            ], 500);
        }
    }
}
