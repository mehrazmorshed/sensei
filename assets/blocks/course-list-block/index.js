/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockVariation } from '@wordpress/blocks';
import { list } from '@wordpress/icons';

export const registerCourseListBlock = () => {
	const QUERY_DEFAULT_ATTRIBUTES = {
		className: 'course-list-block',
		query: {
			perPage: 3,
			pages: 0,
			offset: 0,
			postType: 'course',
			order: 'desc',
			orderBy: 'date',
			author: '',
			search: '',
			exclude: [],
			sticky: '',
			inherit: false,
		},
	};

	registerBlockVariation( 'core/query', {
		name: 'course-list',
		title: __( 'Course List', 'sensei-lms' ),
		description: __( 'Show a list of courses.', 'sensei-lms' ),
		icon: list,
		category: 'sensei-lms',
		keywords: [
			__( 'Course', 'sensei-lms' ),
			__( 'List', 'sensei-lms' ),
			__( 'Courses', 'sensei-lms' ),
		],
		attributes: { ...QUERY_DEFAULT_ATTRIBUTES },
		isActive: ( blockAttributes, variationAttributes ) => {
			// Using className instead of postType because otherwise a normal Query Loop block
			// will turn into a Course List block if the post type 'course' is selected. As we're planning
			// to hide the Post Type dropdown for Course List block, so after changing the type to course,
			// the Query loop user will not be able to change the post type again. We don't want that to
			// happen.
			return blockAttributes.className?.match(
				variationAttributes.className
			);
		},
		scope: [ 'block', 'inserter' ],
	} );
};
