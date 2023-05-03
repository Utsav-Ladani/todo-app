import ToDoItem from "./ToDoItem"
import PropTypes from "prop-types"
import { useState } from "react"

function ToDoList( { todoList } ) {
    const [filterID, setFilterID] = useState(1);

    const filters = [
        {
            id: 1,
            text: 'All',
        },
        {
            id: 2,
            text: 'Completed',
        },
        {
            id: 3,
            text: 'Remainings',
        }
    ];

    return (
        <>
            <div className="filters" >
                {
                    filters.map((filter) => (
                        <label key={filter.id}  >
                            <input
                                type="radio"
                                name="filter"
                                value={filterID}
                                className="filter__input"
                                onChange={() => setFilterID(filter.id)}
                            />
                            <span className={ "filter__text" + (filterID === filter.id ? ' filter__text--active' : '') }>{filter.text}</span>
                        </label>
                    ))
                }
            </div>
            <ul className="todo-list">
                {
                    todoList.length ?
                        todoList.filter((todo) => {
                            if (filterID === 2) {
                                return todo.done;
                            }
                            else if (filterID === 3) {
                                return !todo.done;
                            }

                            return true;
                        }).map((todo) => <ToDoItem key={todo.id} data={todo} />) :
                        <li className="todo-item--empty">No tasks</li>
                }
            </ul>
        </>
    )
}

ToDoList.defaultProps = {
    todoList: []
}

ToDoList.propTypes = {
    todoList: PropTypes.arrayOf(PropTypes.object)
}

export default ToDoList