import { useEffect, useState } from 'react';
import './App.css'
import InputForm from './components/InputForm'
import ToDoList from './components/ToDoList'
import ToDoModel from './includes/ToDoModel'

function App() {
	const todoModel = new ToDoModel();
	const [todoList, setToDoList] = useState(todoModel.get());

	useEffect(() => {
		todoModel.subscribe(setToDoList);
	},[]);

	return (
		<main className='main'>
			<header>
				<h1 className='header__h1'>ToDo App</h1>
			</header>
			<InputForm />
			<ToDoList todoList={todoList} />
		</main>
	)
}

export default App
