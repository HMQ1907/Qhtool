<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Jobs\Pipeline\GenerateScriptJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::withCount('videos')->latest()->get();
        return Inertia::render('Campaign/Index', [
            'campaigns' => $campaigns
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_videos' => 'required|integer|min:1|max:50',
            'affiliate_ratio' => 'required|integer|min:0|max:100',
            'affiliate_link' => 'nullable|url',
        ]);

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'total_videos' => $validated['total_videos'],
            'affiliate_ratio' => $validated['affiliate_ratio'],
            'affiliate_link' => $validated['affiliate_link'] ?? '',
            'status' => 'generating'
        ]);

        $affiliateCount = (int) round(($validated['total_videos'] * $validated['affiliate_ratio']) / 100);

        for ($i = 0; $i < $validated['total_videos']; $i++) {
            $type = ($i < $affiliateCount) ? 'affiliate' : 'monetization';
            
            $video = $campaign->videos()->create([
                'title' => 'Video Phần ' . ($i + 1),
                'video_type' => $type,
                'duration_seconds' => $type === 'affiliate'
                    ? config('evolink.affiliate_duration', 45)
                    : config('evolink.monetization_duration', 30),
                'aspect_ratio' => config('evolink.video_aspect_ratio', '9:16'),
                'quality' => config('evolink.video_quality', '720p'),
                'status' => 'draft'
            ]);

            // Đẩy vào luồng tự động
            GenerateScriptJob::dispatch($video);
        }

        return redirect()->back()->with('success', 'Chiến dịch Tâm lý học đang được tự động sản xuất bởi AI toàn diện!');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('videos');
        return Inertia::render('Campaign/Show', [
            'campaign' => $campaign
        ]);
    }
}
