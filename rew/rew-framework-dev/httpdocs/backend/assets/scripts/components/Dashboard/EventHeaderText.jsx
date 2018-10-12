import { PropTypes } from 'react';
import TimeSince from './TimeSince';

const propTypes = {
    body: PropTypes.node,
    timestamp: PropTypes.number.isRequired
};

const defaultProps = {
    body: null
};

const EventHeaderText = ({ body, timestamp }) => {
    return <div className="article__content">
        <div className="text">
            {body}
        </div>
        <TimeSince timestamp={timestamp} />
    </div>;
};

EventHeaderText.propTypes = propTypes;
EventHeaderText.defaultProps = defaultProps;

export default EventHeaderText;