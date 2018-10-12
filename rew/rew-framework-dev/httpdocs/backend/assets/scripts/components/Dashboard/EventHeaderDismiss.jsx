import { PropTypes, PureComponent } from 'react';
import Icon from './Icon';
import URLS from 'constants/urls';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

const propTypes = {
    id: PropTypes.number.isRequired,
    mode: PropTypes.string.isRequired,
    hash:      PropTypes.string.isRequired,
    hideEvent: PropTypes.func.isRequired
};

class EventHeaderDimiss extends PureComponent {

    constructor(props) {
        super(props);

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleSubmit() {

        if (confirm('Are you sure you want to dismiss this event?')) {
            $.ajax({
                url: `${URLS.backendAjax }dashboard.php?eventDismissed`,
                type: 'POST',
                dataType: 'json',
                data: {
                    event_id: this.props.id,
                    event_mode: this.props.mode
                }
            }).done((json) => {

                if (json.errors) {
                    showErrors(json.errors);
                } else if (json.success) {
                    showSuccess(json.success);

                    // Remove Event
                    this.props.hideEvent(this.props.hash);
                }
            });
        }
    }

    render() {

        // Build Dismiss Button
        return (
            <a
                className="btn btn--ghost"
                style={{maxHeight: 50, padding: '0 0 8px'}}
                onClick={() => this.handleSubmit()}
                href="#"
            >
                <Icon icon="close" />
            </a>
        );
    }
}
EventHeaderDimiss.propTypes = propTypes;

export default EventHeaderDimiss;