<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_contact()
    {
        $user = User::factory()->create();

        $data = [
            'contact' => [
                'name' => 'João Silva',
                'cpf' => '12345678901',
            ],
            'phones' => [
                ['phone' => '123456789'],
            ],
            'addresses' => [
                [
                    'address' => 'Rua Exemplo',
                    'number' => '123',
                    'cep' => '12345678',
                    'latitude' => -23.550520,
                    'longitude' => -46.633308,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/store-contact', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'name', 'cpf', 'updated_at', 'created_at'
                 ]);
    }

    /** @test */
    public function it_can_update_a_contact()
    {
        $user = User::factory()->create();

        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $data = [
            'contact' => [
                'name' => 'João Silva Atualizado',
                'cpf' => '98765432100',
                'id' => $contact->id
            ],
            'phones' => [
                ['phone' => '987654321'],
            ],
            'addresses' => [
                [
                    'address' => 'Rua Atualizada',
                    'number' => '456',
                    'cep' => '87654321',
                    'latitude' => -23.550520,
                    'longitude' => -46.633308,
                ],
            ],
        ];

        $response = $this->actingAs($user)->putJson("/api/edit-contact", $data);

        $response->assertStatus(200)
                ->assertJson([
                    'name' => 'João Silva Atualizado',
                    'cpf' => '98765432100',
                ]);
    }

    /** @test */
    public function it_can_list_all_contacts()
    {
        $user = User::factory()->create();

        Contact::factory()->create(['user_id' => $user->id]);
        Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/list-contacts');

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data'); 
    }

    /** @test */
    public function it_can_delete_a_contact()
    {
        $user = User::factory()->create();

        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/delete-contacts/{$contact->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Contato deletado com sucesso']);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    /** @test */
    public function it_validates_cpf_uniqueness_per_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $user1->id,
            'cpf' => '123.456.789-00',
        ]);

        $response = $this->actingAs($user1)->postJson('/api/store-contact', [
            'contact' => [
                'name' => 'Duplicate CPF',
                'cpf' => '123.456.789-00',
            ],
            'phones' => [
                ['phone' => '999999999'],
            ],
            'addresses' => [
                [
                    'address' => 'Rua A',
                    'number' => 123,
                    'cep' => '12345-678',
                ],
            ],
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user2)->postJson('/api/store-contact', [
            'contact' => [
                'name' => 'Unique CPF',
                'cpf' => '123.456.789-00',
            ],
            'phones' => [
                ['phone' => '999999999'],
            ],
            'addresses' => [
                [
                    'address' => 'Rua B',
                    'number' => 456,
                    'cep' => '98765-432',
                ],
            ],
        ]);

        $response->assertStatus(201);
    }


}
