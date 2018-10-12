const $subpermissionCheckboxes = $('input[type="checkbox"][data-subpermission!=""]');
const $superpermissionCheckboxes = $('input[type="checkbox"][data-superpermission!=""]');

$subpermissionCheckboxes.on('change', function() {
    if (this.checked) {
        const owningSuperpermission = $(this).data('subpermission');
        $(`input[type="checkbox"][data-superpermission="${owningSuperpermission}"]`).prop('checked', true);
        if ($(this).data('full-permissions')) {
            $(`input[type="checkbox"][data-subpermission="${owningSuperpermission}"]`).prop('checked', true);
        }
    }
});

$superpermissionCheckboxes.on('change', function() {
    if (!this.checked) {
        const ownedSubpermission = $(this).data('superpermission');
        $(`input[type="checkbox"][data-subpermission="${ownedSubpermission}"]`).prop('checked', false);
    }
});

const togglePermissionsSection = (() => { // eslint-disable-line no-unused-vars
    const container = document.getElementById('permissionList');
    container.addEventListener('click', function(e) {
        const sections = Array.from(document.querySelectorAll('.field--permissions'));
        sections.forEach(function(section) {
            if (section.contains(e.target) && e.target.nodeName == 'H3') section.classList.toggle('is-active');
        });
    });
})();

