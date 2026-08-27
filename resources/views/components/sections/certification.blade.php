@props([
    'title' => __('certification_title'),
    'rightTitle' => __('sectors_title'),
    'certifications' => [
        ['name' => 'Res. 4272 de 2021', 'status' => __('VIGENTE')],
        ['name' => 'ANSI/ASSP Z359', 'status' => __('VIGENTE')],
        ['name' => 'EN 795:2012', 'status' => __('VIGENTE')],
        ['name' => 'Personal avalado ONAC', 'status' => __('VIGENTE')],
        ['name' => 'SG-SST Decreto 1072', 'status' => __('VIGENTE')],
    ],
    'sectors' => [
        __('manufacturing'),
        __('construction'),
        __('energy'),
        __('telecommunications'),
        __('logistics'),
        __('agribusiness'),
    ],
])

<section id="certifications" aria-labelledby="certifications-title" class="bg-amc-gray-bg">
    <div class="mx-auto max-w-7xl px-6 py-28 lg:px-8">
        <div class="grid grid-cols-1 gap-x-16 gap-y-16 lg:grid-cols-2">
            <div>
                <h2 id="certifications-title" class="mb-10 text-3xl font-bold text-amc-blue sm:text-4xl">
                    {{ $title }}
                </h2>
                <ul class="divide-y divide-slate-300">
                    @foreach ($certifications as $cert)
                        <li class="flex items-center justify-between py-5">
                            <span class="text-base text-amc-blue">{{ $cert['name'] }}</span>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-amc-orange-text">
                                {{ $cert['status'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="mb-10 text-3xl font-bold text-amc-blue sm:text-4xl">
                    {{ $rightTitle }}
                </h2>
                @php
                    $cols = 2;
                    $total = count($sectors);
                    $lastRowStart = $total - ($total % $cols === 0 ? $cols : $total % $cols);
                @endphp
                <ul class="grid grid-cols-1 sm:grid-cols-2">
                    @foreach ($sectors as $i => $sector)
                        <li
                            class="py-5 sm:px-6 {{ $i % $cols === 0 ? 'sm:border-r sm:border-slate-300' : '' }} {{ $i < $lastRowStart ? 'border-b border-slate-300' : '' }}">
                            <span class="text-base text-amc-blue">{{ $sector }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
