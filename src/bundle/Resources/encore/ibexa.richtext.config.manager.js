const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-richtext-onlineeditor-js',
        newItems: [
            // path.resolve(__dirname, '../public/js/alloyeditor/buttons/content-variable-edit.js'),
            // path.resolve(__dirname, '../public/js/alloyeditor/plugins/content-variables.js'),
        ]
    });
};
