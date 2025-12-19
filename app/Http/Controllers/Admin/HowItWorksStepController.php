<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HowItWorksStepRequest;
use App\Models\HowItWorksStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HowItWorksStepController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = HowItWorksStep::query();

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
                $tq->where('title', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $steps = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.how-it-works-steps.index', compact('steps', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $maxStepNumber = HowItWorksStep::max('step_number') ?? 0;
        $nextStepNumber = $maxStepNumber + 1;

        return view('admin.cms.how-it-works-steps.create', compact('locales', 'nextStepNumber'));
    }

    public function store(HowItWorksStepRequest $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('how-it-works-steps', 'public');
        }

        $step = HowItWorksStep::create([
            'icon' => $validated['icon'] ?? null,
            'image_path' => $validated['image_path'] ?? null,
            'step_number' => $validated['step_number'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $translation = $step->translateOrNew($locale);
            $translation->title = $validated["title_{$locale}"];
            $translation->description = $validated["description_{$locale}"];
            $translation->save();
        }
        
        // Refresh the step to ensure translations are loaded
        $step->refresh();

        return redirect()->route('admin.how-it-works-steps.index')
            ->with('success', __('Step created successfully.'));
    }

    public function edit(HowItWorksStep $howItWorksStep)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.how-it-works-steps.edit', compact('howItWorksStep', 'locales'));
    }

    public function update(HowItWorksStepRequest $request, HowItWorksStep $howItWorksStep)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        $validated = $request->validated();

        // Handle image_path update
        if ($request->hasFile('image_path')) {
            // Get the raw image_path value to avoid array conversion issues
            $oldImagePath = $howItWorksStep->getRawOriginal('image_path');
            if ($oldImagePath && is_string($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
            $validated['image_path'] = $request->file('image_path')->store('how-it-works-steps', 'public');
        } else {
            // Keep existing image_path if no new file is uploaded
            unset($validated['image_path']);
        }

        $howItWorksStep->update([
            'icon' => $validated['icon'] ?? $howItWorksStep->icon,
            'image_path' => $validated['image_path'] ?? $howItWorksStep->image_path,
            'step_number' => $validated['step_number'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $translation = $howItWorksStep->translateOrNew($locale);
            $translation->title = $validated["title_{$locale}"];
            $translation->description = $validated["description_{$locale}"];
            $translation->save();
        }
        
        // Refresh the step to ensure translations are loaded
        $howItWorksStep->refresh();

        return redirect()->route('admin.how-it-works-steps.index')
            ->with('success', __('Step updated successfully.'));
    }

    public function destroy(HowItWorksStep $howItWorksStep)
    {
        // Get the raw image_path value to avoid array conversion issues
        $imagePath = $howItWorksStep->getRawOriginal('image_path');
        if ($imagePath && is_string($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        $howItWorksStep->delete();

        return redirect()->route('admin.how-it-works-steps.index')
            ->with('success', __('Step deleted successfully.'));
    }
}
