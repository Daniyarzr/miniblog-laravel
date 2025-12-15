@php $level = $level ?? 0; @endphp
<div class="card border-0 shadow-sm mb-3" style="margin-left: {{ $level * 20 }}px;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-2">
                @php $initial = mb_strtoupper(mb_substr($comment->author->name, 0, 1)); @endphp
                @if ($comment->author->avatar_url)
                    <img src="{{ $comment->author->avatar_url }}" alt="{{ $comment->author->name }}" class="rounded-circle" width="32" height="32">
                @else
                    <div class="rounded-circle bg-secondary text-white d-inline-flex justify-content-center align-items-center" style="width:32px;height:32px;">
                        {{ $initial }}
                    </div>
                @endif
                <div>
                    <div class="fw-semibold">{{ $comment->author->name }}</div>
                    <div class="text-muted small">{{ $comment->created_at->format('d.m.Y H:i') }}</div>
                </div>
            </div>
            @if ($comment->user_id === auth()->id())
                <form method="POST" action="{{ route('comments.destroy', [$post, $comment]) }}" onsubmit="return confirm('Удалить комментарий?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-link text-danger btn-sm" type="submit">Удалить</button>
                </form>
            @endif
        </div>
        <div class="mb-2" style="white-space: pre-line;">{{ $comment->content }}</div>

        @auth
            @if ($level < 2)
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reply-{{ $comment->id }}">Ответить</button>
                <div class="collapse mt-2" id="reply-{{ $comment->id }}">
                    <form method="POST" action="{{ route('comments.store', $post) }}">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <div class="mb-2">
                            <textarea name="content" rows="2" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Отправить</button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</div>

@foreach ($comment->replies as $reply)
    @include('posts.partials.comment', ['comment' => $reply, 'post' => $post, 'level' => $level + 1])
@endforeach
