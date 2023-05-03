import { useEffect, useState } from 'react';
import './App.css'
import InputForm from './components/InputForm'
import ToDoList from './components/ToDoList'
import ToDoModel from './includes/ToDoModel'
import Search from './components/Search';

function App() {
	const [todoList, setToDoList] = useState([]);

	useEffect(() => {
		const todoModel = new ToDoModel();
		todoModel.subscribe(setToDoList);
		
		setToDoList(todoModel.get());
	},[]);

	const clearAll = () => {
		const todoModel = new ToDoModel();
		todoModel.clearAll();
	};

	return (
		<main className='main'>
			<header>
				<h1 className='header__h1'>ToDo App</h1>
			</header>
			<InputForm />
			<Search />
			<ToDoList  todoList={todoList} />
			<div className='btn-clear-wrapper'>
				<button 
					className='btn-clear' 
					onClick={clearAll}
				>
					Clear All
				</button>
			</div>
		</main>
	)
}

export default App
