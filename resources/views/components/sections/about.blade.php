@props([
    'eyebrow' => __('Nosotros'),
    'title' => __('Detrás de cada línea de vida hay una familia esperando en casa.'),
    'description' => __(
        'AMC Gestión de Riesgos SAS acompaña a empresas industriales, constructoras y del sector energético en el cumplimiento normativo y, sobre todo, en la protección real de sus equipos humanos. No entregamos formatos: entregamos operaciones más seguras.',
    ),
    'features' => [
        [
            'title' => __('Personal competente'),
            'description' => __(
                'Coordinadores y entrenadores avalados, con experiencia real en operación industrial de alto riesgo.',
            ),
        ],
        [
            'title' => __('Ingeniería documentada'),
            'description' => __(
                'Cada instalación se entrega con memorias de cálculo, fichas técnicas y certificados verificables.',
            ),
        ],
        [
            'title' => __('Respuesta en obra'),
            'description' => __(
                'Cobertura nacional con cuadrillas propias y tiempos de respuesta acordes al contrato.',
            ),
        ],
    ],
    'image' => 'about-us-amc-gestion-riesgo.webp',
    'imageAlt' => __('Equipo de AMC Gestión de Riesgos en obra con elementos de protección personal'),
])

<section aria-labelledby="nosotros-title" id="about" class="bg-amc-gray-bg px-6 py-10 md:py-10 lg:px-8">
    <div class="mx-auto grid max-w-7xl items-center gap-16 lg:grid-cols-2">
        <figure class="order-1 overflow-hidden rounded-xl shadow-xl">
            <img src="{{ asset('assets/images/' . $image) }}" width="768" height="512" alt="{{ $imageAlt }}"
                loading="lazy" decoding="async" class="aspect-4/3 w-full object-cover scale-125 origin-[83%_35%]">
        </figure>
        <div class="order-2">
            <h2 id="nosotros-title" class="mb-6 max-w-2xl text-3xl font-bold text-amc-blue sm:text-4xl">
                {{ $title }}
            </h2>
            <p class="mb-8 max-w-2xl text-base leading-relaxed text-amc-gray-text">{{ $description }}</p>

            <ul class="max-w-2xl space-y-8 border-l border-slate-300 pl-8">
                @foreach ($features as $feature)
                    <li>
                        <h3 class="mb-2 text-lg font-semibold text-amc-blue">{{ $feature['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-amc-gray-text">{{ $feature['description'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
