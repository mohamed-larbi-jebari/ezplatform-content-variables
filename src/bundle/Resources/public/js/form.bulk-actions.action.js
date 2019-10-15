(function (global, doc) {
    doc.querySelectorAll('.update-bulk-action').forEach((button) => {
        const actionElement = doc.querySelector(button.getAttribute('data-action-element'));
        const action = button.getAttribute('data-action');
        button.addEventListener('click', (event) => {
            actionElement.value = action
        }, false);
    });
})(window, document);