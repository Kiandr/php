import { PropTypes } from 'react';

const propTypes = {
    title: PropTypes.string.isRequired,
    value: PropTypes.string,
    link: PropTypes.string
};

const defaultProps  = {
    link: null,
    value: '-',
};

const DataItem = ({title, value, link}) => {

    // Build Value
    if (link) {
        value = <a href={link}>{value}</a>;
    }

    // Build Column
    return (<tr>
        <th>{title}</th>
        <td>{value}</td>
    </tr>);
};

DataItem.propTypes = propTypes;
DataItem.defaultProps = defaultProps;

export default DataItem;