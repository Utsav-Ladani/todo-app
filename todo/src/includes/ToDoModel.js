import { v4 as uuidv4 } from 'uuid';

class ToDoModel {
    static instance = null;

    constructor() {
        if (ToDoModel.instance) {
            return ToDoModel.instance
        }

        const localData = localStorage.getItem('react-todos');

        this.todos = localData ? JSON.parse(localData) : [];
        this.observe = () => { };

        ToDoModel.instance = this;

        return this;
    }

    subscribe(setMethod) {
        this.observe = setMethod;
    }

    get() {
        return [...this.todos];
    }

    add(todo) {
        const id = uuidv4();

        this.todos.push({
            id,
            text: todo,
            done: false
        });
        this.save();
    }

    remove(todoID) {
        this.todos = this.todos.filter((t) => t.id !== todoID);

        this.save();
    }

    edit(todoID, newText) {
        this.todos = this.todos.map((t) => t.id === todoID ? { ...t, text: newText } : t );

        this.save();
    }

    done(todoID, done) {
        this.todos = this.todos.map((t) => t.id === todoID ? { ...t, done: done } : t );

        this.save();
    }
    
    search(text) {
        this.observe( this.todos.filter((t) => t.text.toLowerCase().includes(text.toLowerCase())) );
    }

    clearAll() {
        this.todos = [];
        this.save();
    }

    save() {
        localStorage.setItem('react-todos', JSON.stringify(this.todos));
        this.observe([...this.todos]);
    }
}

export default ToDoModel;