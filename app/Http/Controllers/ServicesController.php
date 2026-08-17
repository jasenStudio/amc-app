<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServicesController extends Controller
{
    public function index(): View
    {
        return view('pages::services', ['services' => config('services.items')]);
    }

    public function show(string $slug): View
    {
        $items = config('services.items');

        $service = is_array($items) ? collect($items)->firstWhere('slug', $slug) : null;

        if (! is_array($service)) {
            throw new NotFoundHttpException;
        }

        return view('pages::services.show', compact('service'));
    }
}
