<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsletterSubscriberRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterSubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->subscribed();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'unsubscribed') {
                $query->whereNotNull('unsubscribed_at');
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->latest('subscribed_at')->paginate(20);

        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::subscribed()->count(),
            'unsubscribed' => NewsletterSubscriber::whereNotNull('unsubscribed_at')->count(),
        ];

        return view('admin.cms.newsletter-subscribers.index', compact('subscribers', 'stats'));
    }

    public function create()
    {
        return view('admin.cms.newsletter-subscribers.create');
    }

    public function store(NewsletterSubscriberRequest $request)
    {
        $validated = $request->validated();

        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'source' => $validated['source'] ?? 'admin',
            'subscribed_at' => now(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.newsletter-subscribers.index')
            ->with('success', __('Subscriber added successfully.'));
    }

    public function show(NewsletterSubscriber $newsletterSubscriber)
    {
        return view('admin.cms.newsletter-subscribers.show', compact('newsletterSubscriber'));
    }

    public function edit(NewsletterSubscriber $newsletterSubscriber)
    {
        return view('admin.cms.newsletter-subscribers.edit', compact('newsletterSubscriber'));
    }

    public function update(NewsletterSubscriberRequest $request, NewsletterSubscriber $newsletterSubscriber)
    {
        $validated = $request->validated();

        $newsletterSubscriber->update($validated);

        return redirect()->route('admin.newsletter-subscribers.index')
            ->with('success', __('Subscriber updated successfully.'));
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->delete();

        return redirect()->route('admin.newsletter-subscribers.index')
            ->with('success', __('Subscriber deleted successfully.'));
    }

    public function unsubscribe(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->unsubscribe();

        return redirect()->back()
            ->with('success', __('Subscriber unsubscribed successfully.'));
    }

    public function resubscribe(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->resubscribe();

        return redirect()->back()
            ->with('success', __('Subscriber resubscribed successfully.'));
    }

    public function export()
    {
        $subscribers = NewsletterSubscriber::subscribed()->get(['email', 'name', 'subscribed_at']);
        
        $filename = 'newsletter_subscribers_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Name', 'Subscribed At']);
            
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name ?? '',
                    $subscriber->subscribed_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
