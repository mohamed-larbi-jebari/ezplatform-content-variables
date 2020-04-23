import EzConfigBase from './../../../../../../../../../ezsystems/ezplatform-richtext/src/bundle/Resources/public/js/OnlineEditor/toolbars/config/base';

export default class ContentVariableEditConfig extends EzConfigBase {
    constructor(config) {
        super(config);

        this.name = 'content-variable-edit';
        this.buttons = [this.getEditAttributesButton(config), 'content-variable-edit', 'ezblockremove'];
    }

    test(payload) {
        const nativeEvent = payload.data.nativeEvent;

        if (!nativeEvent) {
            return false;
        }

        const target = new CKEDITOR.dom.element(nativeEvent.target);
        const widget = payload.editor.get('nativeEditor').widgets.getByElement(target);

        return !!(widget && widget.name === 'content-variable');
    }
}

const eZ = (window.eZ = window.eZ || {});

eZ.ezAlloyEditor = eZ.ezAlloyEditor || {};
eZ.ezAlloyEditor.customSelections = eZ.ezAlloyEditor.customSelections || {};
eZ.ezAlloyEditor.customSelections.contentVariableEdit = ContentVariableEditConfig;

eZ.addConfig('ezAlloyEditor.customSelections.contentVariableEdit', ContentVariableEditConfig);
