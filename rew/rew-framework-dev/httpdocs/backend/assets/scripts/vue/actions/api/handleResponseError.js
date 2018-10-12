import createApiError from './createApiError';

/**
 * @type {string}
 */
const UNEXPECTED_ERROR = 'Unexpected response received from server.';

/**
 * Catch API exceptions
 * @param {Error} error
 * @throws {Error}
 */
export default function (error) {
    const { response } = error;

    //eslint-disable-next-line no-console, no-undef
    if (__DEV__) console.error('[api-response]', response);

    // Expected JSON response data
    if (error instanceof SyntaxError) {
        throw new Error(UNEXPECTED_ERROR);
    }

    // Read error from API response
    const { data } = response;
    if (data && data.error) {
        throw createApiError(data.error);
    }

    // Something has gone horribly wrong
    throw new Error(UNEXPECTED_ERROR);

}
