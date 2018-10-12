import { PropTypes } from 'react';
const propTypes = {
    timestamp: PropTypes.number.isRequired
};

const TimeSince = ({ timestamp }) => {

    const currentTimestamp = new Date().getTime() / 1000;
    const timeSince = currentTimestamp - timestamp;
    let timeSinceString = '';

    if (timeSince < 60) {
        timeSinceString = 'Just now';
    } else if (timeSince < 3600) {
        const minutes = Math.floor(timeSince/60);
        timeSinceString = minutes + ' minutes ago';
    } else {
        const hours = Math.floor(timeSince/3600);
        timeSinceString = hours + ' hours ago';
    }

    return <div className="text text--mute">
        {timeSinceString}
    </div>;
};

TimeSince.propTypes = propTypes;

export default TimeSince;