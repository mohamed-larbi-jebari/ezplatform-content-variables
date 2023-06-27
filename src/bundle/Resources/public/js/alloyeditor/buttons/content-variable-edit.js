import React from 'react';
import ReactDOM from 'react-dom';
import AlloyEditor from 'alloyeditor';
import BtnContentVariableInsert from './content-variable-insert';
import ContentVariablesModal from '../modals/content-variables';

class BtnContentVariableEdit extends BtnContentVariableInsert {
    static get key() {
        return 'content-variable-edit';
    }

    confirmHandler(contentVariable) {
        const editor = this.props.editor.get('nativeEditor');

        if (editor.widgets.selected.length > 0) {
            const widget = editor.widgets.selected[0];
            widget.update(contentVariable);
            widget.setFocused(false);
        }

        editor.unlockSelection(true);
    }

    openModal() {
        const editor = this.props.editor.get('nativeEditor');
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;

        editor.lockSelection();
        if (editor.widgets.selected.length === 0) {
            return null;
        }

        const config = {
            restInfo: { token, siteaccess },
            onConfirm: this.confirmHandler.bind(this),
            onClose: this.cancelHandler.bind(this),
            ...editor.widgets.selected[0].data,
        };
        ReactDOM.render(React.createElement(ContentVariablesModal, config), this.container);
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
                    <use xlinkHref="/bundles/ibexaadminui/img/ibexa-icons.svg#edit" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[BtnContentVariableEdit.key] = AlloyEditor.BtnContentVariableEdit = BtnContentVariableEdit;

const eZ = (window.eZ = window.eZ || {});

eZ.ezAlloyEditor = eZ.ezAlloyEditor || {};
eZ.ezAlloyEditor.btnContentVariableEdit = BtnContentVariableEdit;
