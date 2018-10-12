import 'selectize';
import renderSettings from './renderSettings';

export default (selectize) => {
    const extendOpts = $(selectize).data('selectize') || {};
    $(selectize).selectize({
        preload: true,
        plugins: ['remove_button'],
        maxOptions: 10000,
        render: renderSettings,
        extendOpts
    });
};
