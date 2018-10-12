import { PropTypes, PureComponent } from 'react';
import EventFooter from '../../EventFooter';
import Icon from '../../Icon';
import URLS from 'constants/urls';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

const propTypes = {
    hash:      PropTypes.string.isRequired,
    lead:      PropTypes.object.isRequired,
    listing:   PropTypes.object,
    form:      PropTypes.object.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const defaultProps  = {
    listing: null,
};

class InquiryFooter extends PureComponent {

    constructor(props) {
        super(props);
        this.state = {value: ''};

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(event) {
        this.setState({ value: event.target.value});
    }

    handleSubmit() {
        const data = {
            id: this.props.lead.id,
            subject: 'RE: ' + (this.props.listing != null ? this.props.listing.name : this.props.form.name),
            message: this.state.value,
            leads : [this.props.lead.id]
        };

        $.ajax({
            url: `${URLS.backendAjax }json.php?action=message`,
            type: 'POST',
            dataType: 'json',
            data: data
        }).done((json) => {

            if (json.errors) {
                showErrors(json.errors);
            } else if (json.success) {

                // Record inquiry response
                $.ajax({
                    url: `${URLS.backendAjax }dashboard.php?eventResponse`,
                    type: 'POST',
                    dataType: 'json',
                    data: {event: this.props.form.id}
                }).always(() => {

                    showSuccess(json.success);

                    // Remove Event
                    this.props.hideEvent(this.props.hash);
                });
            }
        }).fail(() => {
            showErrors([
                'An Error occurred while replying to this lead! Please try again later.'
            ]);
        });
    }

    render() {

        // Build Footer Element
        return (<EventFooter>
            <span className='input w1/1'>
                <input
                    type='text'
                    onChange={(e) => this.handleChange(e)}
                    placeholder='Write a response...'
                />
                <a
                    className='btn btn--email'
                    onClick={() => this.handleSubmit()}
                >
                    <Icon icon="email" />
                </a>
            </span>
        </EventFooter>);
    }
}

InquiryFooter.propTypes = propTypes;
InquiryFooter.defaultProps = defaultProps;

export default InquiryFooter;