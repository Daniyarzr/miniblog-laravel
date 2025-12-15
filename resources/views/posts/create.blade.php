<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Новая статья</h1>
        <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary btn-sm">Назад</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('posts.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="title">Заголовок</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    <x-input-error :messages="$errors->get('title')" />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="content">Содержимое</label>
                    <textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                    <x-input-error :messages="$errors->get('content')" />
                </div>
                <button type="submit" class="btn btn-primary">Создать</button>
            </form>
        </div>
    </div>
</x-app-layout>
