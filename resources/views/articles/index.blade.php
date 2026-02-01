@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-display font-bold text-slate-900 mb-4">Náš Blog</h1>
        <p class="text-lg text-slate-600">Novinky, tipy a rady pre váš biznis aj relax.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($articles as $article)
            <article class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition">
                @if($article->image_path)
                    <img src="{{ asset('storage/' . $article->image_path) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-emerald-50 flex items-center justify-center text-emerald-200">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 3v5h5"/></svg>
                    </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-md uppercase tracking-wider">{{ $article->category ?? 'Blog' }}</span>
                        <span class="text-slate-400 text-xs">{{ $article->published_at->format('d.m.Y') }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-3 leading-tight">
                        <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-emerald-600 transition">{{ $article->title }}</a>
                    </h2>
                    <p class="text-slate-600 text-sm mb-4 line-clamp-3">
                        {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 120) }}
                    </p>
                    <a href="{{ route('articles.show', $article->slug) }}" class="text-emerald-600 text-sm font-semibold hover:underline">Čítať viac →</a>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                <p class="text-slate-500 italic">Zatiaľ sme nepublikovali žiadne články.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $articles->links() }}
    </div>
</div>
@endsection
