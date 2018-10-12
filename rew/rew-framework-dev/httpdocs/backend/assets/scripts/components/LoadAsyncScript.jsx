import { PropTypes, Component } from 'react';

const propTypes = {
    component: PropTypes.node.isRequired,
    scriptUrl: PropTypes.string.isRequired
};

class LoadAsyncScript extends Component {
    constructor(props) {
        super(props);
        this.state = { scriptLoaded: false };
    }
    componentDidMount() {
        if (window.google == null) {
            $.ajax({
                url: this.props.scriptUrl,
                dataType: 'script'
            }).done(() => {
                this.setState({scriptLoaded: true});
            });
        } else {
            this.onMount(() => {
                this.setState({scriptLoaded: true});
            });
        }
    }
    render() {
        if (!this.state.scriptLoaded) return null;
        return this.props.component;
    }
}

LoadAsyncScript.propTypes = propTypes;

export default LoadAsyncScript;
