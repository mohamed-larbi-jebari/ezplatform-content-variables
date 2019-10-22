export default class ResponseCache {
    constructor(key) {
        this.key = key;
        this.storage = window;
    }

    has() {
        return this.storage.hasOwnProperty(this.key);
    }

    set(data) {
        this.storage[this.key] = data;
    }

    get() {
        return this.storage[this.key];
    }
}