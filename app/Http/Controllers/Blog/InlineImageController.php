<?php

namespace App\Http\Controllers\Blog;

use App\Actions\Images\UploadInlineImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class InlineImageController extends Controller
{
    public function __invoke(Request $request, UploadInlineImage $action): JsonResponse
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
        ]);

        try {
            $url = $action($request->file('upload'));
        } catch (\Throwable $e) {
            return response()->json([
                'error' => ['message' => $e->getMessage()],
            ], 422);
        }

        return response()->json(['url' => $url]);
    }
}
