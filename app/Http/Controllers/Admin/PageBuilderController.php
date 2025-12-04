<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageBuilderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Show page builder for a specific page
     */
    public function show(string $page)
    {
        $sections = Section::where('page', $page)
            ->with(['translations'])
            ->ordered()
            ->get();
        
        $availableSections = Section::where('page', $page)
            ->orWhereNull('page')
            ->with(['translations'])
            ->get()
            ->unique('id');
        
        $pages = ['home', 'about', 'contact', 'services', 'blog'];
        
        // Prepare sections data for JavaScript
        $sectionsData = $availableSections->map(function($section) {
            $currentLocale = app()->getLocale();
            $translation = $section->translate($currentLocale);
            $enTranslation = $section->translate('en');
            return [
                'id' => $section->id,
                'name' => $section->name,
                'type' => $section->type,
                'title' => ($translation && $translation->title) 
                    ? $translation->title 
                    : (($enTranslation && $enTranslation->title) ? $enTranslation->title : $section->name),
            ];
        })->keyBy('id');
        
        return view('admin.cms.page-builder.index', compact('page', 'sections', 'availableSections', 'pages', 'sectionsData'));
    }

    /**
     * Save page builder layout
     */
    public function saveLayout(Request $request, string $page)
    {
        $request->validate([
            'layout' => 'required|array',
            'layout.*.id' => 'required|exists:sections,id',
            'layout.*.row' => 'required|integer|min:0',
            'layout.*.column' => 'required|integer|min:0',
            'layout.*.width' => 'required|integer|min:1|max:12',
            'layout.*.order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Get section IDs that are in the layout
            $sectionIdsInLayout = collect($request->layout)->pluck('id')->toArray();
            
            // Remove sections from this page that are not in the layout
            Section::where('page', $page)
                ->whereNotIn('id', $sectionIdsInLayout)
                ->update([
                    'page' => null,
                    'builder_data' => null,
                ]);
            
            // Update sections in the layout
            foreach ($request->layout as $item) {
                $section = Section::findOrFail($item['id']);
                
                // Update section
                $section->update([
                    'page' => $page,
                    'order' => $item['order'],
                    'builder_data' => [
                        'row' => $item['row'],
                        'column' => $item['column'],
                        'width' => $item['width'],
                    ],
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => __('Page layout saved successfully.'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => __('Error saving layout: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sections for a page
     */
    public function getSections(string $page)
    {
        $sections = Section::where('page', $page)
            ->with(['translations'])
            ->ordered()
            ->get()
            ->map(function ($section) {
                $currentLocale = app()->getLocale();
                $translation = $section->translate($currentLocale);
                $enTranslation = $section->translate('en');
                
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'type' => $section->type,
                    'title' => ($translation && $translation->title) 
                        ? $translation->title 
                        : (($enTranslation && $enTranslation->title) ? $enTranslation->title : $section->name),
                    'order' => $section->order,
                    'builder_data' => $section->builder_data ?? [
                        'row' => 0,
                        'column' => 0,
                        'width' => 12,
                    ],
                ];
            });
        
        return response()->json($sections);
    }
}
