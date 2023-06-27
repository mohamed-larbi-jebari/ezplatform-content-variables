import React from 'react';
import ReactDOM from 'react-dom';
import AlloyEditor from 'alloyeditor';
import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import Widget from '@ckeditor/ckeditor5-widget/src/widget';
import { toWidget, toWidgetEditable } from '@ckeditor/ckeditor5-widget/src/utils';
import { SpecialCharacters } from '@ckeditor/ckeditor5-special-characters';
import EzWidgetButton from './../../../../../../../../../ezsystems/ezplatform-richtext/src/bundle/Resources/public/js/OnlineEditor/buttons/base/ez-widgetbutton';
import ContentVariablesModal from '../modals/content-variables';

export default class BtnContentVariableInsert extends EzWidgetButton {
    static get key() {
        return 'contentvariableinsert';
    }

    constructor(props) {
        super(props);

        this.container = document.querySelector('.ez-modal-wrapper');
    }

    openModal() {
        this.props.editor.get('nativeEditor').lockSelection();

        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

        const config = {
            restInfo: { token, siteaccess },
            onConfirm: this.confirmHandler.bind(this),
            onClose: this.cancelHandler.bind(this),
        };
        ReactDOM.render(React.createElement(ContentVariablesModal, config), this.container);
    }

    confirmHandler(contentVariable) {
        const editor = this.props.editor.get('nativeEditor');

        // Insert widget by running it as CKEditor command
        if (navigator.userAgent.indexOf('Chrome') > -1) {
            const scrollY = window.pageYOffset;
            this.execCommand();
            window.scroll(window.pageXOffset, scrollY);
        } else {
            this.execCommand();
        }

        const widget = this.getWidget();
        if (widget) {
            widget.setFocused(true);
            widget.update(contentVariable);
        }

        editor.unlockSelection(true);
    }

    cancelHandler() {
        this.props.editor.get('nativeEditor').unlockSelection(true);
    }

    render() {
        return (
            <button
                className="ae-button ez-btn-ae ez-btn-ae--content-variables"
                onClick={this.openModal.bind(this)}
                tabIndex={this.props.tabIndex}
                title={Translator.trans('button.label', {}, 'content_variables')}
            >
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ibexaadminui/img/ibexa-icons.svg#keyword" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[BtnContentVariableInsert.key] = AlloyEditor.BtnContentVariableInsert = BtnContentVariableInsert;

const ibexa = (window.ibexa = window.ibexa || {});

ibexa.richText  = ibexa.richText || {};
ibexa.richText.alloyEditor = ibexa.richText.alloyEditor || {};
ibexa.richText.alloyEditor.btnContentVariableInsert = BtnContentVariableInsert;

BtnContentVariableInsert.defaultProps = {
    command: 'content-variable',
    modifiesSelection: true,
};