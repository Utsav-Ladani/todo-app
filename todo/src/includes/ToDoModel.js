class ToDoModel {
    static instance = null;

    constructor() {
        if (ToDoModel.instance) {
            return ToDoModel.instance
        }

        this.todos = [];
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
        this.todos.push(todo);
        this.observe(this.todos);
    }

    remove(todo) {
        this.todos = this.todos.filter((t) => t !== todo);
    }

    edit(todo, newTodo) {
        // this.todos = this.todos.map((t) => t === todo ? newTodo : t);
    }
}

export default ToDoModel;