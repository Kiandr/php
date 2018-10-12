const { protocol, host } = location;
const baseUrl = `${protocol}//${host}/`;
const backendUrl = `${baseUrl}backend/`;

export default  {
    root: baseUrl,
    backend: backendUrl,
    backendLib: `${backendUrl}inc/lib/`,
    backendAjax: `${backendUrl}inc/php/ajax/`,
    moxiemanager: `${backendUrl}inc/lib/tinymce/plugins/moxiemanager`,
    listing: `${baseUrl}listing/cms/{{value}}/`
};