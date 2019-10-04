const path = require('path');
const eZAdminUIPath = '../../../../../../ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/';

module.exports = (eZConfig, eZConfigManager) => {
    eZConfig.entry['ezplatform-contnet-variables-variables-list-js'] = [
        path.resolve(__dirname, eZAdminUIPath + 'js/scripts/button.state.toggle.js')
    ];
    eZConfig.entry['ezplatform-contnet-variables-variables-edit-js'] = [
        path.resolve(__dirname, '../public/js/variable.value-type.js')
    ];
    eZConfig.entry['ezplatform-contnet-variables-collection-list-js'] = [
        path.resolve(__dirname, eZAdminUIPath + 'js/scripts/button.state.toggle.js')
    ];
};