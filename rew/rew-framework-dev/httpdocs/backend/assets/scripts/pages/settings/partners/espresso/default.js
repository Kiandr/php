import URLS from 'constants/urls';

// Open espresso dialer on form submit
$('#login-espresso-submit').on('click', function() {
    const tpid = $('select[name="tpid"]').val();
    if (tpid.length > 0) {
        window.open(
            `${URLS.backend}settings/partners/espresso/interface/?account_manager&tpid=${tpid}`,
            'rewespresso',
            'height=650,width=1200,scrollbars=1,location=no,toolbar=no,resizable=yes'
        );
    }
});
