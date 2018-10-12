const REGISTER_FILTER = 'register';
const INQUIRY_FILTER = 'inquiry';
const MESSAGE_FILTER = 'message';
const SELLING_FILTER = 'selling';
const SHOWING_FILTER = 'showing';

export default  {
    register: REGISTER_FILTER,
    inquiry:  INQUIRY_FILTER,
    message:  MESSAGE_FILTER,
    selling:  SELLING_FILTER,
    showing:  SHOWING_FILTER,
    list:     [REGISTER_FILTER, INQUIRY_FILTER, MESSAGE_FILTER, SELLING_FILTER, SHOWING_FILTER]
};