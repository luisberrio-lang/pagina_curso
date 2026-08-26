<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
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

        $passwordHash = $admin->password;
        $this->actingAs($admin)
            ->get(route('admin.configuration.show'))
            ->assertOk()
            ->assertSee('Contraseña configurada')
            ->assertSee('SÍ')
            ->assertSee('name="_token"', false)
            ->assertDontSee('Secreto-No-Visible-123')
            ->assertDontSee($passwordHash);

        $this->actingAs($admin)
            ->post(route('admin.configuration.sync'), [])
            ->assertSessionHasErrors('confirm_sync');
    }

    public function test_configuration_values_are_read_from_the_central_config_and_password_may_be_empty(): void
    {
        $this->configure(password: null);

        $this->assertSame('Administración Principal', config('admin.name'));
        $this->assertSame('principal@example.com', config('admin.email'));
        $this->assertSame('+51 999 111 222', config('admin.phone'));
        $this->assertNull(config('admin.password'));
    }

    public function test_sync_command_uses_the_service_and_never_prints_the_password_or_hash(): void
    {
        $this->configure(password: 'Clave-Comando-123');

        $exitCode = Artisan::call('admin:sync-env');
        $output = Artisan::output();
        $admin = User::query()->where('is_admin', true)->firstOrFail();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Administrador sincronizado correctamente', $output);
        $this->assertStringContainsString('Contraseña sincronizada', $output);
        $this->assertStringNotContainsString('Clave-Comando-123', $output);
        $this->assertStringNotContainsString($admin->password, $output);
        $this->assertTrue(Hash::check('Clave-Comando-123', $admin->password));
    }

    public function test_repeated_sync_does_not_duplicate_the_administrator(): void
    {
        $this->configure(password: 'Clave-Segura-123');

        app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();
        app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame(1, User::query()->where('is_admin', true)->count());
    }

    public function test_synchronized_admin_can_login_and_access_dashboard(): void
    {
        $this->configure(password: 'Correoprueba123');
        config()->set('admin.email', 'correoprueba@gmail.com');
        app(\App\Services\AdminEnvironmentSynchronizer::class)->sync();

        $this->post(route('login'), [
            'email' => 'correoprueba@gmail.com',
            'password' => 'Correoprueba123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();

        auth()->logout();
        $this->post(route('login'), [
            'email' => 'correoprueba@gmail.com',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('email');
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
