(function (global, doc) {
    // ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable::VALUE_STATIC_PLACEHOLDER
    const placeholder = 'empty-value-placeholder';
    // ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable::VALUE_TYPE_CALLBACK
    const callbackType = 2;

    const handleValueType = (typeSelect) => {
        const container = typeSelect.closest('.content-variable-block');
        const staticValue = container.querySelector('.content-variable-value-static');
        const callbackValue = container.querySelector('.content-variable-value-callback');

        if (parseInt(typeSelect.value) === callbackType) {
            if (staticValue.value === '') {
                staticValue.value = placeholder;
            }
            staticValue.parentElement.hidden = true;
            staticValue.closest('.form-group').hidden = true;
            callbackValue.parentElement.hidden = false;
            callbackValue.closest('.ibexa-dropdown--single').hidden = false;
            callbackValue.closest('.form-group').hidden = false;
        } else {
            if (staticValue.value === placeholder) {
                staticValue.value = '';
            }
            staticValue.parentElement.hidden = false;
            staticValue.closest('.form-group').hidden = false;
            callbackValue.parentElement.hidden = true;
            callbackValue.closest('.ibexa-dropdown--single').hidden = true;
            callbackValue.closest('.form-group').hidden = true;
        }
    };
    const items = doc.querySelectorAll('.content-variable-value-type');

    items.forEach((item) => {
        handleValueType(item);
        item.addEventListener('change', (event) => {
            handleValueType(event.target);
        }, false);
    });
})(window, document);