const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfig.entry['ezplatform-content-variables-bulk-edit-css'] = [
        path.resolve(__dirname, '../public/css/button-collapse.css')
    ];
    ibexaConfig.entry['ezplatform-content-variables-variables-list-js'] = [
        path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
        path.resolve(__dirname, '../public/js/button.update-priorities.toggler.js'),
        path.resolve(__dirname, '../public/js/button.submit-form.js'),
        path.resolve(__dirname, '../public/js/form.bulk-actions.action.js'),
        path.resolve(__dirname, '../public/js/form.toggle-checkbox.js')
    ];
    ibexaConfig.entry['ezplatform-content-variables-variables-edit-js'] = [
        path.resolve(__dirname, '../public/js/variable.value-type.js')
    ];
    ibexaConfig.entry['ezplatform-content-variables-collection-list-js'] = [
        path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
        path.resolve(__dirname, '../public/js/button.update-priorities.toggler.js'),
        path.resolve(__dirname, '../public/js/button.submit-form.js'),
        path.resolve(__dirname, '../public/js/form.bulk-actions.action.js'),
        path.resolve(__dirname, '../public/js/form.toggle-checkbox.js')
    ];
    ibexaConfig.entry['ezplatform-content-variables-bulk-edit-js'] = [
        path.resolve(__dirname, '../public/js/button.collapsable-collections.js')
    ];
    ibexaConfig.entry['ezplatform-content-variables-related-content-js'] = [
        path.resolve(__dirname, '../public/js/button.collapsable-collections.js')
    ];
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [
            path.resolve(__dirname, '../public/css/ibexa-admin-ui-layout-css.css')
        ]
    });

};