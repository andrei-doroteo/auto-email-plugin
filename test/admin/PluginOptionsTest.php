<?php declare( strict_types=1 );

/**
 * This file is AI generated.
 *
 * TODO: Do a manual code review on these tests.
 */

use PHPUnit\Framework\TestCase;
use DoroteoDigital\AutoEmail\admin\PluginOptions;
use PHPUnit\Framework\Attributes\DataProvider;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Unit tests for PluginOptions class using Brain Monkey for WordPress function mocking
 *
 * Brain Monkey is compatible with PHPUnit 12 and provides a modern approach to
 * mocking WordPress functions.
 *
 * Tests cover:
 * - Singleton pattern implementation
 * - WordPress options initialization and retrieval
 * - Getting and setting business owner email
 * - Error handling for malformed data
 * - Various email format validations
 */
final class PluginOptionsTest extends TestCase {
	use MockeryPHPUnitIntegration;

	private const string WP_OPTIONS_KEY = 'auto_email__go-divas_options';

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		// Reset the singleton instance using reflection
		$reflectionClass = new \ReflectionClass( PluginOptions::class );
		$instance        = $reflectionClass->getProperty( 'singleton' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function testGetInstanceReturnsSameInstance(): void {
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		// Mock get_option to be called only once since the singleton is cached
		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$instance1 = PluginOptions::getInstance();
		$instance2 = PluginOptions::getInstance();

		$this->assertSame( $instance1, $instance2, 'getInstance should return the same singleton instance' );
	}

	public function testConstructorCreatesOptionsIfNotExist(): void {
		$expected = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		// Mock get_option to return false (option doesn't exist)
		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		// Mock add_option to be called with default values
		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $expected );

		$instance = PluginOptions::getInstance();

		// Verify the instance was created
		$this->assertInstanceOf( PluginOptions::class, $instance );
	}

	public function testConstructorLoadsExistingOptions(): void {
		$existingOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "test@example.com",
			]
		];

		// Mock get_option to return existing options
		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( $existingOptions );

		$instance = PluginOptions::getInstance();

		// Should load existing options
		$this->assertEquals( "test@example.com", $instance->get_business_owner_email() );
	}

	public function testGetBusinessOwnerEmailReturnsEmptyStringWhenNotSet(): void {
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$instance = PluginOptions::getInstance();
		$email    = $instance->get_business_owner_email();

		$this->assertSame( "", $email );
	}

	public function testGetBusinessOwnerEmailReturnsCorrectEmail(): void {
		$options = [
			"automatic_notifs" => [
				"business_owner_email" => "owner@business.com",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( $options );

		$instance = PluginOptions::getInstance();
		$email    = $instance->get_business_owner_email();

		$this->assertSame( "owner@business.com", $email );
	}

	public function testGetBusinessOwnerEmailReturnsEmptyStringOnMalformedData(): void {
		// Test with missing nested key
		$options = [
			"automatic_notifs" => []
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( $options );

		$instance = PluginOptions::getInstance();
		$email    = $instance->get_business_owner_email();

		$this->assertSame( "", $email );
	}

	public function testGetBusinessOwnerEmailReturnsEmptyStringOnMissingParentKey(): void {
		// Test with completely missing automatic_notifs key
		// Note: An empty array [] is falsy in PHP, so add_option will be called
		$options        = [];
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( $options );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$instance = PluginOptions::getInstance();
		$email    = $instance->get_business_owner_email();

		$this->assertSame( "", $email );
	}

	public function testSetBusinessOwnerEmailUpdatesEmail(): void {
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$updatedOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "newemail@example.com",
			]
		];

		Functions\expect( 'update_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $updatedOptions );

		$instance = PluginOptions::getInstance();
		$instance->set_business_owner_email( "newemail@example.com" );

		$this->assertSame( "newemail@example.com", $instance->get_business_owner_email() );
	}

	public function testSetBusinessOwnerEmailSavesToWordPressOptions(): void {
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$updatedOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "saved@example.com",
			]
		];

		// Verify update_option is called with the correct values
		Functions\expect( 'update_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $updatedOptions );

		$instance = PluginOptions::getInstance();
		$instance->set_business_owner_email( "saved@example.com" );
	}

	public function testSetBusinessOwnerEmailOverwritesExistingEmail(): void {
		$existingOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "old@example.com",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( $existingOptions );

		$updatedOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "new@example.com",
			]
		];

		Functions\expect( 'update_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $updatedOptions );

		$instance = PluginOptions::getInstance();
		$instance->set_business_owner_email( "new@example.com" );

		$this->assertSame( "new@example.com", $instance->get_business_owner_email() );
	}

	public static function emailProvider(): array {
		return [
			'simple email'         => [ 'test@example.com' ],
			'email with subdomain' => [ 'user@mail.example.com' ],
			'email with plus'      => [ 'user+tag@example.com' ],
			'email with hyphen'    => [ 'first-last@example.com' ],
			'empty string'         => [ '' ],
		];
	}

	#[DataProvider( 'emailProvider' )]
	public function testSetAndGetBusinessOwnerEmailWithVariousFormats( string $email ): void {
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$updatedOptions = [
			"automatic_notifs" => [
				"business_owner_email" => $email,
			]
		];

		Functions\expect( 'update_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $updatedOptions );

		$instance = PluginOptions::getInstance();
		$instance->set_business_owner_email( $email );

		$this->assertSame( $email, $instance->get_business_owner_email() );
	}

	public function testSingletonCannotBeCloned(): void {
		$defaultOptions = [
			"automatic_notifs" => [
				"business_owner_email" => "",
			]
		];

		Functions\expect( 'get_option' )
			->once()
			->with( self::WP_OPTIONS_KEY )
			->andReturn( false );

		Functions\expect( 'add_option' )
			->once()
			->with( self::WP_OPTIONS_KEY, $defaultOptions );

		$instance = PluginOptions::getInstance();

		// Get reflection of the __clone method
		$reflectionClass = new \ReflectionClass( PluginOptions::class );
		$cloneMethod     = $reflectionClass->getMethod( '__clone' );

		// Verify __clone is private
		$this->assertTrue( $cloneMethod->isPrivate(), '__clone method should be private to prevent cloning' );
	}
}