import ToDoItem from "./ToDoItem"
import PropTypes from "prop-types"

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

ToDoList.defaultProps = {
    todoList: []
}

ToDoList.propTypes = {
    todoList: PropTypes.arrayOf(PropTypes.object)
}

export default ToDoList