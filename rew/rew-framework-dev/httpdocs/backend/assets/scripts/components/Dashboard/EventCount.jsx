import { PropTypes } from 'react';
import FILTERS from 'constants/Dashboard/filters';

const propTypes = {
    eventCount: PropTypes.number.isRequired,
    eventName:  PropTypes.string.isRequired,
    filterMode: PropTypes.oneOf(FILTERS.list),
    setFilter:  PropTypes.func.isRequired,
    active:     PropTypes.bool.isRequired
};

const defaultProps  = {
    filterMode: null
};

const EventCount = ({ eventCount, eventName, filterMode, setFilter, active }) => {
    return <div
        className={active ? 'iS-col active' : 'iS-col'}
        onClick={() => setFilter(filterMode)}
    >
        <i>{eventCount}</i>
        <small>{eventName}</small>
    </div>;
};

EventCount.propTypes = propTypes;
EventCount.defaultProps = defaultProps;

export default EventCount;
