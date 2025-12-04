<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, add temporary JSON columns
        Schema::table('pages', function (Blueprint $table) {
            $table->json('title_json')->nullable()->after('title');
            $table->json('content_json')->nullable()->after('content');
            $table->json('meta_title_json')->nullable()->after('meta_title');
            $table->json('meta_description_json')->nullable()->after('meta_description');
            $table->json('meta_keywords_json')->nullable()->after('meta_keywords');
            $table->json('excerpt_json')->nullable()->after('excerpt');
            $table->json('menu_label_json')->nullable()->after('menu_label');
        });
        
        // Migrate existing data to JSON format
        $pages = DB::table('pages')->get();
        foreach ($pages as $page) {
            $locale = $page->locale ?? 'en';
            $updates = [];
            
            if ($page->title) {
                $updates['title_json'] = json_encode([$locale => $page->title]);
            }
            if ($page->content) {
                $updates['content_json'] = json_encode([$locale => $page->content]);
            }
            if ($page->meta_title) {
                $updates['meta_title_json'] = json_encode([$locale => $page->meta_title]);
            }
            if ($page->meta_description) {
                $updates['meta_description_json'] = json_encode([$locale => $page->meta_description]);
            }
            if ($page->meta_keywords) {
                $updates['meta_keywords_json'] = json_encode([$locale => $page->meta_keywords]);
            }
            if ($page->excerpt) {
                $updates['excerpt_json'] = json_encode([$locale => $page->excerpt]);
            }
            if ($page->menu_label) {
                $updates['menu_label_json'] = json_encode([$locale => $page->menu_label]);
            }
            
            if (!empty($updates)) {
                DB::table('pages')->where('id', $page->id)->update($updates);
            }
        }
        
        // Drop old columns and rename new ones
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'meta_title', 'meta_description', 'meta_keywords', 'excerpt', 'menu_label']);
        });
        
        Schema::table('pages', function (Blueprint $table) {
            $table->json('title')->after('slug');
            $table->json('content')->nullable()->after('title');
            $table->json('meta_title')->nullable()->after('is_published');
            $table->json('meta_description')->nullable()->after('meta_title');
            $table->json('meta_keywords')->nullable()->after('meta_description');
            $table->json('excerpt')->nullable()->after('featured_image');
            $table->json('menu_label')->nullable()->after('show_in_menu');
        });
        
        // Copy data from temporary columns
        $pages = DB::table('pages')->get();
        foreach ($pages as $page) {
            $updates = [];
            if ($page->title_json) $updates['title'] = $page->title_json;
            if ($page->content_json) $updates['content'] = $page->content_json;
            if ($page->meta_title_json) $updates['meta_title'] = $page->meta_title_json;
            if ($page->meta_description_json) $updates['meta_description'] = $page->meta_description_json;
            if ($page->meta_keywords_json) $updates['meta_keywords'] = $page->meta_keywords_json;
            if ($page->excerpt_json) $updates['excerpt'] = $page->excerpt_json;
            if ($page->menu_label_json) $updates['menu_label'] = $page->menu_label_json;
            
            if (!empty($updates)) {
                DB::table('pages')->where('id', $page->id)->update($updates);
            }
        }
        
        // Drop temporary columns
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title_json', 'content_json', 'meta_title_json', 'meta_description_json', 'meta_keywords_json', 'excerpt_json', 'menu_label_json']);
        });
    }

    public function down(): void
    {
        // Convert JSON back to string (using first available locale)
        $pages = DB::table('pages')->get();
        foreach ($pages as $page) {
            $updates = [];
            
            if ($page->title) {
                $titleData = json_decode($page->title, true);
                $updates['title'] = is_array($titleData) ? (reset($titleData) ?? '') : $page->title;
            }
            if ($page->content) {
                $contentData = json_decode($page->content, true);
                $updates['content'] = is_array($contentData) ? (reset($contentData) ?? '') : $page->content;
            }
            if ($page->meta_title) {
                $metaTitleData = json_decode($page->meta_title, true);
                $updates['meta_title'] = is_array($metaTitleData) ? (reset($metaTitleData) ?? '') : $page->meta_title;
            }
            if ($page->meta_description) {
                $metaDescData = json_decode($page->meta_description, true);
                $updates['meta_description'] = is_array($metaDescData) ? (reset($metaDescData) ?? '') : $page->meta_description;
            }
            if ($page->meta_keywords) {
                $metaKeywordsData = json_decode($page->meta_keywords, true);
                $updates['meta_keywords'] = is_array($metaKeywordsData) ? (reset($metaKeywordsData) ?? '') : $page->meta_keywords;
            }
            if ($page->excerpt) {
                $excerptData = json_decode($page->excerpt, true);
                $updates['excerpt'] = is_array($excerptData) ? (reset($excerptData) ?? '') : $page->excerpt;
            }
            if ($page->menu_label) {
                $menuLabelData = json_decode($page->menu_label, true);
                $updates['menu_label'] = is_array($menuLabelData) ? (reset($menuLabelData) ?? '') : $page->menu_label;
            }
            
            if (!empty($updates)) {
                DB::table('pages')->where('id', $page->id)->update($updates);
            }
        }
        
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'meta_title', 'meta_description', 'meta_keywords', 'excerpt', 'menu_label']);
        });
        
        Schema::table('pages', function (Blueprint $table) {
            $table->string('title')->after('slug');
            $table->text('content')->nullable()->after('title');
            $table->string('meta_title')->nullable()->after('is_published');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->text('excerpt')->nullable()->after('featured_image');
            $table->string('menu_label')->nullable()->after('show_in_menu');
        });
    }
};
