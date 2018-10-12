import { PropTypes } from 'react';
const propTypes = {
    image: PropTypes.string,
    defaultClass: PropTypes.string,
    defaultText: PropTypes.string,
    size: PropTypes.oneOf(['tiny', 'small', 'medium', 'large'])
};
const defaultProps = {
    image: null,
    defaultClass: null,
    defaultText: null,
    size: 'medium'
};

const EventHeaderImage = ({ image, defaultClass, defaultText, size }) => {

    if (!image){
        return <div className={'mda__thumb marR thumb thumb--' + size + ' -bg-' + defaultClass}>
            <span className="thumb__label">
                {defaultText}
            </span>
        </div>;
    } else {
        return <div className={'mda__thumb marR thumb thumb--' + size}>
            <img
                src={image}
            />
        </div>;
    }
};

EventHeaderImage.propTypes = propTypes;
EventHeaderImage.defaultProps = defaultProps;

export default EventHeaderImage;
