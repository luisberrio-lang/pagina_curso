<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminEnvironmentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_be_created_from_central_configuration(): void
    {
        $this->configure(password: 'Clave-Segura-123');
        $operator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($operator)
            ->post(route('admin.configuration.sync'), ['confirm_sync' => '1'])
            ->assertRedirect(route('admin.configuration.show'));

        $operator->refresh();
        $this->assertSame('Administración Principal', $operator->name);
        $this->assertSame('principal@example.com', $operator->email);
        $this->assertSame('+51 999 111 222', $operator->phone);
        $this->assertTrue($operator->is_admin);
        $this->assertTrue(Hash::check('Clave-Segura-123', $operator->password));
        $this->assertNotSame('Clave-Segura-123', $operator->password);
    }

    public function test_it_creates_first_admin_when_no_admin_or_matching_user_exists(): void
    {
        $this->configure(password: 'Clave-Creacion-123');

        $user = app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();

        $this->assertTrue($user->is_admin);
        $this->assertSame('principal@example.com', $user->email);
        $this->assertSame('+51 999 111 222', $user->phone);
        $this->assertTrue(Hash::check('Clave-Creacion-123', $user->password));
    }

    public function test_empty_password_preserves_existing_password_while_updating_profile(): void
    {
        $admin = User::factory()->create([
            'name' => 'Nombre anterior',
            'email' => 'anterior@example.com',
            'phone' => null,
            'password' => Hash::make('Clave-Anterior-123'),
            'is_admin' => true,
        ]);
        $originalHash = $admin->password;
        $this->configure(password: null);

        app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();
        $admin->refresh();

        $this->assertSame('Administración Principal', $admin->name);
        $this->assertSame('principal@example.com', $admin->email);
        $this->assertSame('+51 999 111 222', $admin->phone);
        $this->assertSame($originalHash, $admin->password);
        $this->assertTrue($admin->is_admin);
    }

    public function test_configured_password_replaces_hash_only_with_a_secure_hash(): void
    {
        $admin = User::factory()->create(['password' => Hash::make('Anterior-123'), 'is_admin' => true]);
        $this->configure(password: 'Nueva-Segura-123');

        app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();
        $admin->refresh();

        $this->assertTrue(Hash::check('Nueva-Segura-123', $admin->password));
        $this->assertNotSame('Nueva-Segura-123', $admin->password);
    }

    public function test_only_lowest_id_admin_is_synchronized(): void
    {
        $primary = User::factory()->create(['email' => 'first@example.com', 'is_admin' => true]);
        $secondary = User::factory()->create(['email' => 'second@example.com', 'is_admin' => true]);
        $this->configure(password: null);

        app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();

        $this->assertSame('principal@example.com', $primary->fresh()->email);
        $this->assertSame('second@example.com', $secondary->fresh()->email);
    }

    public function test_guest_and_normal_user_cannot_access_or_run_sync(): void
    {
        $this->get(route('admin.configuration.show'))->assertRedirect(route('login'));
        $this->post(route('admin.configuration.sync'), ['confirm_sync' => '1'])->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.configuration.show'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.configuration.sync'), ['confirm_sync' => '1'])->assertForbidden();
    }

    public function test_password_is_never_rendered_and_confirmation_is_required(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->configure(password: 'Secreto-No-Visible-123');

        $this->actingAs($admin)
            ->get(route('admin.configuration.show'))
            ->assertOk()
            ->assertSee('Contraseña configurada')
            ->assertSee('SÍ')
            ->assertDontSee('Secreto-No-Visible-123');

        $this->actingAs($admin)
            ->post(route('admin.configuration.sync'), [])
            ->assertSessionHasErrors('confirm_sync');
    }

    private function configure(?string $password): void
    {
        config()->set('admin', [
            'name' => 'Administración Principal',
            'email' => 'principal@example.com',
            'password' => $password,
            'phone' => '+51 999 111 222',
        ]);
    }
}
