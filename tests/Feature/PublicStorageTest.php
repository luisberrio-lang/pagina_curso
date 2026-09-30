<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Course;
use App\Models\CourseImage;
use App\Models\User;
use App\Support\PublicMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class PublicStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storage_blocks_executable_extensions_at_apache_level(): void
    {
        $rules = File::get(public_path('.htaccess'));

        $this->assertStringContainsString(
            'RewriteRule ^storage/.*\.(?:php[0-9]?|phtml|phar|cgi|pl|py|sh)$ - [F,L,NC]',
            $rules,
        );
    }

    public function test_local_public_disk_uses_public_storage_and_generates_public_urls(): void
    {
        $this->assertSame(public_path('storage'), config('filesystems.disks.public.root'));
        $publicDiskUrl = (string) config('filesystems.disks.public.url');
        $this->assertSame('/storage', parse_url($publicDiskUrl, PHP_URL_PATH));
        $this->assertNotFalse(filter_var($publicDiskUrl, FILTER_VALIDATE_URL));

        Storage::fake('public');
        Storage::disk('public')->put('courses/covers/historical.webp', 'image');

        $this->assertSame(
            '/storage/courses/covers/historical.webp',
            PublicMedia::url('courses/covers/historical.webp'),
        );
    }

    public function test_missing_or_unsafe_media_paths_fail_closed(): void
    {
        Storage::fake('public');

        foreach ([null, '', '/absolute.webp', '../secret', 'courses/../secret', 'courses\\secret.webp', 'courses//secret.webp'] as $path) {
            $this->assertNull(PublicMedia::url($path));
        }

        $course = $this->course(['cover_path' => 'courses/covers/missing.webp']);
        $this->assertNull($course->coverUrl());
        $this->get(route('courses.show', $course))->assertOk()->assertDontSee('missing.webp');
    }

    public function test_cover_upload_is_webp_with_server_generated_name_and_public_url(): void
    {
        Storage::fake('public');
        $course = $this->course();

        $this->actingAs($this->admin())->put(route('admin.courses.update', $course), [
            'area_id' => $course->area_id,
            'title' => $course->title,
            'price_anual' => '49.90',
            'is_published' => '1',
            'cover' => UploadedFile::fake()->image('nombre extraño.jpg', 1200, 800),
        ])->assertRedirect();

        $course->refresh();
        $this->assertMatchesRegularExpression('#^courses/covers/[A-Za-z0-9]{40}\.webp$#', $course->cover_path);
        Storage::disk('public')->assertExists($course->cover_path);
        $this->assertStringEndsWith('/storage/'.$course->cover_path, $course->coverUrl());

        $imageInfo = getimagesize(Storage::disk('public')->path($course->cover_path));
        $this->assertSame('image/webp', $imageInfo['mime']);
        $this->assertSame(900, $imageInfo[0]);
        $this->assertSame(500, $imageInfo[1]);
    }

    public function test_dangerous_invalid_and_oversized_uploads_are_rejected(): void
    {
        Storage::fake('public');
        $course = $this->course();
        $base = ['area_id' => $course->area_id, 'title' => $course->title, 'price_anual' => '10.00'];

        foreach ([
            UploadedFile::fake()->create('shell.php', 1, 'application/x-php'),
            UploadedFile::fake()->create('vector.svg', 1, 'image/svg+xml'),
            UploadedFile::fake()->create('page.html', 1, 'text/html'),
            UploadedFile::fake()->image('large.jpg')->size(4097),
        ] as $file) {
            $this->actingAs($this->admin())
                ->put(route('admin.courses.update', $course), array_merge($base, ['cover' => $file]))
                ->assertSessionHasErrors('cover');
        }

        Storage::disk('public')->assertDirectoryEmpty('courses/covers');
    }

    public function test_multiple_samples_can_be_uploaded_ordered_displayed_and_deleted(): void
    {
        Storage::fake('public');
        $course = $this->course();

        $this->actingAs($this->admin())->post(route('admin.courses.images.store', $course), [
            'images' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.png'),
            ],
        ])->assertRedirect();

        $images = $course->images()->get();
        $this->assertCount(2, $images);
        $this->assertSame([1, 2], $images->pluck('sort_order')->all());
        foreach ($images as $image) {
            Storage::disk('public')->assertExists($image->path);
            $this->assertNotNull($image->url());
        }

        $this->actingAs($this->admin())
            ->post(route('admin.courses.images.up', [$course, $images[1]]))
            ->assertRedirect();
        $this->assertSame(1, $images[1]->fresh()->sort_order);

        $this->get(route('courses.show', $course))->assertOk()->assertSee($images[0]->url(), false);
        $this->actingAs($this->admin())
            ->delete(route('admin.courses.images.destroy', [$course, $images[0]]))
            ->assertRedirect();
        Storage::disk('public')->assertMissing($images[0]->path);
    }

    public function test_sample_files_are_cleaned_when_database_work_rolls_back(): void
    {
        Storage::fake('public');
        $this->withoutExceptionHandling();
        $course = $this->course();
        $attempt = 0;
        CourseImage::creating(function () use (&$attempt): void {
            if (++$attempt === 2) {
                throw new RuntimeException('controlled rollback');
            }
        });

        try {
            $this->actingAs($this->admin())->post(route('admin.courses.images.store', $course), [
                'images' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
            ]);
            $this->fail('The controlled rollback exception was not thrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame('controlled rollback', $exception->getMessage());
        }

        $this->assertDatabaseCount('course_images', 0);
        Storage::disk('public')->assertDirectoryEmpty('courses/samples');
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function course(array $attributes = []): Course
    {
        $area = Area::create([
            'name' => 'Storage',
            'slug' => 'storage-'.str()->lower(str()->random(8)),
            'sort_order' => 0,
            'is_default' => true,
        ]);

        return Course::create(array_merge([
            'area_id' => $area->id,
            'title' => 'Curso storage',
            'slug' => 'curso-storage-'.str()->lower(str()->random(8)),
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'price_anual' => '10.00',
        ], $attributes));
    }
}
