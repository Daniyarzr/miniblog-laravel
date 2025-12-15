<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = ['Анна', 'Иван', 'Мария', 'Дмитрий', 'Ольга', 'Сергей', 'Елена', 'Николай'];
        $users = User::limit(count($names))->get();
        $users->each(function ($user, $i) use ($names) {
            $user->update(['name' => $names[$i % count($names)]]);
        });

        if ($users->count() < count($names)) {
            $missing = collect($names)->slice($users->count());
            $newUsers = $missing->map(function (string $name, int $i) {
                return User::factory()->create([
                    'name' => $name,
                    'email' => "user_new_{$i}@" . Str::random(4) . ".ru",
                ]);
            });
            $users = $users->concat($newUsers);
        }

        $titles = [
            'Как я подружился с Laravel',
            'Bootstrap 5: быстрый старт',
            'SQLite для пет-проектов',
            'Минимализм в дизайне блога',
            'Почему тесты экономят время',
            'API на Laravel за вечер',
            'Как писать понятные миграции',
            'Лучшие практики комментариев',
        ];

        $paragraphs = [
            'Сегодня поделюсь опытом, как за пару вечеров собрать блог на Laravel и Bootstrap.',
            'Используйте готовые классы Bootstrap, чтобы быстро собрать чистый интерфейс без лишней стилизации.',
            'SQLite отлично подходит для прототипов: одна база — один файл, миграции выполняются мгновенно.',
            'Минимализм — это когда на странице только текст, аккуратные кнопки и достаточно воздуха.',
            'Тесты помогают ловить ошибки до продакшена и ускоряют доработки, особенно когда проект растёт.',
            'JSON API на Laravel — это маршруты, ресурсы и немного валидации. Получается чисто и прозрачно.',
            'Хорошие миграции читаемы: понятные имена столбцов, каскады и индексы там, где нужно.',
            'Комментарии с ответами просты: parent_id, ограничить вложенность и добавить отступы.',
        ];

        $posts = Post::all();

        if ($posts->isEmpty()) {
            $posts = collect(range(1, 12))->map(function () use ($users, $titles, $paragraphs) {
                return Post::create([
                    'user_id' => $users->random()->id,
                    'title' => $titles[array_rand($titles)],
                    'content' => $paragraphs[array_rand($paragraphs)] . "\n\n" . $paragraphs[array_rand($paragraphs)],
                ]);
            });
        } else {
            foreach ($posts as $post) {
                $post->update([
                    'title' => $titles[array_rand($titles)],
                    'content' => $paragraphs[array_rand($paragraphs)] . "\n\n" . $paragraphs[array_rand($paragraphs)],
                ]);
            }
        }

        foreach ($posts as $post) {
            // Оставляем по одному комментарию
            Comment::where('post_id', $post->id)->delete();

            Comment::create([
                'user_id' => $users->random()->id,
                'post_id' => $post->id,
                'content' => $paragraphs[array_rand($paragraphs)],
            ]);

            $likeUsers = $users->random(rand(1, $users->count()));
            foreach ($likeUsers as $user) {
                Like::firstOrCreate([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
