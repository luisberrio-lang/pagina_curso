<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\LegacyMediaMigrationService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminLegacyMediaMigrationTest extends TestCase
{
    use RefreshDatabase;

    private string $sourceRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sourceRoot = storage_path('framework/testing/legacy-media-'.Str::uuid());
        File::makeDirectory($this->sourceRoot.'/covers', 0755, true);
        File::makeDirectory($this->sourceRoot.'/samples', 0755, true);
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->sourceRoot);

        parent::tearDown();
    }

    public function test_missing_file_is_copied_without_deleting_source(): void
    {
        $contents = $this->imageContents('legacy.webp');
        File::put($this->sourceRoot.'/covers/legacy.webp', $contents);

        $result = $this->migration()->migrate();

        $this->assertSame([
            'found' => 1,
            'copied' => 1,
            'skipped' => 0,
            'conflicts' => 0,
            'errors' => 0,
        ], $result);
        Storage::disk('public')->assertExists('courses/covers/legacy.webp');
        $this->assertSame($contents, Storage::disk('public')->get('courses/covers/legacy.webp'));
        $this->assertFileExists($this->sourceRoot.'/covers/legacy.webp');
    }

    public function test_identical_file_is_omitted_and_repeated_execution_is_safe(): void
    {
        $contents = $this->imageContents('repeated.jpg');
        File::put($this->sourceRoot.'/samples/repeated.jpg', $contents);

        $first = $this->migration()->migrate();
        $second = $this->migration()->migrate();

        $this->assertSame(1, $first['copied']);
        $this->assertSame(1, $second['skipped']);
        $this->assertSame(0, $second['copied']);
        $this->assertSame(0, $second['conflicts']);
        $this->assertSame($contents, Storage::disk('public')->get('courses/samples/repeated.jpg'));
        $this->assertFileExists($this->sourceRoot.'/samples/repeated.jpg');
    }

    public function test_different_destination_is_a_conflict_and_is_not_overwritten(): void
    {
        $sourceContents = $this->imageContents('conflict.png');
        File::put($this->sourceRoot.'/covers/conflict.png', $sourceContents);
        Storage::disk('public')->put('courses/covers/conflict.png', 'destination-version');

        $result = $this->migration()->migrate();

        $this->assertSame(1, $result['conflicts']);
        $this->assertSame(0, $result['copied']);
        $this->assertSame('destination-version', Storage::disk('public')->get('courses/covers/conflict.png'));
        $this->assertSame($sourceContents, File::get($this->sourceRoot.'/covers/conflict.png'));
    }

    public function test_dangerous_extensions_are_rejected_and_never_copied(): void
    {
        foreach (['shell.php', 'page.html', 'vector.svg', 'code.js', 'script.phtml', 'renamed.jpg'] as $name) {
            File::put($this->sourceRoot.'/covers/'.$name, 'unsafe');
        }

        $result = $this->migration()->migrate();

        $this->assertSame(6, $result['found']);
        $this->assertSame(6, $result['errors']);
        $this->assertSame(0, $result['copied']);
        Storage::disk('public')->assertDirectoryEmpty('courses');
    }

    public function test_only_internal_cover_and_sample_paths_without_traversal_are_allowed(): void
    {
        $migration = $this->migration();

        $this->assertTrue($migration->isAllowedRelativePath('covers/photo.JPG'));
        $this->assertTrue($migration->isAllowedRelativePath('samples/photo.webp'));
        $this->assertFalse($migration->isAllowedRelativePath('../covers/photo.jpg'));
        $this->assertFalse($migration->isAllowedRelativePath('covers/../photo.jpg'));
        $this->assertFalse($migration->isAllowedRelativePath('other/photo.jpg'));
        $this->assertFalse($migration->isAllowedRelativePath('covers/nested/photo.jpg'));
        $this->assertFalse($migration->isAllowedRelativePath('covers/photo.php'));
    }

    public function test_only_an_admin_can_execute_the_action(): void
    {
        $payload = ['confirm_media_migration' => '1'];

        $this->post(route('admin.configuration.migrate-media'), $payload)
            ->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)
            ->post(route('admin.configuration.migrate-media'), $payload)
            ->assertForbidden();
    }

    public function test_admin_must_confirm_and_sees_only_safe_counters(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $result = ['found' => 4, 'copied' => 1, 'skipped' => 1, 'conflicts' => 1, 'errors' => 1];
        $this->mock(LegacyMediaMigrationService::class)
            ->shouldReceive('migrate')
            ->once()
            ->andReturn($result);

        $this->actingAs($admin)
            ->post(route('admin.configuration.migrate-media'))
            ->assertSessionHasErrors('confirm_media_migration');

        $this->actingAs($admin)
            ->post(route('admin.configuration.migrate-media'), ['confirm_media_migration' => '1'])
            ->assertRedirect(route('admin.configuration.show'))
            ->assertSessionHas('media_migration', $result);

        $this->actingAs($admin)
            ->get(route('admin.configuration.show'))
            ->assertOk()
            ->assertSee('Migrar imágenes históricas')
            ->assertSee('Ejecutar migración de imágenes')
            ->assertSeeInOrder(['Encontrados', '4', 'Copiados', '1', 'Omitidos', '1', 'Conflictos', '1', 'Errores', '1'])
            ->assertDontSee(storage_path(), false);
    }

    public function test_action_is_protected_by_csrf(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $route = app('router')->getRoutes()->getByName('admin.configuration.migrate-media');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->gatherMiddleware());
        $this->actingAs($admin)
            ->get(route('admin.configuration.show'))
            ->assertOk()
            ->assertSee('action="'.route('admin.configuration.migrate-media').'"', false)
            ->assertSee('name="_token"', false);
    }

    private function migration(): LegacyMediaMigrationService
    {
        return new LegacyMediaMigrationService(app(Filesystem::class), $this->sourceRoot);
    }

    private function imageContents(string $name): string
    {
        $file = UploadedFile::fake()->image($name, 20, 20);
        $contents = file_get_contents($file->getRealPath());

        $this->assertIsString($contents);

        return $contents;
    }
}
