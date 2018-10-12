import { PropTypes } from 'react';
import DataItem from './DataItem';

const propTypes = {
    data: PropTypes.array.isRequired
};

const defaultProps  = {
    data: []
};

const DataList = ({data}) => {

    const dataArray = data.map((dataItem) => (
        <DataItem
            key={dataItem.id}
            title={dataItem.title}
            value={dataItem.value}
            link={dataItem.link}
        />
    ));

    // Build Data List
    return (<div className='form-preview'>
        <table>
            <tbody>
                {dataArray}
            </tbody>
        </table>
    </div>);
};

DataList.propTypes = propTypes;
DataList.defaultProps = defaultProps;

export default DataList;