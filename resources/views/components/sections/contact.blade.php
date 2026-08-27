@props([
    'title' => __('Start your risk management today.'),
    'description' => __('Our technical team is ready to advise you on regulatory compliance and operational safety.'),
    'phoneLabel' => __('Call us'),
    'phoneNumber' => '+573147874006',
    'emailLabel' => __('Email'),
    'emailAddress' => 'gerencia@amcgestiondelriesgo.com.co',
])

<section id="contact" aria-labelledby="contact-title" class="bg-amc-gray-bg">
    <div class="mx-auto grid max-w-7xl gap-16 px-6 py-24 lg:grid-cols-6 lg:px-8">
        <div class="lg:col-span-3">
            <h2 id="contact-title" class="text-3xl font-bold text-amc-blue sm:text-4xl">
                {{ $title }}
            </h2>
            <p class="mt-6 text-base leading-relaxed text-amc-gray-text">{{ $description }}</p>

            <div class="mt-10 space-y-6">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-lg font-bold text-amc-orange-text shadow">
                        T
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amc-gray-text">
                            {{ $phoneLabel }}</p>
                        <a href="tel:{{ $phoneNumber }}"
                            class="text-base font-semibold text-amc-blue">{{ $phoneNumber }}</a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-lg font-bold text-amc-orange-text shadow">
                        M
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amc-gray-text">
                            {{ $emailLabel }}</p>
                        <a href="mailto:{{ $emailAddress }}"
                            class="text-base font-semibold text-amc-blue">{{ $emailAddress }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <livewire:contact.contact-form />
        </div>
    </div>
</section>
