import ContentVariableDefinition from '../widgets/content-variable';

(function(global) {
    if (CKEDITOR.plugins.get('content-variables')) {
        return;
    }

    CKEDITOR.plugins.add('content-variables', {
        requires: 'widget',
        init: function(editor) {
            const definition = new ContentVariableDefinition(editor);
            editor.widgets.add(definition.getIdentifier(), definition.getDefinition());
        },
    });
})(window);