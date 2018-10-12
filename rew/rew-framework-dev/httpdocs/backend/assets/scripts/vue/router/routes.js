const REW = () => import('../REW.vue');
const BackendCRMLeads = () => import('../pages/backend/crm/leads/List.vue');

const routes = [
    {
        path: '/', component: REW,
        children: [{
            path: 'backend/leads',
            component: BackendCRMLeads
        }]
    }

];

export default routes;