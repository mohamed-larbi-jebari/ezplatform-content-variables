(function (global, doc) {
    doc.querySelectorAll('.content-variable-update-priorities').forEach((button) => {
        const containerForm = doc.querySelector(button.getAttribute('data-form'));
        if (containerForm === null) {
            return;
        }

        const priorityInputs = containerForm.querySelectorAll('.content-variable-priority-value');

        priorityInputs.forEach((priorityInput) => {
            priorityInput.addEventListener('focus', (event) => {
                if (button.hasAttribute('disabled')) {
                    button.removeAttribute('disabled');
                }
            }, false);
        });
    });
})(window, document);