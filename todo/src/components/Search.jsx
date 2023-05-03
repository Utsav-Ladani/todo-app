import { useState } from 'react';
import ToDoModel from '../includes/ToDoModel';

function Search() {
	const [searchTerm, setSearchTerm] = useState('');

	const handleChange = (e) => {
		const text = e.target.value;

		setSearchTerm(text);

        const todoModel = new ToDoModel();
        todoModel.search(text);
	};

	return (
        <div className='search-wrapper'>
            <input
                type="text"
                placeholder="Search your task"
                name="todo-search"
                id="todo-search"
                className="form__input input--search"
                value={searchTerm}
                onChange={handleChange}
            />
        </div>
    )
}

export default Search