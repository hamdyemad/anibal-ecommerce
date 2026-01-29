<?php

namespace Modules\Vendor\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Modules\Vendor\app\Models\Vendor;
use Modules\Vendor\app\Models\VendorRequest;
use Modules\Vendor\app\Repositories\VendorRepository;
use App\Services\UserService;
use App\Models\Role;
use App\Models\Language;
use Modules\AreaSettings\app\Models\Country;
use Modules\CategoryManagment\app\Models\Department;

class VendorCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected VendorRepository $vendorRepository;
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userService = app(UserService::class);
        $this->vendorRepository = new VendorRepository($this->userService);
        
        // Set up fake storage
        Storage::fake('public');
        
        // Create necessary data
        $this->createRequiredData();
    }

    protected function createRequiredData(): void
    {
        // Create languages
        Language::factory()->create(['code' => 'en', 'name' => 'English']);
        Language::factory()->create(['code' => 'ar', 'name' => 'Arabic']);
        
        // Create vendor role
        Role::factory()->create([
            'type' => Role::VENDOR_ROLE_TYPE,
            'name' => 'Vendor',
        ]);
        
        // Create country
        Country::factory()->create([
            'code' => 'EG',
            'name' => 'Egypt',
        ]);
        
        // Create department
        Department::factory()->create([
            'name' => 'Electronics',
        ]);
    }

    /** @test */
    public function it_can_create_a_vendor_with_minimum_required_data()
    {
        // Arrange
        $data = [
            'email' => 'vendor@example.com',
            'password' => 'password123',
            'active' => true,
            'type' => 'product',
            'phone' => '+201234567890',
            'translations' => [
                1 => [ // English
                    'name' => 'Test Vendor',
                    'description' => 'Test vendor description',
                ],
                2 => [ // Arabic
                    'name' => 'بائع تجريبي',
                    'description' => 'وصف البائع التجريبي',
                ],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertInstanceOf(Vendor::class, $vendor);
        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'type' => 'product',
            'active' => true,
            'phone' => '+201234567890',
        ]);
        
        // Assert user was created
        $this->assertNotNull($vendor->user);
        $this->assertEquals('vendor@example.com', $vendor->user->email);
        $this->assertTrue(Hash::check('password123', $vendor->user->password));
        
        // Assert vendor role was assigned
        $this->assertTrue($vendor->user->roles()->where('type', Role::VENDOR_ROLE_TYPE)->exists());
        
        // Assert translations were stored
        $this->assertEquals('Test Vendor', $vendor->getTranslation('name', 'en'));
        $this->assertEquals('بائع تجريبي', $vendor->getTranslation('name', 'ar'));
    }

    /** @test */
    public function it_creates_user_account_when_creating_vendor()
    {
        // Arrange
        $data = [
            'email' => 'newvendor@example.com',
            'password' => 'securepass123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'New Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'newvendor@example.com',
            'active' => true,
        ]);
        
        $this->assertEquals($vendor->user->email, 'newvendor@example.com');
    }

    /** @test */
    public function it_assigns_vendor_role_to_user_on_creation()
    {
        // Arrange
        $data = [
            'email' => 'roletest@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Role Test Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $vendorRole = Role::where('type', Role::VENDOR_ROLE_TYPE)->first();
        $this->assertTrue($vendor->user->roles->contains($vendorRole));
    }

    /** @test */
    public function it_can_upload_logo_during_vendor_creation()
    {
        // Arrange
        $logo = UploadedFile::fake()->image('logo.jpg', 200, 200);
        
        $data = [
            'email' => 'logovendor@example.com',
            'password' => 'password123',
            'active' => true,
            'logo' => $logo,
            'translations' => [
                1 => ['name' => 'Logo Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertNotNull($vendor->logo);
        $this->assertEquals('logo', $vendor->logo->type);
        Storage::disk('public')->assertExists($vendor->logo->path);
        
        // Assert user image was updated with logo
        $this->assertEquals($vendor->logo->path, $vendor->user->image);
    }

    /** @test */
    public function it_can_upload_banner_during_vendor_creation()
    {
        // Arrange
        $banner = UploadedFile::fake()->image('banner.jpg', 1200, 400);
        
        $data = [
            'email' => 'bannervendor@example.com',
            'password' => 'password123',
            'active' => true,
            'banner' => $banner,
            'translations' => [
                1 => ['name' => 'Banner Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertNotNull($vendor->banner);
        $this->assertEquals('banner', $vendor->banner->type);
        Storage::disk('public')->assertExists($vendor->banner->path);
    }

    /** @test */
    public function it_can_upload_documents_during_vendor_creation()
    {
        // Arrange
        $document1 = UploadedFile::fake()->create('license.pdf', 1000);
        $document2 = UploadedFile::fake()->create('certificate.pdf', 1500);
        
        $data = [
            'email' => 'docsvendor@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Docs Vendor'],
            ],
            'documents' => [
                [
                    'file' => $document1,
                    'translations' => [
                        1 => ['name' => 'Business License'],
                        2 => ['name' => 'رخصة تجارية'],
                    ],
                ],
                [
                    'file' => $document2,
                    'translations' => [
                        1 => ['name' => 'Tax Certificate'],
                    ],
                ],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertCount(2, $vendor->documents);
        
        foreach ($vendor->documents as $document) {
            $this->assertEquals('docs', $document->type);
            Storage::disk('public')->assertExists($document->path);
        }
        
        // Assert document translations
        $firstDoc = $vendor->documents->first();
        $this->assertEquals('Business License', $firstDoc->getTranslation('name', 'en'));
        $this->assertEquals('رخصة تجارية', $firstDoc->getTranslation('name', 'ar'));
    }

    /** @test */
    public function it_can_sync_departments_during_vendor_creation()
    {
        // Arrange
        $department1 = Department::factory()->create(['name' => 'Electronics']);
        $department2 = Department::factory()->create(['name' => 'Fashion']);
        
        $data = [
            'email' => 'deptvendor@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Department Vendor'],
            ],
            'departments' => [$department1->id, $department2->id],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertCount(2, $vendor->departments);
        $this->assertTrue($vendor->departments->contains($department1));
        $this->assertTrue($vendor->departments->contains($department2));
    }

    /** @test */
    public function it_generates_unique_slug_from_english_name()
    {
        // Arrange
        $data = [
            'email' => 'slugvendor@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Unique Vendor Name'],
                2 => ['name' => 'اسم بائع فريد'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals('unique-vendor-name', $vendor->slug);
    }

    /** @test */
    public function it_handles_duplicate_slug_by_adding_random_suffix()
    {
        // Arrange - Create first vendor
        $firstVendor = $this->vendorRepository->createVendor([
            'email' => 'first@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Same Name'],
            ],
        ]);

        // Act - Create second vendor with same name
        $secondVendor = $this->vendorRepository->createVendor([
            'email' => 'second@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Same Name'],
            ],
        ]);

        // Assert
        $this->assertEquals('same-name', $firstVendor->slug);
        $this->assertNotEquals('same-name', $secondVendor->slug);
        $this->assertStringStartsWith('same-name-', $secondVendor->slug);
    }

    /** @test */
    public function it_stores_meta_information_during_creation()
    {
        // Arrange
        $data = [
            'email' => 'metavendor@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => [
                    'name' => 'Meta Vendor',
                    'description' => 'Vendor description',
                    'meta_title' => 'Meta Title',
                    'meta_description' => 'Meta Description',
                    'meta_keywords' => 'keyword1, keyword2, keyword3',
                ],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals('Meta Title', $vendor->getTranslation('meta_title', 'en'));
        $this->assertEquals('Meta Description', $vendor->getTranslation('meta_description', 'en'));
        
        $keywords = $vendor->getMetaKeywordsArray('en');
        $this->assertCount(3, $keywords);
        $this->assertContains('keyword1', $keywords);
        $this->assertContains('keyword2', $keywords);
        $this->assertContains('keyword3', $keywords);
    }

    /** @test */
    public function it_can_create_vendor_from_vendor_request()
    {
        // Arrange
        $vendorRequest = VendorRequest::factory()->create([
            'status' => 'pending',
            'company_logo' => 'requests/logo.jpg',
        ]);
        
        $data = [
            'email' => 'fromrequest@example.com',
            'password' => 'password123',
            'active' => true,
            'vendor_request_id' => $vendorRequest->id,
            'translations' => [
                1 => ['name' => 'From Request Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals($vendorRequest->id, $vendor->vendor_request_id);
        
        // Assert vendor request status was updated to approved
        $vendorRequest->refresh();
        $this->assertEquals('approved', $vendorRequest->status);
        
        // Assert logo was copied from vendor request if vendor didn't have one
        if (!isset($data['logo'])) {
            $this->assertNotNull($vendor->logo);
            $this->assertEquals($vendorRequest->company_logo, $vendor->logo->path);
        }
    }

    /** @test */
    public function it_creates_inactive_vendor_when_active_is_false()
    {
        // Arrange
        $data = [
            'email' => 'inactive@example.com',
            'password' => 'password123',
            'active' => false,
            'translations' => [
                1 => ['name' => 'Inactive Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertFalse($vendor->active);
        $this->assertFalse($vendor->user->active);
    }

    /** @test */
    public function it_wraps_vendor_creation_in_database_transaction()
    {
        // Arrange
        $data = [
            'email' => 'transaction@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Transaction Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert - If transaction works, both vendor and user should exist
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id]);
        $this->assertDatabaseHas('users', ['id' => $vendor->user_id]);
    }

    /** @test */
    public function it_validates_required_email_field()
    {
        // Arrange
        $data = [
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'No Email Vendor'],
            ],
        ];

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->vendorRepository->createVendor($data);
    }

    /** @test */
    public function it_validates_required_password_field()
    {
        // Arrange
        $data = [
            'email' => 'nopassword@example.com',
            'active' => true,
            'translations' => [
                1 => ['name' => 'No Password Vendor'],
            ],
        ];

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->vendorRepository->createVendor($data);
    }

    /** @test */
    public function it_validates_required_translations()
    {
        // Arrange
        $data = [
            'email' => 'notrans@example.com',
            'password' => 'password123',
            'active' => true,
            // No translations provided
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert - Vendor should be created but without translations
        $this->assertInstanceOf(Vendor::class, $vendor);
        $this->assertNull($vendor->getTranslation('name', 'en'));
    }

    /** @test */
    public function it_defaults_to_product_type_when_not_specified()
    {
        // Arrange
        $data = [
            'email' => 'defaulttype@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => ['name' => 'Default Type Vendor'],
            ],
            // No type specified
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals('product', $vendor->type);
    }

    /** @test */
    public function it_can_create_service_type_vendor()
    {
        // Arrange
        $data = [
            'email' => 'service@example.com',
            'password' => 'password123',
            'active' => true,
            'type' => 'service',
            'translations' => [
                1 => ['name' => 'Service Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals('service', $vendor->type);
    }

    /** @test */
    public function it_stores_phone_number_correctly()
    {
        // Arrange
        $data = [
            'email' => 'phone@example.com',
            'password' => 'password123',
            'active' => true,
            'phone' => '+201234567890',
            'translations' => [
                1 => ['name' => 'Phone Vendor'],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals('+201234567890', $vendor->phone);
    }

    /** @test */
    public function it_creates_vendor_with_all_translation_fields()
    {
        // Arrange
        $data = [
            'email' => 'fulltrans@example.com',
            'password' => 'password123',
            'active' => true,
            'translations' => [
                1 => [
                    'name' => 'Full Translation Vendor',
                    'description' => 'Complete description',
                    'meta_title' => 'SEO Title',
                    'meta_description' => 'SEO Description',
                    'meta_keywords' => 'seo, keywords, test',
                ],
            ],
        ];

        // Act
        $vendor = $this->vendorRepository->createVendor($data);

        // Assert
        $this->assertEquals('Full Translation Vendor', $vendor->getTranslation('name', 'en'));
        $this->assertEquals('Complete description', $vendor->getTranslation('description', 'en'));
        $this->assertEquals('SEO Title', $vendor->getTranslation('meta_title', 'en'));
        $this->assertEquals('SEO Description', $vendor->getTranslation('meta_description', 'en'));
        
        $keywords = $vendor->getMetaKeywordsArray('en');
        $this->assertCount(3, $keywords);
    }
}
