<x-layouts::app :title="__('Blog | AMC Gestión de Riesgos')" :description="__('Perspectivas, guías e ideas prácticas de AMC Gestión de Riesgos.')">
    <x-header />

    <main class="bg-amc-gray-bg">

        <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Blog') }}</p>
            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                {{ __('Ideas for moving forward.') }}
            </h1>
            <p class="mt-6 max-w-3xl text-lg leading-8 text-amc-gray-text">
                {{ __('Perspectives, pratical guides') }}
            </p>


            <form method="GET" action="{{ route('blog') }}" class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">

                {{-- Categoría --}}
                @if ($tags->isNotEmpty())
                    <div class="relative flex-1 sm:max-w-58">
                        <span class="icon-controls absolute top-3 left-2 text-gray-500">
                            <x-icon name="folder-tree"
                                class="pointer-events-none absolute left-3 top-1/2  -translate-y-1/2 text-zinc-400" />
                        </span>
                        <select name="tag" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-9 text-sm text-zinc-700 focus:border-amc-orange-text focus:outline-none focus:ring-1 focus:ring-amc-orange-text ">
                            <option value="">{{ __('Select Categories') }}</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->slug }}" @selected($activeTag === $tag->slug)>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                @endif

                {{-- Búsqueda --}}
                <div class="relative flex-1 sm:max-w-72">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="{{ __('Search articles...') }}"
                        class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-3 text-sm text-zinc-700 placeholder:text-zinc-400 focus:border-amc-orange-text focus:outline-none focus:ring-1 focus:ring-amc-orange-text">
                    <button type="submit" class="icon-controls absolute top-3 right-2 text-gray-500">
                        <x-icon name="search"
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    </button>
                </div>

                {{-- Fecha --}}
                <div class="relative flex-1 sm:max-w-48">

                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                        class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-3 text-sm text-zinc-700 focus:border-amc-orange-text focus:outline-none focus:ring-1 focus:ring-amc-orange-text">
                </div>

                {{-- Submit accesible sin JS (por si el select/date no disparan onchange) --}}
                <button type="submit" class="sr-only">{{ __('Filter') }}</button>
            </form>


            @if ($posts->isEmpty())
                <p class="mt-12 text-lg text-zinc-500">{{ __('No posts yet.') }}</p>
            @else
                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group block">
                            @if ($post->cover_image_thumb_url)
                                <img src="{{ $post->cover_image_thumb_url }}" alt=""
                                    class="aspect-3/2 w-full rounded-lg object-cover" loading="lazy">
                            @else
                                <div class="aspect-3/2 w-full rounded-lg bg-zinc-100 dark:bg-zinc-800"></div>
                            @endif
                            <div class="mt-4">
                                @if ($post->tags->isNotEmpty())
                                    <div class="mb-2 flex flex-wrap gap-1">
                                        @foreach ($post->tags as $tag)
                                            <span
                                                class="text-xs font-medium text-amc-orange-text">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <h3
                                    class="text-lg font-semibold text-amc-blue group-hover:text-amc-orange transition dark:group-hover:text-amc-orange group-hover:underline group-hover:underline-offset-3">
                                    {{ $post->title }}
                                </h3>
                                @if ($post->excerpt)
                                    <p class="mt-1 text-sm text-zinc-500 line-clamp-2">{{ $post->excerpt }}</p>
                                @endif
                                <p class="mt-2 text-xs text-zinc-500">{{ $post->published_at?->format('M d, Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>


    </main>
</x-layouts::app>
