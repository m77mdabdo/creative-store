<?php

namespace Tests\Feature\Blog;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
    use RefreshDatabase;

    private function blog(array $attributes = []): Blog
    {
        return Blog::create(array_merge([
            'title'        => 'Post',
            'slug'         => 'post-' . uniqid(),
            'content'      => '<p>Body</p>',
            'status'       => true,
            'published_at' => now()->subDay(),
        ], $attributes));
    }

    private function comment(Blog $blog, array $overrides = [])
    {
        return $this->actingAs(User::factory()->create())
            ->post(route('blog.comment.store', $blog->id), array_merge([
                'name'    => 'Reader',
                'email'   => 'reader@example.com',
                'comment' => 'Nice post',
            ], $overrides));
    }

    public function test_a_script_url_is_not_accepted_as_a_website(): void
    {
        $blog = $this->blog();

        $this->comment($blog, ['website' => 'javascript:alert(document.cookie)'])->assertSessionHasErrors('website');

        $this->assertSame(0, BlogComment::count());
    }

    public function test_a_normal_website_is_saved(): void
    {
        $blog = $this->blog();

        $this->comment($blog, ['website' => 'https://example.com'])->assertSessionHasNoErrors();

        $this->assertSame('https://example.com', BlogComment::sole()->website);
    }

    public function test_hidden_posts_do_not_take_comments(): void
    {
        $draft = $this->blog(['status' => false]);

        $this->comment($draft)->assertNotFound();

        $this->assertSame(0, BlogComment::count());
    }
}
