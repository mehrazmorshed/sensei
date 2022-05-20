/**
 * External dependencies
 */
import { fireEvent, render } from '@testing-library/react';
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import Wizard from './wizard';

const SOME_DUMMY_CONTENT = 'SOME_DUMMY_CONTENT';
const SOME_DUMMY_ACTIONS = 'SOME_DUMMY_ACTIONS';
describe( '<Wizard />', () => {
	it( 'Wizard renders first step content and actions by default.', () => {
		const DummyStep = () => {
			return SOME_DUMMY_CONTENT;
		};
		DummyStep.Actions = () => {
			return SOME_DUMMY_ACTIONS;
		};

		const { queryByText } = render(
			<Wizard steps={ [ DummyStep ] } onChange={ () => {} } />
		);

		expect( queryByText( SOME_DUMMY_CONTENT ) ).toBeTruthy();
		expect( queryByText( SOME_DUMMY_ACTIONS ) ).toBeTruthy();
		expect( queryByText( 'Step 1 of 1' ) ).toBeTruthy();
	} );

	it( 'Wizard renders step without actions.', () => {
		const DummyStepWithoutActions = () => {
			return SOME_DUMMY_CONTENT;
		};
		const { queryByText } = render(
			<Wizard
				steps={ [ DummyStepWithoutActions ] }
				onChange={ () => {} }
			/>
		);

		expect( queryByText( SOME_DUMMY_CONTENT ) ).toBeTruthy();
		expect( queryByText( SOME_DUMMY_ACTIONS ) ).toBeFalsy();
		expect( queryByText( 'Step 1 of 1' ) ).toBeTruthy();
	} );

	it( 'Wizard navigates to next step.', () => {
		const FirstStep = () => {
			return 'FIRST_STEP_CONTENT';
		};
		FirstStep.Actions = ( { goToNextStep } ) => {
			return <button onClick={ goToNextStep }>Next</button>;
		};
		const SecondStep = () => {
			return 'SECOND_STEP_CONTENT';
		};

		const { queryByText } = render(
			<Wizard steps={ [ FirstStep, SecondStep ] } onChange={ () => {} } />
		);

		expect( queryByText( 'FIRST_STEP_CONTENT' ) ).toBeTruthy();
		expect( queryByText( 'Step 1 of 2' ) ).toBeTruthy();

		fireEvent.click( queryByText( 'Next' ) );

		expect( queryByText( 'SECOND_STEP_CONTENT' ) ).toBeTruthy();
		expect( queryByText( 'Step 2 of 2' ) ).toBeTruthy();
	} );

	it( 'Wizard calls `onCompletion` callback after last step.', () => {
		const SingleStep = () => {
			return null;
		};
		SingleStep.Actions = ( { goToNextStep } ) => {
			return <button onClick={ goToNextStep }>Next</button>;
		};
		const onCompletionCallback = jest.fn();

		const { queryByText } = render(
			<Wizard
				steps={ [ SingleStep ] }
				onChange={ () => {} }
				onCompletion={ onCompletionCallback }
			/>
		);
		fireEvent.click( queryByText( 'Next' ) );
		expect( onCompletionCallback ).toBeCalled();
	} );

	it( 'Wizard calls `onChange` when wizard data is updated.', () => {
		const onChangeMock = jest.fn();
		const StepWithInput = ( {
			data: wizardData,
			setData: setWizardData,
		} ) => {
			return (
				<input
					value={ wizardData.value ?? '' }
					onChange={ ( e ) => {
						setWizardData( { value: e.target.value } );
					} }
				></input>
			);
		};

		const { queryByRole } = render(
			<Wizard steps={ [ StepWithInput ] } onChange={ onChangeMock } />
		);
		fireEvent.change( queryByRole( 'textbox' ), {
			target: { value: 'TEST_DATA' },
		} );
		expect( onChangeMock ).toHaveBeenCalledWith( { value: 'TEST_DATA' } );
	} );
} );
