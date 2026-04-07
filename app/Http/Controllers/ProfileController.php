<?php

namespace App\Http\Controllers;

use App\Models\GeneratedImage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __invoke(): Response
    {
        $user = Auth::user();

        $recentImages = GeneratedImage::query()
            ->where('user_id', $user?->id)
            ->whereNotNull('output_image_path')
            ->latest()
            ->limit(12)
            ->get()
            ->map(function (GeneratedImage $image) {
                return [
                    'id' => $image->id,
                    'url' => $this->resolveOutputUrl($image->output_image_path),
                    'status' => $image->status,
                    'prompt' => $image->prompt,
                    'created_at' => $image->created_at?->format('d/m H:i'),
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('Profile', [
            'recentImages' => $recentImages,
        ]);
    }

    private function resolveOutputUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }
}
