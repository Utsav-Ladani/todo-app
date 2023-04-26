import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faPlus } from '@fortawesome/free-solid-svg-icons'
import { useState } from 'react';
import ToDoModel from '../includes/ToDoModel';

function InputForm() {
	const [inpValue, setInpValue] = useState('');

	const handleSubmit = (e) => {
		e.preventDefault();

		const text = inpValue.trim();

		if (text) {
			const todoModel = new ToDoModel();
			todoModel.add(text);

			setInpValue('');
		}
	};

	return (
		<form action="#" method="post" className="form" onSubmit={handleSubmit}>
			<input
				type="text"
				placeholder="Write your next task"
				name="todo-inp"
				id="todo-inp"
				className="form__input"
				value={inpValue}
				onChange={(e) => setInpValue(e.target.value)}
			/>
			<button type="submit" className="form__btn">
				<FontAwesomeIcon icon={faPlus} size='xl' />
			</button>
		</form>
	)
}

export default InputForm