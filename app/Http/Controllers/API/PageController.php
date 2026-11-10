<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function showBySlug($slug)
    {
        try {
            $page = Page::where('slug', $slug)->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message'  => "Page fetched successfully",
                'data' => [
                    'title' => $page->title,
                    'content' => $page->content,
                    'content_de' => $page->content_de,
                    'content_cz' => $page->content_cz,
                    'image' => $page->image ? asset('storage/' . $page->image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'  => "Unable to fetch pages",
                'message' => 'Failed to fetch page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function slug()
    {
        try {
        $slug = DB::table('pages')->select('slug')->get();
        return response()->json([
                'success' => true,
                'message'  => "Slugs fetched successfully",
                'data' => $slug
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'  => "Unable to fetch slug",
                'message' => 'Failed to fetch page: ' . $e->getMessage()
            ], 500);
        }
    }
    

    public function getPrivacyTermsPages()
    {
        try {
            $pages = Page::select("title", "slug", "content", "image")->get();
    
            $pages->transform(function ($page) {
                $page->image = $page->image
                    ? asset('storage/' . $page->image)
                    : null;
    
                return $page;
            });
    
            return response()->json([
                'code' => 200,
                'message' => 'Pages fetched successfully',
                'data' => $pages,
            ], 200);
    
        } catch (\Exception $exception) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal server error',
            ], 500);
        }
    }
    
      public function getVersion()
    {
            // Get latest version from DB
            $version = DB::table('app_versions')->latest()->first();
    
            // If no record found, return defaults
            if (!$version) {
                return response()->json([
                    'latestVersion' => '1.0.0',
                    'minRequiredVersion' => '1.0.0',
                    'forceUpdate' => false,
                    'androidUrl' => 'https://play.google.com/store/apps/details?id=com.app',
                    'iosUrl' => 'https://apps.apple.com/app/id123456'
                ]);
            }
    
            // Return version info with URLs
            return response()->json([
                'latestVersion' => $version->latest_version,
                'minRequiredVersion' => $version->min_required_version,
                'forceUpdate' => (bool)$version->force_update,
                'androidUrl' => $version->android_url ?? 'https://play.google.com/store/apps/details?id=com.app',
                'iosUrl' => $version->ios_url ?? 'https://apps.apple.com/app/id123456'
            ]);
    }
}