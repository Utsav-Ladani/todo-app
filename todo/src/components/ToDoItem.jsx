import { faEdit, faSave, faTrash } from "@fortawesome/free-solid-svg-icons"
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import ToDoModel from "../includes/ToDoModel";
import { useState } from "react";
import PropTypes from 'prop-types';

function ToDoItem({ data }) {
    const [isEdit, setIsEdit] = useState(false);
    const [text, setText] = useState('');

    const handleEdit = () => {
        if (!isEdit) {
            setText(data.text);
        }
        else if (text.trim() !== '') {
            const todoModel = new ToDoModel();
            todoModel.edit(data.id, text.trim());
        }

        setIsEdit(!isEdit);
    };

    const handleDelete = () => {
        const todoModel = new ToDoModel();
        todoModel.remove(data.id);
    };

    const handleTextareaChange = (e) => {
        let text = e.target.value;
        text = text.replace(/\n/g, '');

        setText(text);
    };

    const handleToDoDone = () => {
        const todoModel = new ToDoModel();
        todoModel.done(data.id, !data.done);
    };

    return (
        <li className="todo-item">
            <input
                className="todo-item__checkbox"
                type="checkbox"
                id={`todo-item__checkbox-${data.id}`}
                checked={data.done}
                onChange={handleToDoDone}
            />
            {
                isEdit ?
                    <textarea
                        className="todo-item__content"
                        value={text}
                        onChange={handleTextareaChange}
                    /> :
                    <div 
                        className={`todo-item__content ${data.done ? 'todo-item__content--done' : ''}`}
                        onClick={handleToDoDone}
                    >
                        {data.text}
                    </div>
            }
            <div className="btn-wrapper">
                <button className="todo-item__btn btn-edit" onClick={handleEdit} >
                    {
                        isEdit
                            ? <FontAwesomeIcon icon={faSave} />
                            : <FontAwesomeIcon icon={faEdit} />
                    }
                </button>
                <button className="todo-item__btn btn-delete" onClick={handleDelete} >
                    <FontAwesomeIcon icon={faTrash} />
                </button>
            </div>
        </li>
    )
}

ToDoItem.propTypes = {
    data: PropTypes.shape({
        id: PropTypes.string.isRequired,
        text: PropTypes.string.isRequired,
        done: PropTypes.bool.isRequired
    })
}

export default ToDoItem