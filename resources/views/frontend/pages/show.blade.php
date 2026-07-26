@extends('site.app.layout')

@section('title', $translation?->title ?? $page->slug)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $translation?->content), 160))

@section('content')
<link rel="stylesheet" href="{{ versioned_asset('css/site/policy-page.css') }}">

<section class="policy-page">
    <div class="container">
        <nav class="policy-breadcrumb" aria-label="breadcrumb">
            <a href="{{ route('home') }}">{{ __('site.home') }}</a>
            <span aria-hidden="true">/</span>
            <span>{{ $translation?->title ?? $page->slug }}</span>
        </nav>

        <article class="policy-card">
            <h1>{{ $translation?->title ?? $page->slug }}</h1>

            @php($blocks = preg_split("/\n\s*\n/", trim((string) $translation?->content)))

            @foreach ($blocks as $block)
                @php($lines = preg_split("/\n/", trim($block)))
                @if (count($lines) > 1 && collect($lines)->skip(1)->every(fn ($line) => str_starts_with(trim($line), '- ')))
                    <h2>{{ $lines[0] }}</h2>
                    <ul>
                        @foreach (array_slice($lines, 1) as $line)
                            <li>{{ ltrim(trim($line), '- ') }}</li>
                        @endforeach
                    </ul>
                @elseif (count($lines) > 1 && mb_strlen($lines[0]) < 70)
                    <h2>{{ $lines[0] }}</h2>
                    <p>{!! nl2br(e(implode("\n", array_slice($lines, 1)))) !!}</p>
                @else
                    <p>{!! nl2br(e($block)) !!}</p>
                @endif
            @endforeach
        </article>
    </div>
</section>
@endsection
