@extends('store.layout')

@section('title', $page->localized_title)

@section('content')
    <style>
        .legal-body { color: rgb(51 65 85); }
        .legal-body h1 { font-size: 1.5rem; line-height: 1.3; font-weight: 700; color: rgb(15 23 42); letter-spacing: -0.02em; }
        .legal-body h2 { font-size: 1.05rem; font-weight: 600; color: rgb(15 23 42); margin-top: 1.75rem; letter-spacing: 0.01em; }
        .legal-body h3 { font-size: 0.95rem; font-weight: 600; color: rgb(30 41 59); margin-top: 1.25rem; }
        .legal-body p { margin-top: 0.85rem; line-height: 1.75; }
        .legal-body p > strong:first-child { color: rgb(15 23 42); font-weight: 600; }
        .legal-body ul { margin-top: 0.6rem; margin-bottom: 0.4rem; padding-left: 1.25rem; list-style: disc; }
        .legal-body li { margin-top: 0.28rem; line-height: 1.65; }
        .legal-body a { color: rgb(21 95 179); text-decoration: underline; text-underline-offset: 2px; }
        .legal-body a:hover { color: rgb(17 74 140); }
        .legal-body strong { color: rgb(15 23 42); font-weight: 600; }
    </style>

    <article class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-8 shadow-sm sm:px-10 sm:py-12 lg:px-14">
        <div class="legal-body text-sm sm:text-[0.95rem]">
            {!! $page->localized_body !!}
        </div>
    </article>
@endsection
