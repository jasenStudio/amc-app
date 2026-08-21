<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Images\UploadImageAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        Gate::authorize('manage-posts');

        $request->validate([
            'upload' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=3000,max_height=3000',
            ],
            'path' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-\/]+$/'],
            'slugHint' => ['nullable', 'string', 'max:200'],
        ]);

        try {
            $paths = app(UploadImageAction::class)(
                $request->file('upload'),
                $request->input('path'),
                $request->input('slugHint'),
                'public',
            );

            $url = Storage::disk('public')->url($paths['full']);

            return response()->json([
                'thumb' => $paths['thumb'],
                'full' => $paths['full'],
                'url' => $url,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => ['message' => $e->getMessage()],
            ], 422);
        }
    }
}
