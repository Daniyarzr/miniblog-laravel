<x-app-layout>
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-2">{{ $post->title }}</h1>
            <div class="d-flex align-items-center gap-2 text-muted small">
                @php $initial = mb_strtoupper(mb_substr($post->author->name, 0, 1)); @endphp
                @if ($post->author->avatar_url)
                    <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="rounded-circle" width="36" height="36">
                @else
                    <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center" style="width:36px;height:36px;">
                        {{ $initial }}
                    </div>
                @endif
                <span class="fw-semibold text-dark">{{ $post->author->name }}</span>
                <span>{{ $post->created_at->format('d.m.Y') }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-secondary btn-sm">Редактировать</a>
                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Удалить статью?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit">Удалить</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3 text-comment small">❤ {{ $post->likes_count }}</div>
            <div class="fs-6 post-content" style="white-space: pre-line;">{{ $post->content }}</div>
            <div class="mt-3">
                @auth
                    <form method="POST" action="{{ route('posts.like', $post) }}">
                        @csrf
                        <button class="btn btn-outline-primary btn-sm" type="submit">
                            {{ $userLike ? 'Убрать лайк' : 'Лайк' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Войти, чтобы лайкать</a>
                @endauth
            </div>
        </div>
    </div>

    <div class="mb-3 d-flex align-items-center justify-content-between">
        <h2 class="h5 mb-0">Комментарии</h2>
        <span class="text-muted">{{ $comments->count() }}</span>
    </div>

    @auth
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('comments.store', $post) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="content">Добавить комментарий</label>
                        <textarea name="content" id="content" rows="3" class="form-control" required>{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" />
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-secondary">Войдите, чтобы оставить комментарий.</div>
    @endauth

    @forelse ($comments as $comment)
        @include('posts.partials.comment', ['comment' => $comment, 'post' => $post, 'level' => 0])
    @empty
        <div class="alert alert-light border">Комментариев пока нет.</div>
    @endforelse
</x-app-layout>
