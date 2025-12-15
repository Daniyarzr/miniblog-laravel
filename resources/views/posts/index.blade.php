<x-app-layout>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Блог</h1>
        @auth
            <a href="{{ route('posts.create') }}" class="btn btn-primary">Новая статья</a>
        @endauth
    </div>

    <form method="GET" action="{{ route('posts.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Поиск по заголовку" value="{{ request('q') }}">
            <button class="btn btn-outline-secondary" type="submit">Искать</button>
        </div>
    </form>

    @forelse ($posts as $post)
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title mb-1">
                        <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                    </h5>
                    <span class="text-muted small">{{ $post->created_at->format('d.m.Y') }}</span>
                </div>
                <p class="text-light mb-3">{{ \Illuminate\Support\Str::limit($post->content, 120) }}</p>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @php $initial = mb_strtoupper(mb_substr($post->author->name, 0, 1)); @endphp
                    @if ($post->author->avatar_url)
                        <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="rounded-circle" width="36" height="36">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center" style="width:36px;height:36px;">
                            {{ $initial }}
                        </div>
                    @endif
                    <span class="fw-semibold">{{ $post->author->name }}</span>
                    <span class="text-comment small d-flex align-items-center gap-1">
                        ❤ {{ $post->likes_count }}
                    </span>
                    <span class="text-comment small">· Комментариев: {{ $post->comments_count ?? 0 }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary">Пока нет статей.</div>
    @endforelse

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</x-app-layout>
