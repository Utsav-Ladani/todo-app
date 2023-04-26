import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faPlus } from '@fortawesome/free-solid-svg-icons'

function InputForm() {
	return (
		<form action="#" method="post" className="form">
			<input
				type="text"
				placeholder="Write your next task"
				name="todo-inp"
				id="todo-inp"
				className="form__input"
			/>
			<button type="submit" className="form__btn">
				<FontAwesomeIcon icon={faPlus} size='xl' />
			</button>
		</form>
	)
}

export default InputForm