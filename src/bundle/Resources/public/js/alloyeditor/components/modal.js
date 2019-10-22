import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

const MODAL_OPEN_BODY_CLASS = 'modal-open';
const MODAL_BACKDROP_CLASS = 'modal-backdrop show';

export default class Modal extends Component {
    constructor(props) {
        super(props);

        this.elements = {
            'body': document.body,
            'backdrop': document.createElement('div'),
        };
    }

    componentDidMount() {
        this.elements.body.className += ' ' + MODAL_OPEN_BODY_CLASS;
        this.elements.backdrop.className = MODAL_BACKDROP_CLASS;
        this.elements.body.appendChild(this.elements.backdrop);
    }

    componentWillUnmount() {
        this.elements.body.classList.remove(MODAL_OPEN_BODY_CLASS);
        this.elements.body.removeChild(this.elements.backdrop);
    }

    close() {
        ReactDOM.unmountComponentAtNode(ReactDOM.findDOMNode(this).parentNode);
        this.props.onClose();
    }

    confirm() {
        ReactDOM.unmountComponentAtNode(ReactDOM.findDOMNode(this).parentNode);
        this.props.onConfirm();
    }

    render() {
        return(
            <div className="modal ez-modal show" tabIndex="-1" role="dialog" style={{display: 'block'}}>
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h3 className="modal-title">{this.props.title}</h3>
                            <button type="button" className="close" onClick={this.close.bind(this)}>
                                <svg className="ez-icon ez-icon--medium" aria-hidden="true">
                                    <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#discard"/>
                                </svg>
                            </button>
                        </div>
                        <div className="modal-body">
                            {this.props.children}
                        </div>
                        {this.renderModalFooter()}
                    </div>
                </div>
            </div>
        );
    }

    renderModalFooter() {
        const selectButtonProps = {};
        if (!this.props.isSelectButtonEnabled()) {
            selectButtonProps.disabled = 'disabled';
        }

        return (
            <div className="modal-footer justify-content-center">
                <button type="button" className="btn btn-dark" onClick={this.close.bind(this)}>{this.props.cancelLabel}</button>
                <button type="button" className="btn btn-primary font-weight-bold" {...selectButtonProps} onClick={this.confirm.bind(this)}>{this.props.selectLabel}</button>
            </div>
        );
    }
}

Modal.propTypes = {
    title: PropTypes.string.isRequired,
    cancelLabel: PropTypes.string.isRequired,
    selectLabel: PropTypes.string.isRequired,
    isSelectButtonEnabled: PropTypes.func.isRequired,
    onClose: PropTypes.func.isRequired,
    onConfirm: PropTypes.func.isRequired,
};