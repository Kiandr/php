/**
 * Create API error from data
 * @param {Object} error
 * @returns {Error}
 */
export default function (error) {
    const { type, code } = error;
    let err = new Error(error.message);
    if (code > 0) err.errorCode = code;
    if (type) err.errorType = type;
    return err;
}
