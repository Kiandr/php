import { PropTypes } from 'react';
const propTypes = {
    image: PropTypes.string.isRequired,
    header: PropTypes.string,
    body: PropTypes.node
};

const defaultProps  = {
    header: '',
    body: ''
};

const HeroPad = ({ image, header, body }) => {

    return <div className="hero pad">
        <img src={image} />
        <h2>{header}</h2>
        {body}
    </div>;
};

HeroPad.propTypes = propTypes;
HeroPad.defaultProps = defaultProps;

export default HeroPad;