import ToDoItem from "./ToDoItem"

function ToDoList( { todoList } ) {
    return (
        <ul className="todo-list">
            {
                todoList.map((val, index) => (
                    <ToDoItem key={index} data={val} />
                ))
            }
        </ul>
    )
}

export default ToDoList