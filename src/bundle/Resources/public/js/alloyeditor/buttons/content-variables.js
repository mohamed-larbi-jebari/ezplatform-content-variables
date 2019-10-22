import React from 'react';
import ReactDOM from 'react-dom';
import AlloyEditor from 'alloyeditor';
import EzButton from './../../../../../../../../../ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/js/alloyeditor/src/base/ez-button';
import ContentVariablesModal from '../modals/content-variables';

class BtnContentVariables extends EzButton {
    static get key() {
        return 'content_variables';
    }

    constructor(props) {
        super(props);

        this.container = document.querySelector('.ez-modal-wrapper');
    }

    editSource() {
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
        editor.insertText(' #' + contentVariable.identifier + '# ');
        editor.unlockSelection(true);
    }

    cancelHandler() {
        this.props.editor.get('nativeEditor').unlockSelection(true);
    }

    render() {
        return (
            <button
                className="ae-button ez-btn-ae ez-btn-ae--content-variables"
                onClick={this.editSource.bind(this)}
                tabIndex={this.props.tabIndex}
                title={Translator.trans('button.label', {}, 'content_variables')}
            >
                <svg className="ez-icon ez-btn-ae__icon">
                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#keyword" />
                </svg>
            </button>
        );
    }
}

AlloyEditor.Buttons[BtnContentVariables.key] = BtnContentVariables.BtnSource = BtnContentVariables;