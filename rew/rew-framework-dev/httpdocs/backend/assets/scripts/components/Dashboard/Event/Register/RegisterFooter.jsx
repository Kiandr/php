import { PropTypes, PureComponent } from 'react';
import Select from 'react-select';
import EventFooter from '../../EventFooter';
import Icon from '../../Icon';
import URLS from 'constants/urls';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

const propTypes = {
    authId: PropTypes.number.isRequired,
    hash:      PropTypes.string.isRequired,
    lead:      PropTypes.object.isRequired,
    hideEvent: PropTypes.func.isRequired
};

const defaultProps  = {
    listing: null,
};

class RegisterFooter extends PureComponent {

    constructor(props) {
        super(props);
        this.state = {assigning: false};
    }

    switchMode() {
        this.setState({ assigning: !this.state.assigning});
    }

    handleSelect (agent) {

        // AJAX Data
        const data = {
            ajax: true,
            agent_id: agent.id,
            leads : [this.props.lead.id]
        };

        // AJAX Call
        this.currentPoll =$.ajax({
            url: `${URLS.backendAjax }json.php?action=assign`,
            type: 'POST',
            dataType: 'json',
            data: data
        }).done((json) => {

            if (json.errors) {
                // Error Notifications
                showErrors(json.errors);
            } else if (json.success) {

                showSuccess(json.success);

                // Remove Event
                this.props.hideEvent(this.props.hash, this.props.lead.id);
            }
        }).fail(() => {
            showErrors([
                'An Error occured while assigning this lead! Please try again later.'
            ]);
        });
    }

    handleClick() {

        // AJAX Data
        const data = {
            'status': 'accepted',
            leads : [this.props.lead.id]
        };

        // AJAX Call
        this.currentPoll =$.ajax({
            url: `${URLS.backendAjax }json.php?action=status`,
            type: 'POST',
            dataType: 'json',
            data: data
        }).done((json) => {

            if (json.errors) {
                // Error Notifications
                showErrors(json.errors);
            } else if (json.success) {

                showSuccess(json.success);

                // Remove Event
                this.props.hideEvent(this.props.hash);
            }
        }).fail(() => {
            showErrors([
                'An Error occurred while accepting this lead! Please try again later.'
            ]);
        });
    }

    getAcceptOrAssign() {
        if (this.props.lead.agent != this.props.authId || (this.props.authId == 1 && this.props.lead.status == 'unassigned')) {
            return <a
                onClick={() => this.switchMode()}
                className='btn btn--positive'
                style={{width: 'calc(50% - 12px)', float: 'left'}}
            >
                <Icon icon="agent" />
                {this.props.lead.agent != 1 ? 'Reassign' : ' Assign'}
            </a>;
        } else {
            return <a
                onClick={() => this.handleClick()}
                className='btn btn--positive'
                style={{width: 'calc(50% - 12px)', float: 'left'}}
            >
                <Icon icon="check" />
                {' Accept'}
            </a>;
        }
    }

    getAgents(input) {
        return $.ajax({
            url: `${URLS.backendAjax }dashboard.php?fetchAgent&lead=${this.props.lead.id}&input=${input}`,
            type: 'GET',
            dataType: 'json'
        }).then((json) => {
            return { options: json.data };
        });
    }

    renderAgent(agent) {

        if (!agent.image) {
            return <div>
                <div className={'mda__thumb marR thumb thumb--tiny -bg-' + agent.defaultClass}>
                    <span className="thumb__label">
                        {agent.defaultText}
                    </span>
                </div>
                <span
                    className='token__label'
                >
                    {agent.name}
                </span>
            </div>;
        } else {
            return <div>
                <div className={'mda__thumb marR thumb thumb--tiny'}>
                    <img
                        src={agent.image}
                    />
                </div>
                <span
                    className='token__label'
                >
                    {agent.name}
                </span>
            </div>;
        }
    }

    render() {

        // Reviewing Lead
        if (!this.state.assigning) {

            // Build Footer Element
            return (<EventFooter>
                <div
                    style={{float: 'none'}}
                    className='btns'
                >
                    <a
                        href={this.props.lead.link}
                        className='btn'
                        style={{width: 'calc(50% - 12px)', float: 'right'}}
                    >
                        {'Review'}
                    </a>
                    {this.getAcceptOrAssign()}
                </div>
            </EventFooter>);

        } else {

            const menuStyle = {overflow: 'hidden'};

            const arrowRenderer  = () => {
                return <Icon icon="search" />;
            };

            // Build Footer Element
            return <EventFooter>
                <div className="mobileExpand">
                    <span className='select w1/1'>
                        <Select.Async
                            autoload={true}
                            ignoreCase={false}
                            scrollMenuIntoView={true}
                            menuStyle={menuStyle}
                            filterOptions={false}
                            placeholder='Search Agents...'
                            loadOptions={(input) => this.getAgents(input)}
                            optionRenderer={this.renderAgent}
                            onChange={(value) => this.handleSelect(value)}
                            tabSelectsValue={false}
                            arrowRenderer={arrowRenderer}
                        />
                    </span>
                </div>
            </EventFooter>;
        }
    }
}

RegisterFooter.propTypes = propTypes;
RegisterFooter.defaultProps = defaultProps;

export default RegisterFooter;
