<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Statistic::query();

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('value', 'like', "%{$search}%")
                  ->orWhereHas('translations', function($tq) use ($search) {
                      $tq->where('label', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
                  });
            });
        }

        $statistics = $query->ordered()->get();
        $locales = config('localization.supported_locales', ['en']);

        return view('admin.cms.statistics.index', compact('statistics', 'locales'));
    }

    public function create()
    {
        $locales = config('localization.supported_locales', ['en']);
        return view('admin.cms.statistics.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en']);
        $rules = [
            'value' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["label_{$locale}"] = 'required|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $statistic = Statistic::create([
            'value' => $validated['value'],
            'icon' => $validated['icon'] ?? null,
            'suffix' => $validated['suffix'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $statistic->translateOrNew($locale)->label = $validated["label_{$locale}"];
            $statistic->translateOrNew($locale)->description = $validated["description_{$locale}"] ?? null;
        }
        $statistic->save();

        return redirect()->route('admin.statistics.index')
            ->with('success', __('Statistic created successfully.'));
    }

    public function edit(Statistic $statistic)
    {
        $locales = config('localization.supported_locales', ['en']);
        return view('admin.cms.statistics.edit', compact('statistic', 'locales'));
    }

    public function update(Request $request, Statistic $statistic)
    {
        $locales = config('localization.supported_locales', ['en']);
        $rules = [
            'value' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($locales as $locale) {
            $rules["label_{$locale}"] = 'required|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $statistic->update([
            'value' => $validated['value'],
            'icon' => $validated['icon'] ?? null,
            'suffix' => $validated['suffix'] ?? null,
            'order' => $validated['order'] ?? $statistic->order,
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($locales as $locale) {
            $statistic->translateOrNew($locale)->label = $validated["label_{$locale}"];
            $statistic->translateOrNew($locale)->description = $validated["description_{$locale}"] ?? null;
        }
        $statistic->save();

        return redirect()->route('admin.statistics.index')
            ->with('success', __('Statistic updated successfully.'));
    }

    public function destroy(Statistic $statistic)
    {
        $statistic->delete();

        return redirect()->route('admin.statistics.index')
            ->with('success', __('Statistic deleted successfully.'));
    }
}
