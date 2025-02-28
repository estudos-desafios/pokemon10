<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Pokemon;

class PokemonTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_APIS = '/api/v1/';
    private const BASE_URL_ENDPOINT = self::BASE_APIS . 'pokemons/';

    protected function setUp(): void
    {
        parent::setUp();

        Pokemon::factory()->create([
            'name' => 'bulbasaur',
            'type' => 'grass',
            "height" => 7,
            "weight" => 69,
        ]);

        Pokemon::factory()->create([
            'name' => 'ivysaur',
            'type' => 'grass',
            "height" => 10,
            "weight" => 130,
        ]);
    }

    /** @test */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function api_is_active(): void
    {
        $response = $this->get(self::BASE_APIS);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                    'status' => 'success',
                    'message' => 'Welcome to the API'
                ]);

    }
    
    /** @test */
    public function endpoint_is_ok(): void
    {

        $response = $this->get(self::BASE_URL_ENDPOINT);

        $response->assertStatus(200)
                ->assertJsonIsArray(['pokemons'])
                ->assertJsonStructure([
                    'status' => 'success',
                    'pokemons' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'height',
                            'weight',
                        ]
                    ]
                ]);
    }

    public static function notFoundPokemonProvider(): array
    {
        return [
            'id negativo' => [-1],
            'id zero' => [0],
            'id inexistente' => [99999],
            'pokemon 404' => ['abc'],
        ];
    }

    /**
     * @dataProvider notFoundPokemonProvider
     * @test 
     **/
    public function status_404($pokemonId)
    {
        // $this->markTestSkipped('This test is skipped.');

        $response = $this->get(self::BASE_URL_ENDPOINT . $pokemonId);

        $response->assertStatus(404) 
                 ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Pokemon not found'
            ]);

    }    

    /** @test */
    public function checks_for_valid_id()
    {
        $this->markTestSkipped('This test is skipped.');

        $this->getJson(self::BASE_URL_ENDPOINT . '1')
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'bulbasaur']);
    }

    /** @test */
    public function check_status_and_structure_if_found_by_name()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/bulbasaur')
            ->assertStatus(200)
            ->assertJsonFragment(['type' => 'grass']);
    }

    /** @test */
    public function check_status_if_pokemon_not_exist()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/nada')
            ->assertStatus(404);
    }

    /** @test */
    public function check_status_error_by_search_with_empty_name()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/search?query=')
            ->assertStatus(400);
    }

    /** @test */
    public function check_status_not_found_by_name()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/search?query=Alien')
            ->assertStatus(404);
    }

    /** @test */
    public function check_status_and_valid_structure_by_search()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/search?query=grass')
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'bulbasaur']);
    }

    /** @test */
    public function check_status_structure_and_max_10_itens()
    {
        $this->markTestSkipped('This test is skipped.');
        Pokemon::factory()->count(15)->create();

        $this->getJson('/api/v1/pokemons')
            ->assertStatus(200)
            ->assertJsonStructure(['name', 'height', 'weight'])
            ->assertJsonCount(10, 'data');
    }
    
    /** @test */
    public function check_status_error_if_search_name_is_numeric()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/search?name=123')
            ->assertStatus(400);
    }
    
    /** @test */
    public function check_status_error_when_invalid_type()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/search?type=!@#')
            ->assertStatus(400);
    }
    
    /** @test */
    public function check_status_and_structure_by_search_name_and_type()
    {
        $this->markTestSkipped('This test is skipped.');
        Pokemon::factory()->create([
            'name' => 'Bulbasaur',
            'type' => 'Grass'
        ]);
    
        $this->getJson('/api/v1/pokemons/search?name=Bulbasaur&type=Grass')
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Bulbasaur']);
    }
    
    /** @test */
    public function check_status_and_count_itens_is_correct()
    {
        $this->markTestSkipped('This test is skipped.');
        Pokemon::factory()->count(25)->create();
    
        $this->getJson('/api/v1/pokemons?page=3')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data'); // A última página deve ter apenas 5 itens
    }
    
    /** @test */
    public function check_status_and_structure_json()
    {
        $this->markTestSkipped('This test is skipped.');
        $this->getJson('/api/v1/pokemons/1')
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'type',
                'weight',
                'height',
                'date_updated'
            ]);
    }
    
    /** @test */
    public function can_update_pokemon()
    {
        $this->markTestSkipped('This test is skipped.');
        $pokemon = Pokemon::factory()->create([
            'name' => 'Eevee',
            'type' => 'Normal',
        ]);
    
        $this->putJson("/api/v1/pokemons/{$pokemon->id}", [
            'name' => 'Vaporeon',
            'type' => 'Water',
        ])->assertStatus(200);
    
        $this->assertDatabaseHas('pokemons', [
            'id' => $pokemon->id,
            'name' => 'Vaporeon',
            'type' => 'Water',
        ]);
    }
            
    /** @test */
    public function can_delete_pokemon()
    {
        $this->markTestSkipped('This test is skipped.');
        $pokemon = Pokemon::factory()->create();
    
        $this->deleteJson("/api/pokemons/{$pokemon->id}")
            ->assertStatus(204);
    
        $this->assertDatabaseMissing('pokemons', ['id' => $pokemon->id]);
    }
}
