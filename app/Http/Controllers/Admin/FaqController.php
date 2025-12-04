<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->has('category') && $request->category !== '') {
            $query->byCategory($request->category);
        }

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($tq) use ($search) {
                $tq->where('question', 'like', "%{$search}%")
                   ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $faqs = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $categories = Faq::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.cms.faqs.index', compact('faqs', 'locales', 'categories'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $categories = Faq::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.cms.faqs.create', compact('locales', 'categories'));
    }

    public function store(FaqRequest $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        $faq = Faq::create([
            'category' => $validated['category'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $faq->translateOrNew($locale)->question = $validated["question_{$locale}"];
            $faq->translateOrNew($locale)->answer = $validated["answer_{$locale}"];
        }
        $faq->save();

        return redirect()->route('admin.faqs.index')
            ->with('success', __('FAQ created successfully.'));
    }

    public function edit(Faq $faq)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $categories = Faq::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.cms.faqs.edit', compact('faq', 'locales', 'categories'));
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        $faq->update([
            'category' => $validated['category'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $faq->translateOrNew($locale)->question = $validated["question_{$locale}"];
            $faq->translateOrNew($locale)->answer = $validated["answer_{$locale}"];
        }
        $faq->save();

        return redirect()->route('admin.faqs.index')
            ->with('success', __('FAQ updated successfully.'));
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', __('FAQ deleted successfully.'));
    }
}
