import { PropTypes } from 'react';
const EventBody = ({ children }) => {
    return <div className="mda__bd">
        {children}
    </div>;
};
EventBody.propTypes = {
    children: PropTypes.node
};
EventBody.defaultProps = {
    children: null
};

export default EventBody;