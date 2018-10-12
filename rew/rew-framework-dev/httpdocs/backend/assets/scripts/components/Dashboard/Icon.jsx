import { PropTypes } from 'react';
const propTypes = {
    icon: PropTypes.string.isRequired,
};

const Icon = ({ icon }) => {
    return <svg className={'icon icon-'+icon+' mar0'}>
        <use 
            xmlnsXlink='http://www.w3.org/1999/xlink'
            xlinkHref={'/backend/img/icos.svg#icon-'+icon}
        />
    </svg>;
};

Icon.propTypes = propTypes;

export default Icon;