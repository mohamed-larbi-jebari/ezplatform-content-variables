import React from 'react';
import BaseDefinition from './base';

export default class ContentVariableDefinition extends BaseDefinition {

    constructor(editor) {
        super(editor);
    }

    getIdentifier() {
        return 'content-variable';
    }

    isInline() {
        return true;
    }

    getTemplateDefaults() {
        return {
            collectionId: 0,
            variableId: 0,
            identifier: 'content_variable',
        };
    }

    getTemplate() {
        return `
            <span
                data-ezattribute-widget="` + this.getIdentifier() + `"
                data-ezattribute-collectionId="{collectionId}"
                data-ezattribute-variableId="{variableId}"
                data-ezattribute-identifier="{identifier}"
            >
                <svg class="ez-icon ez-icon--medium ez-icon--secondary">
                    <use
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="/bundles/ibexaadminui/img/ibexa-icons.svg#keyword"
                    />
                </svg>
                <span>{identifier}</span>
                <svg class="ez-icon ez-icon--medium ez-icon--secondary">
                    <use
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="/bundles/ibexaadminui/img/ibexa-icons.svg#keyword"
                    />
                </svg>
            </span>
        `;
    }

    upcast(element) {
        return element.attributes['data-ezattribute-widget'] === this.getIdentifier();
    }

    update(contentVariable) {
        this.setData('collectionId', contentVariable.collectionId);
        this.setData('variableId', contentVariable.id);
        this.setData('identifier', contentVariable.identifier);

        this.render(this.data);
    }

    readDataFromAttributes(element) {
        const attributesPrefix = 'data-ezattribute-';
        let isDataUpdated = false;

        Object.keys(this.defaults).forEach((key) => {
            const attributeValue = element.getAttribute(attributesPrefix + key.toLowerCase());
            if (attributeValue !== this.defaults[key] && attributeValue !== this.data[key]) {
                this.setData(key, attributeValue);
                isDataUpdated = true;
            }
        });

        if (isDataUpdated) {
            this.render(this.data);
        }
    }
};