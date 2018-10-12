/**
 * Very useful function to format numbers.
 * Number.prototype.format (c, d, t)
 *  - c, count after decimal
 *  - d, decimal separater
 *  - t, thousands separater
 * Example: (10000).format(2, '.', ','); // 10,000.00
 */
Number.prototype.format = function(c, d, t){
    var n = this, c = isNaN(c = Math.abs(c)) ? 0 : c, d = d == undefined ? '.' : d, t = t == undefined ? ',' : t, s = n < 0 ? '-' : '', i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '', j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
};

/**
 * String.prototype.trim
 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim#Polyfill
 */
if (!String.prototype.trim) {
    (function() {
        var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
        String.prototype.trim = function() {
		  return this.replace(rtrim, '');
        };
    })();
}

/**
 * openPage
 * @deprecated
 */
var openPage = function (url) {
    $.Window({
        iframe : url
    });
};