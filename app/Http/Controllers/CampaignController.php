<?php

namespace App\Http\Controllers;

use App\Jobs\Pipeline\GenerateScriptJob;
use App\Models\Campaign;
use App\Support\AffiliateCreativeBrief;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::withCount('videos')->latest()->get();

        return Inertia::render('Campaign/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_url' => 'nullable|url',
            'product_description' => 'nullable|string|max:4000',
            'generation_mode' => 'nullable|in:fast_test,premium_product,winner_scale',
            'total_videos' => 'nullable|integer|min:1|max:20',
            'duration_seconds' => 'required|integer|min:12|max:35',
            'product_images' => 'required|array|min:3|max:8',
            'product_images.*' => 'image|max:8192',
        ]);

        $productName = $validated['product_name'];
        $generationMode = $validated['generation_mode'] ?? 'fast_test';
        $totalVideos = (int) ($validated['total_videos'] ?? 1);

        $imagePaths = [];
        foreach ($request->file('product_images', []) as $image) {
            $imagePaths[] = $image->store('affiliate-products', 'public');
        }

        $campaign = Campaign::create([
            'name' => $productName,
            'niche' => 'TikTok Shop Affiliate',
            'base_prompt' => $validated['product_description'] ?? null,
            'affiliate_link' => $validated['product_url'] ?? '',
            'total_videos' => $totalVideos,
            'affiliate_ratio' => 100,
            'status' => 'generating',
        ]);

        for ($i = 0; $i < $totalVideos; $i++) {
            $brief = AffiliateCreativeBrief::forVariation($generationMode, $i);

            $video = $campaign->videos()->create([
                'title' => $brief['format'] . ' #' . ($i + 1),
                'video_type' => 'affiliate',
                'generation_mode' => $generationMode,
                'product_name' => $productName,
                'product_url' => $validated['product_url'] ?? null,
                'product_description' => $validated['product_description'] ?? null,
                'product_images' => $imagePaths,
                'sales_angle' => $brief['angle'],
                'duration_seconds' => $validated['duration_seconds'],
                'aspect_ratio' => '9:16',
                'quality' => '1080p',
                'status' => 'draft',
            ]);

            GenerateScriptJob::dispatch($video);
        }

        return redirect()->back()->with('success', 'Dang tao video affiliate TikTok Shop.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('videos');

        return Inertia::render('Campaign/Show', [
            'campaign' => $campaign,
        ]);
    }
}
