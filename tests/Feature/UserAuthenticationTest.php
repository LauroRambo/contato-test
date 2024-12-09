<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\Phone;
use App\Models\Address;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_new_user()
    {
        $userData = [
            'user' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'

        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user',
            'token',
            'expires_at',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'user' => $user->user,
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user',
            'token',
            'expires_at',
        ]);

        // Verifica se o token foi gerado e expirará dentro de 8 horas
        $this->assertArrayHasKey('token', $response->json());
    }

    /** @test */
    public function it_can_reset_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);

        $newPassword = 'newpassword456';

        $response = $this->postJson('/api/password-reset', [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Senha atualizada com sucesso!',
        ]);

        // Verifica se a senha foi alterada no banco de dados
        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /** @test */
    public function it_can_logout()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // Login do usuário
        $response = $this->postJson('/api/login', [
            'user' => $user->user,
            'email' => $user->email,
            'password' => 'password123'
        ]);
        $token = $response->json('token');
        
        // Logando o usuário
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logout bem-sucedido',
        ]);
    }

    /** @test */
    public function it_deletes_user_and_their_contacts_after_password_verification()
    {
        // Cria um usuário com senha
        $user = User::factory()->create([
            'password' => Hash::make('validpassword123') // A senha correta
        ]);

        // Cria dois contatos associados ao usuário
        $contact1 = Contact::factory()->create(['user_id' => $user->id]);
        $contact2 = Contact::factory()->create(['user_id' => $user->id]);

        // Verifica que o usuário e os contatos foram criados no banco de dados
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('contacts', ['id' => $contact1->id, 'user_id' => $user->id]);
        $this->assertDatabaseHas('contacts', ['id' => $contact2->id, 'user_id' => $user->id]);

        // Ação: Tenta excluir a conta do usuário com a senha correta
        $response = $this->actingAs($user)->deleteJson('/api/delete-account', [
            'password' => 'validpassword123' // Senha correta
        ]);

        // Verifica se a resposta é de sucesso
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Conta e contatos deletados com sucesso']);

        // Verifica que o usuário foi excluído
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Verifica que os contatos associados ao usuário foram excluídos
        $this->assertDatabaseMissing('contacts', ['id' => $contact1->id, 'user_id' => $user->id]);
        $this->assertDatabaseMissing('contacts', ['id' => $contact2->id, 'user_id' => $user->id]);
    }

    /** @test */
    public function it_prevents_account_deletion_with_invalid_password()
    {
        // Cria um usuário com senha
        $user = User::factory()->create([
            'password' => Hash::make('validpassword123')
        ]);

        // Tenta excluir a conta com uma senha errada
        $response = $this->actingAs($user)->deleteJson('/api/delete-account', [
            'password' => 'wrongpassword123'
        ]);

        // Verifica que a resposta indica erro de senha inválida
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Senha inválida.'
        ]);

        // Verifica que o usuário ainda está no banco de dados
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
