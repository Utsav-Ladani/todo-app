import ToDoItem from "./ToDoItem"

function ToDoList( { todoList } ) {
    return (
        <ul className="todo-list">
            {
                todoList.length ?
                    todoList.map((todo) => <ToDoItem key={todo.id} data={todo} />) :
                    <li className="todo-item--empty">No tasks</li>
            }
        </ul>
    )
}

export default ToDoList