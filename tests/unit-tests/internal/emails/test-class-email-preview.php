<?php

namespace SenseiTest\Internal\Emails;

use Sensei\Internal\Emails\Email_Preview;
use Sensei\Internal\Emails\Email_Sender;
use Sensei_Assets;
use Sensei_Factory;
use Sensei_Test_Login_Helpers;
use WP_Post;
use WPDieException;

/**
 * Tests for Sensei\Internal\Emails\Email_Preview.
 *
 * @covers \Sensei\Internal\Emails\Email_Preview
 */
class Email_Preview_Test extends \WP_UnitTestCase {
	use Sensei_Test_Login_Helpers;

	/**
	 * Factory instance.
	 *
	 * @var Sensei_Factory
	 */
	protected $factory;

	/**
	 * Email_Sender mock.
	 *
	 * @var \PHPUnit\Framework\MockObject\MockObject
	 */
	protected $email_sender;

	/**
	 * Sensei_Assets mock.
	 *
	 * @var \PHPUnit\Framework\MockObject\MockObject
	 */
	protected $assets;

	public function setUp(): void {
		parent::setUp();
		$this->factory      = new Sensei_Factory();
		$this->email_sender = $this->createMock( Email_Sender::class );
		$this->assets       = $this->createMock( Sensei_Assets::class );
	}

	public function tearDown(): void {
		parent::tearDown();
		$this->factory->tearDown();
	}

	public function testInit_WhenCalled_AddsRenderPreviewHook() {
		/* Arrange. */
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		/* Act. */
		$email_preview->init();

		/* Assert. */
		$priority = has_action( 'template_redirect', [ $email_preview, 'render_preview' ] );
		$this->assertSame( 10, $priority );
	}

	public function testInit_WhenCalled_AddsPreviewPostLinkHook() {
		/* Arrange. */
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		/* Act. */
		$email_preview->init();

		/* Assert. */
		$priority = has_action( 'preview_post_link', [ $email_preview, 'filter_preview_link' ] );
		$this->assertSame( 10, $priority );
	}

	public function testInit_WhenCalled_AddsPostTypeLinkHook() {
		/* Arrange. */
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		/* Act. */
		$email_preview->init();

		/* Assert. */
		$priority = has_action( 'post_type_link', [ $email_preview, 'filter_preview_link' ] );
		$this->assertSame( 10, $priority );
	}

	public function testRenderPreview_WhenNoPreviewID_DoesNothing() {
		/* Arrange. */
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		/* Act. */
		$result = $email_preview->render_preview();

		/* Assert. */
		$this->assertNull( $result );
	}

	public function testRenderPreview_WhenPostDoesNotExist_ThrowsError() {
		/* Arrange. */
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$_GET['sensei_email_preview_id'] = 42;

		/* Assert. */
		$this->expectException( WPDieException::class );
		$this->expectExceptionMessage( 'Invalid request' );

		/* Act. */
		$email_preview->render_preview();
	}

	public function testRenderPreview_WhenIncorrectPostType_ThrowsError() {
		/* Arrange. */
		$post_id       = $this->factory->post->create();
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$_GET['sensei_email_preview_id'] = $post_id;

		/* Assert. */
		$this->expectException( WPDieException::class );
		$this->expectExceptionMessage( 'Invalid request' );

		/* Act. */
		$email_preview->render_preview();
	}

	public function testRenderPreview_WhenInsufficientPermissions_ThrowsError() {
		/* Arrange. */
		$this->login_as_teacher();

		$post_id       = $this->factory->email->create();
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$_GET['sensei_email_preview_id'] = $post_id;

		/* Assert. */
		$this->expectException( WPDieException::class );
		$this->expectExceptionMessage( 'Insufficient permissions' );

		/* Act. */
		$email_preview->render_preview();
	}

	public function testRenderPreview_WhenIncorrectAdminReferrer_ThrowsError() {
		/* Arrange. */
		$this->login_as_admin();

		$post_id       = $this->factory->email->create();
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$_GET['sensei_email_preview_id'] = $post_id;

		/* Assert. */
		$this->expectException( WPDieException::class );
		$this->expectExceptionMessage( 'The link you followed has expired.' );

		/* Act. */
		$email_preview->render_preview();
	}

