import { Component, PropTypes } from 'react';
import VisibilitySensor from 'react-visibility-sensor';

const propTypes = {
    loadEvents: PropTypes.func.isRequired,
};

class EventListener extends Component {

    onChange(isVisible) {
        if (isVisible) this.props.loadEvents();
    }
    
    render() {
        return <VisibilitySensor onChange={(isVisible) => this.onChange(isVisible)} />;
    }
}

EventListener.propTypes = propTypes;

export default EventListener;
