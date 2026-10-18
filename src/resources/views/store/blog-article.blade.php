@extends('store.layout')

@section('title', $post->localized_title)

@push('meta')
    @if($post->localized_excerpt !== '')
        <meta name="description" content="{{ $post->localized_excerpt }}">
    @endif
@endpush

@section('content')
    <style>
        .blog-body { color: rgb(51 65 85); }
        .blog-body h2 { font-size: 1.15rem; font-weight: 600; color: rgb(15 23 42); margin-top: 1.75rem; }
        .blog-body h3 { font-size: 1rem; font-weight: 600; color: rgb(30 41 59); margin-top: 1.25rem; }
        .blog-body p { margin-top: 0.85rem; line-height: 1.75; }
        .blog-body ul, .blog-body ol { margin-top: 0.6rem; margin-bottom: 0.4rem; padding-left: 1.25rem; }
        .blog-body ul { list-style: disc; }
        .blog-body ol { list-style: decimal; }
        .blog-body li { margin-top: 0.28rem; line-height: 1.65; }
        .blog-body a { color: rgb(21 95 179); text-decoration: underline; text-underline-offset: 2px; }
        .blog-body strong { color: rgb(15 23 42); font-weight: 600; }
        .blog-body img { margin-top: 1rem; max-width: 100%; height: auto; border-radius: 0.75rem; }
    </style>

    <article class="mx-auto w-full max-w-3xl">
        <a href="{{ route('home') }}#blog" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('store.blog.back') }}</a>
        @if($post->published_at)
            <time datetime="{{ $post->published_at->toDateString() }}" class="mt-4 block text-xs font-semibold uppercase tracking-wider text-primary-600">{{ $post->published_at->translatedFormat('d M Y') }}</time>
        @endif
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $post->localized_title }}</h1>
        @if($post->image_path)
            <img src="{{ \App\Support\MediaUrl::public($post->image_path) }}" alt="" class="mt-6 w-full rounded-2xl border border-slate-200 object-cover">
        @endif
        <div class="blog-body mt-6 text-sm sm:text-[0.95rem]">
            {!! $post->localized_body !!}
        </div>
    </article>
@endsection
