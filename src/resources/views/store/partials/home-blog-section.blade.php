@if(($blogPosts ?? collect())->isNotEmpty() && empty($showHomeProductSection))
<section class="w-full" aria-labelledby="home-blog-heading">
    <div class="mb-6">
        <h2 id="home-blog-heading" class="text-3xl font-bold tracking-tight text-slate-900">{{ __('store.blog.heading') }}</h2>
        <p class="mt-2 text-slate-600">{{ __('store.blog.intro') }}</p>
    </div>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($blogPosts as $post)
            <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-slate-300/80 hover:shadow-xl">
                <a href="{{ route('store.blog.show', $post) }}" class="block aspect-[16/10] overflow-hidden bg-slate-100">
                    @if($post->image_path)
                        <img src="{{ \App\Support\MediaUrl::public($post->image_path) }}" alt="{{ $post->localized_title }}" class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                    @else
                        <span class="flex h-full items-center justify-center text-4xl text-slate-300" aria-hidden="true">✎</span>
                    @endif
                </a>
                <div class="flex flex-1 flex-col p-5">
                    @if($post->published_at)
                        <time datetime="{{ $post->published_at->toDateString() }}" class="text-xs font-semibold uppercase tracking-wider text-primary-600">{{ $post->published_at->translatedFormat('d M Y') }}</time>
                    @endif
                    <h3 class="mt-2 text-lg font-semibold leading-snug text-slate-900">
                        <a href="{{ route('store.blog.show', $post) }}" class="hover:text-primary-600">{{ $post->localized_title }}</a>
                    </h3>
                    @if($post->localized_excerpt !== '')
                        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-500">{{ $post->localized_excerpt }}</p>
                    @endif
                    <a href="{{ route('store.blog.show', $post) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">
                        {{ __('store.blog.read') }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif
