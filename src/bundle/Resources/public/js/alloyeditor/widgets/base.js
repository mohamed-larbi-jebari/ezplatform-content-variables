import React from 'react';

export default class BaseDefinition {

    constructor(editor) {
        this.editor = editor;
    }

    getIdentifier() {
        return null;
    }

    isInline() {
        return false;
    }

    isDraggable() {
        return false;
    }

    getTemplate() {
        return null;
    }

    getTemplateDefaults() {
        return {};
    }

    insertWrapper(wrapper) {
        this.editor.insertElement(wrapper);
    }

    upcast(element) {
        return false;
    }

    update(data) {
        this.render(this.data);
    }

    render(data) {
        const newWidget = CKEDITOR.dom.element.createFromHtml(this.template.output(data));
        newWidget.copyAttributes(this.element, {});
        this.element.setHtml(newWidget.getHtml());
    }

    getDefinition() {
        return {
            // CKEditor
            inline: this.isInline(),
            draggable: this.isDraggable(),
            template: this.getTemplate().replace(/\n/g, " ").trim(),
            defaults: this.getTemplateDefaults(),
            upcast: this.upcast,
            insert: this.insert,
            edit: this.edit,
            init: this.init,
            // Custom
            editor: this.editor,
            insertWrapper: this.insertWrapper,
            getIdentifier: this.getIdentifier,
            fireEditorInteraction: this.fireEditorInteraction,
            getWrapperRegion: this.getWrapperRegion,
            getEzConfigElement: this.getEzConfigElement,
            cancelEditEvents: this.cancelEditEvents,
            update: this.update,
            render: this.render,
            readDataFromAttributes: this.readDataFromAttributes,
        };
    }

    /**
     * Insert an `content-variable` widget in the editor. It overrides the
     * default implementation to make sure that in the case where an widget
     * is focused, a new one is added after it.
     *
     * https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_plugins_widget_definition.html#property-insert
     */
    insert() {
        const editor = this.editor;
        const element = CKEDITOR.dom.element.createFromHtml(
            this.template.output(this.defaults)
        );
        const wrapper = editor.widgets.wrapElement(element, this.name);

        editor.widgets.initOn(element, this.name);
        this.insertWrapper(wrapper);

        const instance = editor.widgets.getByElement(wrapper);
        instance.ready = true;
        instance.fire('ready');
        instance.focus();
    }

    /**
     * It's not possible to *edit* an widget in AlloyEditor,
     * so `edit` directly calls `insert` instead. This is needed
     * because by default, the CKEditor engine calls this method
     * when an widget has the focus and the `content-variable` command
     * is executed. In AlloyEditor, we want to insert a new widget,
     * not to `edit` the focused widget.
     *
     * https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_plugins_widget_definition.html#property-edit
     */
    edit() {
        this.insert();
    }

    /**
     * The method executed while initializing a widget, after a widget
     * instance is created, but before it is ready.
     *
     * https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_plugins_widget_definition.html#property-init
     */
    init() {
        this.on('focus', this.fireEditorInteraction);
        this.cancelEditEvents();
        this.readDataFromAttributes(this.element);
    }

    /**
     * Main purpose of this method is to update attributes of upcasted widgets.
     */
    readDataFromAttributes(element) {
    }

    /**
     * Fires the editorInteraction event so that AlloyEditor editor
     * UI remains visible and is updated. This method also computes
     * `selectionData.region` and the `pageX` and `pageY` properties
     * so that the add toolbar is correctly positioned on the
     * widget.
     *
     * @param {Object|String} evt this initial event info object or
     * the event name for which the `editorInteraction` is fired.
     */
    fireEditorInteraction(evt) {
        const wrapperRegion = this.getWrapperRegion();
        const name = evt.name || evt;
        const event = {
            editor: this.editor,
            target: this.element.$,
            name: 'widget' + name,
            pageX: wrapperRegion.left,
            pageY: wrapperRegion.top + wrapperRegion.height,
        };

        this.editor.focus();
        this.focus();

        this.editor.fire('editorInteraction', {
            nativeEvent: event,
            selectionData: {
                element: this.element,
                region: wrapperRegion,
            },
        });
    }

    /**
     * Returns the wrapper element region.
     *
     * @private
     * @return {Object}
     */
    getWrapperRegion() {
        const scroll = this.wrapper.getWindow().getScrollPosition();
        const region = this.wrapper.getClientRect();

        region.top += scroll.y;
        region.bottom += scroll.y;
        region.left += scroll.x;
        region.right += scroll.x;
        region.direction = CKEDITOR.SELECTION_TOP_TO_BOTTOM;

        return region;
    }

    /**
     * Cancels the widget events that trigger the `edit` event as
     * an embed widget can not be edited in a *CKEditor way*.
     */
    cancelEditEvents() {
        const cancel = (event) => event.cancel();

        this.on('doubleclick', cancel, null, null, 5);
        this.on('key', cancel, null, null, 5);
    }
};