	public function testRenderPreview_WhenRenderingPage_RendersEmailSubject() {
		/* Arrange. */
		$this->login_as_admin();

		$post          = $this->factory->email->create_and_get( [ 'post_title' => 'Welcome' ] );
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$this->email_sender
			->expects( $this->once() )
			->method( 'get_email_subject' )
			->with( $post )
			->willReturn( 'Welcome' );

		$_GET['sensei_email_preview_id'] = $post->ID;
		$_REQUEST['_wpnonce']            = wp_create_nonce( 'preview-email-post_' . $post->ID );

		/* Act. */
		ob_start();
		$email_preview->render_preview();
		$content = ob_get_clean();

		/* Assert. */
		$this->assertStringContainsString( 'Welcome', $content );
	}

	public function testRenderPreview_WhenRenderingPage_RendersFromInfo() {
		/* Arrange. */
		$this->login_as_admin();

		$post_id       = $this->factory->email->create();
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$this->email_sender
			->expects( $this->once() )
			->method( 'get_from_address' )
			->willReturn( 'from@address.com' );

		$this->email_sender
			->expects( $this->once() )
			->method( 'get_from_name' )
			->willReturn( 'Sensei Form Name' );

		$_GET['sensei_email_preview_id'] = $post_id;
		$_REQUEST['_wpnonce']            = wp_create_nonce( 'preview-email-post_' . $post_id );

		/* Act. */
		ob_start();
		$email_preview->render_preview();
		$content = ob_get_clean();

		/* Assert. */
		$this->assertStringContainsString( 'Sensei Form Name', $content, 'Fail to render the from name' );
		$this->assertStringContainsString( 'from@address.com', $content, 'Fail to render the from address' );
	}

	public function testRenderPreview_WhenRenderingPage_RendersAvatar() {
		/* Arrange. */
		$this->login_as_admin();

		$post_id       = $this->factory->email->create();
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$_GET['sensei_email_preview_id'] = $post_id;
		$_REQUEST['_wpnonce']            = wp_create_nonce( 'preview-email-post_' . $post_id );

		/* Act. */
		ob_start();
		$email_preview->render_preview();
		$content = ob_get_clean();

		/* Assert. */
		$this->assertStringContainsString( 'gravatar', $content );
	}

	public function testRenderPreview_WhenRenderingEmail_RendersEmailBody() {
		/* Arrange. */
		$this->login_as_admin();

		$post          = $this->factory->email->create_and_get( [ 'post_content' => 'content' ] );
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );

		$this->email_sender
			->expects( $this->once() )
			->method( 'get_email_body' )
			->with( $post )
			->willReturn( 'content' );

		$_GET['sensei_email_preview_id'] = $post->ID;
		$_GET['render_email']            = 1;
		$_REQUEST['_wpnonce']            = wp_create_nonce( 'preview-email-post_' . $post->ID );

		/* Act. */
		ob_start();
		$email_preview->render_preview();
		$content = ob_get_clean();

		/* Assert. */
		$this->assertSame( 'content', $content );
	}

	public function testGetPreviewLink_WhenCalled_ReturnsThePreviewLink() {
		/* Arrange. */
		$post_id = 1;

		/* Act. */
		$link = Email_Preview::get_preview_link( $post_id );

		/* Assert. */
		$this->assertSame(
			wp_nonce_url( get_home_url() . "?sensei_email_preview_id=$post_id", 'preview-email-post_' . $post_id ),
			$link
		);
	}

	public function testFilterPreviewLink_WhenCalled_ReturnsThePreviewLink() {
		/* Arrange. */
		$email_preview = new Email_Preview( $this->email_sender, $this->assets );
		$link          = 'http://example.com/?p=1';
		$post          = new WP_Post(
			(object) array(
				'ID'        => 1,
				'post_type' => 'sensei_email',
			)
		);

		/* Act. */
		$filtered_link = $email_preview->filter_preview_link( $link, $post );

		/* Assert. */
		$nonce = wp_create_nonce( 'preview-email-post_1' );
		$this->assertSame(
			get_home_url() . '?sensei_email_preview_id=1&_wpnonce=' . $nonce,
			$filtered_link
		);
	}
}
