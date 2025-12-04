<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }

        $messages = $query->latest()->paginate(20);

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::whereNull('read_at')->count(),
            'read' => ContactMessage::whereNotNull('read_at')->count(),
        ];

        return view('admin.cms.contact-messages.index', compact('messages', 'stats'));
    }

    public function show(ContactMessage $contactMessage)
    {
        // Mark as read
        if (!$contactMessage->read_at) {
            $contactMessage->update(['read_at' => now()]);
        }

        return view('admin.cms.contact-messages.show', compact('contactMessage'));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact_messages.index')
            ->with('success', __('Message deleted successfully.'));
    }

    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read_at' => now()]);

        return redirect()->back()
            ->with('success', __('Message marked as read.'));
    }

    public function markAsUnread(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read_at' => null]);

        return redirect()->back()
            ->with('success', __('Message marked as unread.'));
    }
}
