       @if ($hasFeaturedPosts)
           <section id="blog" aria-labelledby="blog-title" class="bg-amc-gray-bg">
               <div class=" mx-auto max-w-7xl px-6 py-20 lg:px-8">
                   <h2 id="blog-title" class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                       {{ __('From the field') }}
                   </h2>
                   <p class="mt-5 max-w-3xl text-lg leading-8 text-amc-gray-text">
                       {{ __('Read our latest perspectives, practical guides, and lessons from the work we do.') }}
                   </p>
                   <livewire:blog.featured-posts />
                   <a href="{{ route('blog') }}" data-id="link-to-posts"
                       class="mt-8 inline-flex text-base font-semibold text-amc-orange-text transition hover:text-amc-orange-hover">
                       {{ __('Ir al blog') }} <span aria-hidden="true">&rarr;</span>
                   </a>
               </div>
           </section>
       @endif
