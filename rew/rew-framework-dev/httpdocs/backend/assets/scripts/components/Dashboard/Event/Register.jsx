import { PropTypes } from 'react';
import RegisterHeader from './Register/RegisterHeader';
import RegisterBody from './Register/RegisterBody';
import RegisterFooter from './Register/RegisterFooter';

const propTypes = {
    authId: PropTypes.number.isRequired,
    hash: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    timestamp: PropTypes.number.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const Register = ({authId, hash, data, timestamp, hideEvent }) => {

    // Build Event
    return <div data-event={hash} className='mda'>
        <RegisterHeader
            authId={authId}
            lead={data.lead}
            register_id={parseInt(data.lead.id)}
            timestamp={timestamp}
            hash={hash}
            hideEvent={hideEvent}
        />
        <RegisterBody
            lead={data.lead}
        />
        <RegisterFooter
            authId={authId}
            hash={hash}
            lead={data.lead}
            hideEvent={hideEvent}
        />
    </div>;
};

Register.propTypes = propTypes;

export default Register;