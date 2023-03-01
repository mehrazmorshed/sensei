<?php
namespace SenseiTest\Internal\Emails;

use Sensei\Internal\Emails\Email_Customization;
use Sensei_Assets;
use Sensei_Settings;

/**
 * Tests for the Email_Customization class.
 *
 * @covers \Sensei\Internal\Emails\Email_Customization
 */
class Email_Customization_Test extends \WP_UnitTestCase {
	public function setUp(): void {
		parent::setUp();
		$this->settings = $this->createMock( Sensei_Settings::class );
		$this->assets   = $this->createMock( Sensei_Assets::class );
	}

	public function testInstance_WhenCalled_ReturnsInstance() {
		/* Act. */
		$result = Email_Customization::instance( $this->settings, $this->assets );

		/* Assert. */
		$this->assertInstanceOf( Email_Customization::class, $result );
	}

	public function testInstace_WhenInitiated_AddsHookForRemovingLegacyEmail() {
		/* Arrange. */
		$instance = Email_Customization::instance( $this->settings, $this->assets );

		/* Act. */
		$instance->init();

		/* Assert. */
		$this->assertEquals( 10, has_action( 'init', [ $instance, 'disable_legacy_emails' ] ) );
	}

	/**
	 * Tests that the legacy email hooks are removed when the disable_legacy_emails function is called.
	 *
	 * @param string $action_name The name of the action.
	 * @param string $function_name The name of the function.
	 * @param object $hook_instance The instance of the hook's class.
	 *
	 * @dataProvider legacyHooksDataProvider
	 */
	public function testDisableLegacy_WhenCalled_RemovesLegacyEmailHooks( $action_name, $function_name, $hook_instance ) {
		/* Arrange. */
		$instance        = Email_Customization::instance( $this->settings, $this->assets );
		$priority_before = has_action( $action_name, [ $hook_instance, $function_name ] );

		/* Act. */
		$instance->disable_legacy_emails();

		/* Assert. */
		$priority_after = has_action( $action_name, [ $hook_instance, $function_name ] );
		$this->assertNotEquals( $priority_before, $priority_after );
	}

	public function legacyHooksDataProvider() {
		return [
			'student_completes_course' => [ 'sensei_course_status_updated', 'teacher_completed_course', \Sensei()->emails ],
			'student_starts_course'    => [ 'sensei_user_course_start', 'teacher_started_course', \Sensei()->emails ],
			'student_quiz_submitted'   => [ 'sensei_user_quiz_submitted', 'teacher_quiz_submitted', \Sensei()->emails ],
			'course_completed'         => [ 'sensei_course_status_updated', 'learner_completed_course', \Sensei()->emails ],
			'teacher_course_assigned'  => [ 'sensei_course_new_teacher_assigned', 'teacher_course_assigned_notification', \Sensei()->teacher ],
		];
	}
}
