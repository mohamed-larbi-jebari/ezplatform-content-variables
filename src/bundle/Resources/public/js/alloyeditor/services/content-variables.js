import { showErrorNotification } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/services/notification.service.js';

import {
    getBasicRequestInit,
    handleRequestResponse,
} from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/request.helper.js';

import ResponseCache from './../helpers/response.cache';

const ENDPOINT_COLLECTIONS = '/api/ezp/v2/content_variable_collection/list';
const CACHE_KEY = 'content_variable_collections';

export const loadCollections = (restInfo, callback, errorCallback) => {
    const cache = new ResponseCache(CACHE_KEY);
    if (cache.has()) {
        callback(cache.get());
        return;
    }

    const basicRequestInit = getBasicRequestInit(restInfo);
    const request = new Request(ENDPOINT_COLLECTIONS, {
        ...basicRequestInit,
        method: 'GET',
        headers: {
            ...basicRequestInit.headers,
            Accept: 'application/vnd.ez.api.ContentTypeInfoList+json',
        },
    });

    fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            cache.set(response);
            return response;
        })
        .then(callback)
        .catch((error) => {
            errorCallback(error);
            showErrorNotification(error);
        });
};
