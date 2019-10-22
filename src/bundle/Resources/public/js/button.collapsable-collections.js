import Cookies from './universalCookie.min';

(function (global, doc) {
    const handleCollectionCollapse = (button) => {
        const itemId = button.getAttribute('data-item-id');
        const iconShow = button.querySelector('svg.collapse-show');
        const iconHide = button.querySelector('svg.collapse-hide');
        const doHide = iconShow.hasAttribute('hidden');

        let collapsedItemIds = getCollapsedItemIds(button);
        if (doHide) {
            iconShow.removeAttribute('hidden');
            iconHide.setAttribute('hidden', true);
            if (collapsedItemIds.includes(itemId) === false) {
                collapsedItemIds.push(itemId);
            }
        } else {
            iconShow.setAttribute('hidden', true);
            iconHide.removeAttribute('hidden');
            let index = collapsedItemIds.indexOf(itemId);
            if (index > -1) {
                collapsedItemIds.splice(index, 1);
            }
        }
        storeCollapsedItemIds(button, collapsedItemIds);
    };

    const getCollapsedItemIds = (button) => {
        const cookies = new Cookies();
        const cookieVariable = button.getAttribute('data-cookie-variable');
        const cookieSeprator = button.getAttribute('data-cookie-separator');

        return cookies.get(cookieVariable)
            ? cookies.get(cookieVariable).split(cookieSeprator)
            : [];
    };

    const storeCollapsedItemIds = (button, itemIds) => {
        const cookies = new Cookies();
        const cookieVariable = button.getAttribute('data-cookie-variable');
        const cookieSeparator = button.getAttribute('data-cookie-separator');

        cookies.set(cookieVariable, itemIds.join(cookieSeparator));
    };

    const items = doc.querySelectorAll('.content-variables-collapsable-collections');
    items.forEach((item) => {
        item.addEventListener('click', (event) => {
            handleCollectionCollapse(item);
        }, false);
    });
})(window, document);