import { useEffect, useState } from 'react';
import './App.css'
import InputForm from './components/InputForm'
import ToDoList from './components/ToDoList'
import ToDoModel from './includes/ToDoModel'

function App() {
	const [todoList, setToDoList] = useState([]);

	useEffect(() => {
		const todoModel = new ToDoModel();
		todoModel.subscribe(setToDoList);
		
		setToDoList(todoModel.get());
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
