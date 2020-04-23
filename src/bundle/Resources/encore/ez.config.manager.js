const path = require('path');
const eZAdminUIPath = '../../../../../../ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/';

module.exports = (eZConfig, eZConfigManager) => {
    eZConfig.entry['ezplatform-content-variables-bulk-edit-css'] = [
        path.resolve(__dirname, '../public/css/button-collapse.css')
    ];
    eZConfig.entry['ezplatform-content-variables-variables-list-js'] = [
        path.resolve(__dirname, eZAdminUIPath + 'js/scripts/button.state.toggle.js'),
        path.resolve(__dirname, '../public/js/button.update-priorities.toggler.js'),
        path.resolve(__dirname, '../public/js/button.submit-form.js'),
        path.resolve(__dirname, '../public/js/form.bulk-actions.action.js'),
        path.resolve(__dirname, '../public/js/form.toggle-checkbox.js')
    ];
    eZConfig.entry['ezplatform-content-variables-variables-edit-js'] = [
        path.resolve(__dirname, '../public/js/variable.value-type.js')
    ];
    eZConfig.entry['ezplatform-content-variables-collection-list-js'] = [
        path.resolve(__dirname, eZAdminUIPath + 'js/scripts/button.state.toggle.js'),
        path.resolve(__dirname, '../public/js/button.update-priorities.toggler.js'),
        path.resolve(__dirname, '../public/js/button.submit-form.js'),
        path.resolve(__dirname, '../public/js/form.bulk-actions.action.js'),
        path.resolve(__dirname, '../public/js/form.toggle-checkbox.js')
    ];
    eZConfig.entry['ezplatform-content-variables-bulk-edit-js'] = [
        path.resolve(__dirname, '../public/js/button.collapsable-collections.js')
    ];
    eZConfig.entry['ezplatform-content-variables-related-content-js'] = [
        path.resolve(__dirname, '../public/js/button.collapsable-collections.js')
    ];
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-richtext-onlineeditor-js',
        newItems: [
            path.resolve(__dirname, '../public/js/alloyeditor/buttons/content-variable-insert.js'),
            path.resolve(__dirname, '../public/js/alloyeditor/buttons/content-variable-edit.js'),
            path.resolve(__dirname, '../public/js/alloyeditor/toolbars/content-variable-edit.js'),
            path.resolve(__dirname, '../public/js/alloyeditor/plugins/content-variables.js'),
        ]
    });
    // eZConfigManager.replace({
    //     eZConfig,
    //     entryName: 'ezplatform-admin-ui-alloyeditor-js',
    //     itemToReplace: path.resolve(__dirname, eZAdminUIPath + 'js/scripts/fieldType/base/base-rich-text.js'),
    //     newItem: path.resolve(__dirname, '../public/js/scripts/fieldType/base/base-rich-text.js'),
    // });
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-admin-ui-layout-css',
        newItems: [
            path.resolve(__dirname, '../public/css/alloyeditor/widgets/content-variable.css'),
        ]
    });

};