<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommentController;
use Database\Factories\Domain\Information\Models\ArticleFactory;
use Database\Factories\Domain\User\Models\CommentFactory;
use Database\Factories\Domain\User\Models\UserFactory;
use Domain\Information\Models\Article;
use Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return UserFactory::new()
            ->createOne();
    }

    private function createArticle(): Article
    {
        return ArticleFactory::new()
            ->createOne();
    }

    private function getToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    private function authorize(User $user): array
    {
        return [
            'Authenticated' => 'Bearer' . $this->getToken($user)
        ];
    }

    private function request(Article $article, ?array $data = null): array
    {
        return [
            'comment' => fake()->text(60),
            'article_id' => $article->getKey(),
            'user_id' => auth()->id(),
        ];
    }

    public function test_get_all_comments_for_article_success(): void
    {
        $user = $this->createUser();

        $article = $this->createArticle();

        CommentFactory::new()->count(5)->create([
            'comment' => fake()->text(),
            'article_id' => $article->getKey(),
            'user_id' => $user->getKey()
        ]);

        $this->post(action([CommentController::class, 'getCommentsOnArticle'], $article->getKey()))
            ->assertOk();
    }

    public function test_it_comment_success_create(): void
    {
        $user = $this->createUser();

        $article = $this->createArticle();

        Sanctum::actingAs($user);

        $request = $this->request($article);

        $this->post(action([CommentController::class, 'store']), $request, $this->authorize($user))
            ->assertOk()
            ->assertJson([
                'status' => 201,
                'message' => 'Комментарий успешно добавлен(a)'
            ]);

        $this->assertDatabaseHas('comments', [
            'comment' => $request['comment'],
            'article_id' => $article->getKey(),
            'user_id' => auth()->id(),
        ]);
    }

    public function test_it_can_user_success_update(): void
    {
        $user = $this->createUser();

        $comment = CommentFactory::new()
            ->createOne(['user_id' => $user->getKey()]);

        Sanctum::actingAs($user);

        $request = [
            'comment' => 'Test',
        ];

        $this->put(action([CommentController::class, 'update'], $comment->getKey()), $request, $this->authorize($user))
            ->assertOk()
            ->assertJson([
                'status' => 200,
                'message' => 'Комментарий успешно обновлен(а)'
            ]);

        $this->assertDatabaseHas('comments', [
            'comment' => $request['comment']
        ]);
    }

    public function test_it_can_user_success_delete(): void
    {
        $user = $this->createUser();

        $comment = CommentFactory::new()
            ->createOne([
                'comment' => 'Test',
                'user_id' => $user->getKey()
            ]);

        Sanctum::actingAs($user);

        $this->delete(action([CommentController::class, 'destroy'], $comment->getKey()), $this->authorize($user))
            ->assertOk()
            ->assertJson([
                'status' => 200,
                'message' => 'Комментарий успешно удален(а)'
            ]);

        $this->assertDatabaseMissing('comments', [
            'comment' => 'Test',
            'user_id' => $user->getKey()
        ]);
    }

    public function test_it_can_reply_to_comment_success(): void
    {
        $user = $this->createUser();

        $comment = CommentFactory::new()
            ->createOne();

        Sanctum::actingAs($user);

        $request = [
            'comment' => fake()->text(60),
            'article_id' => null,
            'user_id' => auth()->id(),
            'parent_id' => $comment->getKey()
        ];

        $this->post(action([CommentController::class, 'store']), $request, $this->authorize($user))
            ->assertOk()
            ->assertJson([
                'status' => 201,
                'message' => 'Комментарий успешно добавлен(a)'
            ]);

        $this->assertEquals(2, $comment->count());

        $this->assertEquals(1, $comment->replies()->count());

        $reply = $comment->replies()->first();

        $this->assertEquals($request['comment'], $reply->comment);
    }
}
