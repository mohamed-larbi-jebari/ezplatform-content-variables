import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { loadCollections } from '../services/content-variables';
import Modal from '../components/modal';
import ModalSelect from '../components/modal.select';

import { showErrorNotification } from './../../../../../../../../../ezsystems/ezplatform-admin-ui-modules/src/modules/common/services/notification.service';

export default class ContentVariablesModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isDataLoading: true,
            data: new Map(),
            collectionId: this.props.collectionId ? parseInt(this.props.collectionId) : null,
            variableId: this.props.variableId ? parseInt(this.props.variableId) : null,
        };
    }

    componentDidMount() {
        const { restInfo } = this.props;
        const errorCallback = () => {
            this.setState({isDataLoading: false});
        };

        const collectionsLoaded = new Promise((resolve) => loadCollections(restInfo, resolve, errorCallback));

        collectionsLoaded
            .then(({ CollectionList }) => {
                const collections = this.handleCollections(CollectionList.Collection);
                let newState = {
                    isDataLoading: false,
                    data: collections,
                };

                if (!collections.has(this.state.collectionId)) {
                    const defaultCollection = collections.values().next().value;
                    newState = Object.assign(newState, {
                        collectionId: defaultCollection.id,
                        variableId: defaultCollection.variables.values().next().value.id,
                    });
                }

                this.setState(newState);
            })
            .catch((error) => {
                this.setState({isDataLoading: false});
                showErrorNotification(error);
            });
    }

    handleCollections(collections) {
        const data = new Map();
        Object.entries(collections).map(([key, collection]) => {
            const variables = collection.Variables;
            if (variables.length === 0) {
                return;
            }

            const variablesMap = new Map();
            variables.forEach(function(variable) {
                variablesMap.set(variable._id, {
                    id: variable._id,
                    name: variable._name,
                    identifier: variable._identifier,
                    collectionId: collection._id,
                });
            });

            data.set(collection._id, {
                id: collection._id,
                name: collection._name,
                variables: variablesMap,
            });
        });

        return data
    }

    selectContentVariable() {
        this.props.onConfirm(this.getSelectedVariable())
    }

    getSelectedVariable() {
        return this.state.data.get(this.state.collectionId).variables.get(this.state.variableId);
    }


    render() {
        const config = {
            title: Translator.trans('modal.header', {}, 'content_variables'),
            cancelLabel: Translator.trans('modal.buttons.cancel.label', {}, 'content_variables'),
            selectLabel: Translator.trans('modal.buttons.select.label', {}, 'content_variables'),
            isSelectButtonEnabled: () => {
                return this.state.data.size > 0;
            },
            onClose: this.props.onClose,
            onConfirm: this.selectContentVariable.bind(this),
        };
        return(
            <Modal {...config}>
                {this.renderModalContent()}
            </Modal>
        );
    }

    renderModalContent() {
        if (this.state.isDataLoading) {
            return (
                <div className="text-center">
                    <svg className="ez-icon ez-spin ez-icon-x2 ez-icon-spinner">
                        <use xlinkHref="/bundles/ezplatformadminui/img/ez-icons.svg#spinner"/>
                    </svg>
                </div>
            );
        }

        if (this.state.data.size === 0) {
            return (
                <div className="justify-content-center">
                    {Translator.trans('modal.no_collections', {}, 'content_variables')}
                </div>
            );
        }

        const collectionSelectConfig = {
            title: Translator.trans('modal.form.select.collection', {}, 'content_variables'),
            selectId: 'content_variables_modal_collection',
            options: this.state.data,
            selectedOptionId: this.state.collectionId,
            onChange: (selectedId) => {
                const collectionId = parseInt(selectedId);

                this.setState((state) => {
                    return {
                        collectionId: collectionId,
                        variableId: state.data.get(collectionId).variables.values().next().value.id
                    };
                });
            },
        };
        const variableSelectConfig = {
            title: Translator.trans('modal.form.select.variable', {}, 'content_variables'),
            selectId: 'content_variables_modal_variable',
            options: this.state.data.get(this.state.collectionId).variables,
            selectedOptionId: this.state.variableId,
            onChange: (selectedId) => {
                this.setState({variableId: parseInt(selectedId)});
            },
        };
        return (
            <div>
                <ModalSelect {...collectionSelectConfig}/>
                <ModalSelect {...variableSelectConfig}/>
            </div>
        );
    }
}

ContentVariablesModal.propTypes = {
    onConfirm: PropTypes.func.isRequired,
    onClose: PropTypes.func.isRequired,
};