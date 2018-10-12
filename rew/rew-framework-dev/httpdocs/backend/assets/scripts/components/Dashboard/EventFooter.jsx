import { PropTypes } from 'react';
const EventFooter = ({ children }) => {
    return <div className="mda__ft">
        {children}
    </div>;
};
EventFooter.propTypes = {
    children: PropTypes.node
};
EventFooter.defaultProps = {
    children: null
};


export default EventFooter;