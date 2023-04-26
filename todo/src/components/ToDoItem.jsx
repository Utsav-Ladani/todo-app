import { faEdit, faTrash } from "@fortawesome/free-solid-svg-icons"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"

function ToDoItem({ data }) {
    return (
        <li className="todo-item">
            <input
                className="todo-item__checkbox"
                type="checkbox"
            />
            <div className="todo-item__content">
                {data}
            </div>
            <div className="btn-wrapper">
                <button className="todo-item__btn btn-edit">
                    <FontAwesomeIcon icon={faEdit} />
                </button>
                <button className="todo-item__btn btn-delete">
                    <FontAwesomeIcon icon={faTrash} />
                </button>
            </div>
        </li>
    )
}

export default ToDoItem