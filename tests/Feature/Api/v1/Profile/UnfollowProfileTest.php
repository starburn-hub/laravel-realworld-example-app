<?php

namespace Tests\Feature\Api\v1\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnfollowProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testUnfollowProfile(): void
    {
        /** @var User $author */
        $author = User::factory()->create();

        /** @var User $follower */
        $follower = User::factory()
            ->hasAttached($author, [], 'authors')
            ->create();

        $this->assertTrue($author->followers->contains($follower));

        $response = $this->actingAs($follower, 'api')
            ->deleteJson("/api/v1/profiles/{$author->username}/follow");

        $response->assertOk()
            ->assertJsonPath('profile.following', false);

        $this->assertFalse($follower->authors->contains($author));

        $repeatedResponse = $this->actingAs($follower, 'api')
            ->deleteJson("/api/v1/profiles/{$author->username}/follow");

        $repeatedResponse->assertOk()
            ->assertJsonPath('profile.following', false);
    }

    public function testUnfollowProfileWithoutAuth(): void
    {
        /** @var User $profile */
        $profile = User::factory()->create();

        $response = $this->deleteJson("/api/v1/profiles/{$profile->username}/follow");

        $response->assertUnauthorized();
    }

    public function testUnfollowNonExistentProfile(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/v1/profiles/non-existent/follow');

        $response->assertNotFound();
    }
}