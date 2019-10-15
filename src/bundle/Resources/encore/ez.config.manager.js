const path = require('path');
const eZAdminUIPath = '../../../../../../ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/';

module.exports = (eZConfig, eZConfigManager) => {
    eZConfig.entry['ezplatform-content-variables-variables-list-js'] = [
        path.resolve(__dirname, eZAdminUIPath + 'js/scripts/button.state.toggle.js'),
        path.resolve(__dirname, '../public/js/button.update-priorities.toggler.js'),
        path.resolve(__dirname, '../public/js/button.submit-form.js'),
        path.resolve(__dirname, '../public/js/form.bulk-actions.action.js')
    ];
    eZConfig.entry['ezplatform-content-variables-variables-edit-js'] = [
        path.resolve(__dirname, '../public/js/variable.value-type.js')
    ];
    eZConfig.entry['ezplatform-content-variables-collection-list-js'] = [
        path.resolve(__dirname, eZAdminUIPath + 'js/scripts/button.state.toggle.js'),
        path.resolve(__dirname, '../public/js/button.update-priorities.toggler.js'),
        path.resolve(__dirname, '../public/js/button.submit-form.js'),
        path.resolve(__dirname, '../public/js/form.bulk-actions.action.js')
    ];
};