<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and set a token for authentication
        $this->user = User::factory()->create();

        $this->token = $this->user->token;

        // Set the authorization header for the API requests
        $this->withHeaders(['Authorization' => 'Bearer ' . $this->token]);
    }
    

    public function test_can_get_all_places()
    {
        // Create places associated with the user
        Place::factory()->count(5)->create(['user_id' => $this->user->id]);

        // Make a GET request to the API endpoint
        $response = $this->getJson('/api/places');

        // Assert the response status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id', 'title', 'description', 'address', 'address_link',
                             'image_placeholder', 'image_gallery', 'user_id'
                         ]
                     ]
                 ]);
    }

    public function test_can_create_place()
    {
        $placeData = [
            'title' => 'Example Title',
            'description' => 'Example Description',
            'address' => 'Example Address',
            'address_link' => 'http://example.com',
            'image_placeholder' => 'http://example.com/image.png',
            'image_gallery' => json_encode(['http://example.com/image1.png', 'http://example.com/image2.png']),
        ];

        $response = $this->postJson('/api/places', $placeData);

        // Assert the response status and structure
        $response->assertStatus(201)
                 ->assertJson(['data' => ['title' => $placeData['title']]]);

        // Assert that the place is stored in the database
        $this->assertDatabaseHas('places', $placeData);
    }


    public function test_can_show_place()
    {
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/places/{$place->id}");

        $response->assertStatus(200)
                 ->assertJson(['data' => ['id' => $place->id]]);
    }

    public function test_can_update_place()
    {
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $updateData = ['description' => 'Updated description'];

        $response = $this->patchJson("/api/places/{$place->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson(['data' => ['description' => $updateData['description']]]);

        $this->assertDatabaseHas('places', ['id' => $place->id, 'description' => $updateData['description']]);
    }

    public function test_can_delete_place()
    {
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/places/{$place->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('places', ['id' => $place->id]);
    }

    public function test_cannot_update_others_place()
    {
        $otherUser = User::factory()->create();
        $place = Place::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->patchJson("/api/places/{$place->id}", ['description' => 'Unauthorized update']);

        $response->assertStatus(403);
    }


    public function test_cannot_delete_others_place()
    {
        $otherUser = User::factory()->create();
        $place = Place::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/places/{$place->id}");

        $response->assertStatus(403);
    }

}
