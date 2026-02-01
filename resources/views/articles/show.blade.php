@extends('layouts.app')

@section('content')
<div class="py-12 max-w-3xl mx-auto">
    <a href="{{ route('articles.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-emerald-600 mb-8 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Späť na blog
    </a>

    <header class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-md uppercase tracking-wider">{{ $article->category ?? 'Blog' }}</span>
            <span class="text-slate-400 text-sm">{{ $article->published_at->format('d. M Y') }}</span>
        </div>
        <h1 class="text-4xl font-display font-bold text-slate-900 leading-tight mb-4">{{ $article->title }}</h1>
        @if($article->author)
            <p class="text-slate-500 text-sm italic">Autor: {{ $article->author->name }}</p>
        @endif
    </header>

    @if($article->image_path)
        <div class="mb-10 rounded-3xl overflow-hidden shadow-xl border border-white/60">
            <img src="{{ asset('storage/' . $article->image_path) }}" alt="{{ $article->title }}" class="w-full h-auto">
        </div>
    @endif

    <div class="prose prose-slate prose-lg max-w-none prose-emerald">
        {!! nl2br(e($article->content)) !!}
    </div>

    <footer class="mt-16 pt-8 border-t border-slate-100">
        <div class="bg-emerald-50 rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Chcete zviditeľniť svoju prevádzku?</h3>
                <p class="text-slate-600 text-sm">Zaregistrujte sa na BookMe a získajte viac zákazníkov ešte dnes.</p>
            </div>
            <a href="{{ route('auth.login') }}" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-200/60 hover:bg-emerald-700 transition">Pridať prevádzku</a>
        </div>
    </footer>
</div>
@endsection
