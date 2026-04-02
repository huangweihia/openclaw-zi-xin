@extends('layouts.app')

@section('title', $announcement->title . ' - AI 副业情报局')

@section('content')
<section style="padding: 100px 0 60px;">
    <div class="container" style="max-width: 800px;">
        <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 16px;">{{ $announcement->title }}</h1>
        @if($announcement->published_at)
            <p style="color: var(--gray-light); font-size: 14px; margin-bottom: 28px;">
                {{ $announcement->published_at->format('Y-m-d H:i') }}
            </p>
        @endif
        <div class="card rich-html-content" style="padding: 28px; line-height: 1.75;">
            {!! $announcement->body ?: '<p>暂无正文</p>' !!}
        </div>
        <p style="margin-top: 24px;">
            <a href="{{ route('home') }}" class="navbar-link">← 返回首页</a>
        </p>
    </div>
</section>
@endsection
