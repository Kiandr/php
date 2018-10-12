import axios from 'axios';
import handleResponseData from './handleResponseData';
import handleResponseError from './handleResponseError';

/**
 * API request
 * @param {string} method
 * @param {string} endpoint
 * @param {Object} params
 */
const request = (method, endpoint, data = {}, params = {}) => {
    return axios.request({
        responseType: 'json',
        url: endpoint,
        method,
        params,
        data
    })
    .then(handleResponseData) // eslint-disable-line indent
    .catch(handleResponseError); // eslint-disable-line indent
    // @TODO: fix exception issue
};

export default {
    /**
     * API DELETE request
     * @param {string} endpoint
     * @param {Object} data
     */
    delete: (endpoint, data = {}) => request('delete', endpoint, data),
    /**
     * API POST request
     * @param {string} endpoint
     * @param {Object} data
     */
    post: (endpoint, data = {}) => request('post', endpoint, data),
    /**
     * API POST request
     * @param {string} endpoint
     * @param {Object} data
     */
    put: (endpoint, data = {}) => request('put', endpoint, data),
    /**
     * API GET request
     * @param {string} endpoint
     * @param {Object} params
     */
    get: (endpoint, params = {}) => request('get', endpoint, {}, params)
};
