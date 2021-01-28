/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';

/**
 * Hook to check if a lesson can be completed manually.
 *
 * @return {boolean} If a lesson can be marked as completed by student.
 */
const useCompleteLessonAllowed = () => {
	const passRequiredCheckbox = document.getElementById( 'pass_required' );

	const [ completeLessonAllowed, setCompleteLessonAllowed ] = useState(
		() => {
			return ! passRequiredCheckbox || ! passRequiredCheckbox.checked;
		}
	);

	useEffect( () => {
		// Ignore if the checkbox isn't present.
		if ( ! passRequiredCheckbox ) {
			return;
		}

		const passRequiredEventHandler = () => {
			setCompleteLessonAllowed( ! passRequiredCheckbox.checked );
		};

		passRequiredCheckbox.addEventListener(
			'change',
			passRequiredEventHandler
		);

		return () => {
			passRequiredCheckbox.removeEventListener(
				'change',
				passRequiredEventHandler
			);
		};
	}, [ passRequiredCheckbox ] );

	return completeLessonAllowed;
};

export default useCompleteLessonAllowed;
