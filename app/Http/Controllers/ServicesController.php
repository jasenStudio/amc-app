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
        $service = collect(config('services.items'))->firstWhere('slug', $slug);

        if ($service === null) {
            throw new NotFoundHttpException;
        }

        return view('pages::services.show', compact('service'));
    }
}
