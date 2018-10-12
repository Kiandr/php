// Load SMS attachment code
import 'common/attach-media';

let getParams = {};

location.search.substr(1).split('&').forEach(function(item) {
    getParams[item.split('=')[0]] = item.split('=')[1];
});

if('success' in getParams && 'popup' in getParams) {
    setTimeout(function() {
        parent.location.reload();
    }, 4000);
}
