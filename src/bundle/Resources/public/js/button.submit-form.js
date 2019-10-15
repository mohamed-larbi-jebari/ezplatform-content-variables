(function (global, doc) {
    doc.querySelectorAll('.submit-bulk-actions-form').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (button.hasAttribute('data-submit-button')) {
                doc.querySelector(button.getAttribute('data-submit-button')).click();
            }
        });
    });
})(window, document);