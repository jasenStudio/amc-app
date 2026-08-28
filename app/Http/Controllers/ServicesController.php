<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServicesController extends Controller
{
    public function index(): View
    {
        $services = collect();

        if (Schema::hasTable('services')) {
            $services = Service::query()
                ->active()
                ->ordered()
                ->with(['coverImage'])
                ->paginate(12)
                ->appends(request()->query());
        }

        return view('pages::services.index', [
            'services' => $services,
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::query()
            ->active()
            ->with(['images'])
            ->where('slug', $slug)
            ->first();

        if ($service === null) {
            throw new NotFoundHttpException;
        }

        $relatedServices = Service::query()
            ->active()
            ->where('id', '!=', $service->id)
            ->inRandomOrder()
            ->with(['coverImage'])
            ->limit(3)
            ->get();

        return view('pages::services.show', [
            'service' => $service,
            'relatedServices' => $relatedServices,
        ]);
    }
}