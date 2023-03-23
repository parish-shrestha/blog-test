<?php

namespace Tests\Unit;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $blog = Blog::factory()->create();

        $response = $this->getJson('/api/blogs');
        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'title' => $blog->title,
            'body' => $blog->body,
        ]);
    }

    public function testStore()
    {
        $blogData = [
            'title' => 'Test Blog',
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ];

        $response = $this->postJson('/api/blogs', $blogData);
        $response->assertStatus(201);
        $response->assertJsonFragment($blogData);
    }

    public function testShow()
    {
        $blog = Blog::factory()->create();

        $response = $this->get('/api/blogs/' . $blog->id);

        $response->assertStatus(200)->assertJsonFragment($blog->toArray());
    }

    public function testUpdate()
    {
        $blog = Blog::factory()->create();

        $newTitle = 'New Title';
        $newBody = 'New Body';
        $response = $this->put('/api/blogs/' . $blog->id, [
            'title' => $newTitle,
            'body' => $newBody,
        ]);

        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => $newTitle,
            'body' => $newBody,
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $blog->id,
            'title' => $newTitle,
            'body' => $newBody,
        ]);
    }

    public function testDelete()
    {
        $blog = Blog::factory()->create();

        $response = $this->delete('/api/blogs/' . $blog->id);

        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);

        $response->assertStatus(204);
    }
}
