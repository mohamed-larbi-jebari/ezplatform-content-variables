(function (global, doc) {
    doc.querySelectorAll('table').forEach((table) => {
        const toggleCheckbox = table.querySelector('th > input[type="checkbox"]');
        const checkboxes = [...table.querySelectorAll('td > input[type="checkbox"]:not([disabled])')];
        if (toggleCheckbox === null || checkboxes.length === 0) {
            return;
        }

        checkboxes.forEach((el) => {
            el.addEventListener('change', (event) => {
                if (toggleCheckbox.checked && event.target.checked === false) {
                    toggleCheckbox.checked = false;
                }
            });
        });
        toggleCheckbox.addEventListener('change', (event) => {
            checkboxes.forEach((el) => {
                el.checked = event.target.checked;
                el.dispatchEvent(new Event('change'));
            });
        });
    });
})(window, document);