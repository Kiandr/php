(function() {
    'use strict';

    /*! modernizr 3.5.0 (Custom Build) | MIT *
     * https://modernizr.com/download/?-csspositionsticky-objectfit-setclasses !*/
    !function(e,n,t){function r(e,n){return typeof e===n;}function o(){var e,n,t,o,i,s,a;for(var l in C)if(C.hasOwnProperty(l)){if(e=[],n=C[l],n.name&&(e.push(n.name.toLowerCase()),n.options&&n.options.aliases&&n.options.aliases.length))for(t=0;t<n.options.aliases.length;t++)e.push(n.options.aliases[t].toLowerCase());for(o=r(n.fn,'function')?n.fn():n.fn,i=0;i<e.length;i++)s=e[i],a=s.split('.'),1===a.length?Modernizr[a[0]]=o:(!Modernizr[a[0]]||Modernizr[a[0]]instanceof Boolean||(Modernizr[a[0]]=new Boolean(Modernizr[a[0]])),Modernizr[a[0]][a[1]]=o),h.push((o?'':'no-')+a.join('-'));}}function i(e){var n=x.className,t=Modernizr._config.classPrefix||'';if(S&&(n=n.baseVal),Modernizr._config.enableJSClass){var r=new RegExp('(^|\\s)'+t+'no-js(\\s|$)');n=n.replace(r,'$1'+t+'js$2');}Modernizr._config.enableClasses&&(n+=' '+t+e.join(' '+t),S?x.className.baseVal=n:x.className=n);}function s(){return'function'!=typeof n.createElement?n.createElement(arguments[0]):S?n.createElementNS.call(n,'http://www.w3.org/2000/svg',arguments[0]):n.createElement.apply(n,arguments);}function a(e){return e.replace(/([a-z])-([a-z])/g,function(e,n,t){return n+t.toUpperCase();}).replace(/^-/,'');}function l(e,n){return!!~(''+e).indexOf(n);}function f(e,n){return function(){return e.apply(n,arguments);};}function u(e,n,t){var o;for(var i in e)if(e[i]in n)return t===!1?e[i]:(o=n[e[i]],r(o,'function')?f(o,t||n):o);return!1;}function c(n,t,r){var o;if('getComputedStyle'in e){o=getComputedStyle.call(e,n,t);var i=e.console;if(null!==o)r&&(o=o.getPropertyValue(r));else if(i){var s=i.error?'error':'log';i[s].call(i,'getComputedStyle returning null, its possible modernizr test results are inaccurate');}}else o=!t&&n.currentStyle&&n.currentStyle[r];return o;}function p(e){return e.replace(/([A-Z])/g,function(e,n){return'-'+n.toLowerCase();}).replace(/^ms-/,'-ms-');}function d(){var e=n.body;return e||(e=s(S?'svg':'body'),e.fake=!0),e;}function m(e,t,r,o){var i,a,l,f,u='modernizr',c=s('div'),p=d();if(parseInt(r,10))for(;r--;)l=s('div'),l.id=o?o[r]:u+(r+1),c.appendChild(l);return i=s('style'),i.type='text/css',i.id='s'+u,(p.fake?p:c).appendChild(i),p.appendChild(c),i.styleSheet?i.styleSheet.cssText=e:i.appendChild(n.createTextNode(e)),c.id=u,p.fake&&(p.style.background='',p.style.overflow='hidden',f=x.style.overflow,x.style.overflow='hidden',x.appendChild(p)),a=t(c,e),p.fake?(p.parentNode.removeChild(p),x.style.overflow=f,x.offsetHeight):c.parentNode.removeChild(c),!!a;}function v(n,r){var o=n.length;if('CSS'in e&&'supports'in e.CSS){for(;o--;)if(e.CSS.supports(p(n[o]),r))return!0;return!1;}if('CSSSupportsRule'in e){for(var i=[];o--;)i.push('('+p(n[o])+':'+r+')');return i=i.join(' or '),m('@supports ('+i+') { #modernizr { position: absolute; } }',function(e){return'absolute'==c(e,null,'position');});}return t;}function y(e,n,o,i){function f(){c&&(delete T.style,delete T.modElem);}if(i=r(i,'undefined')?!1:i,!r(o,'undefined')){var u=v(e,o);if(!r(u,'undefined'))return u;}for(var c,p,d,m,y,g=['modernizr','tspan','samp'];!T.style&&g.length;)c=!0,T.modElem=s(g.shift()),T.style=T.modElem.style;for(d=e.length,p=0;d>p;p++)if(m=e[p],y=T.style[m],l(m,'-')&&(m=a(m)),T.style[m]!==t){if(i||r(o,'undefined'))return f(),'pfx'==n?m:!0;try{T.style[m]=o;}catch(h){}if(T.style[m]!=y)return f(),'pfx'==n?m:!0;}return f(),!1;}function g(e,n,t,o,i){var s=e.charAt(0).toUpperCase()+e.slice(1),a=(e+' '+j.join(s+' ')+s).split(' ');return r(n,'string')||r(n,'undefined')?y(a,n,o,i):(a=(e+' '+E.join(s+' ')+s).split(' '),u(a,n,t));}var h=[],C=[],_={_version:'3.5.0',_config:{classPrefix:'',enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,n){var t=this;setTimeout(function(){n(t[e]);},0);},addTest:function(e,n,t){C.push({name:e,fn:n,options:t});},addAsyncTest:function(e){C.push({name:null,fn:e});}},Modernizr=function(){};Modernizr.prototype=_,Modernizr=new Modernizr;var x=n.documentElement,S='svg'===x.nodeName.toLowerCase(),w=_._config.usePrefixes?' -webkit- -moz- -o- -ms- '.split(' '):['',''];_._prefixes=w,Modernizr.addTest('csspositionsticky',function(){var e='position:',n='sticky',t=s('a'),r=t.style;return r.cssText=e+w.join(n+';'+e).slice(0,-e.length),-1!==r.position.indexOf(n);});var b='Moz O ms Webkit',j=_._config.usePrefixes?b.split(' '):[];_._cssomPrefixes=j;var z=function(n){var r,o=w.length,i=e.CSSRule;if('undefined'==typeof i)return t;if(!n)return!1;if(n=n.replace(/^@/,''),r=n.replace(/-/g,'_').toUpperCase()+'_RULE',r in i)return'@'+n;for(var s=0;o>s;s++){var a=w[s],l=a.toUpperCase()+'_'+r;if(l in i)return'@-'+a.toLowerCase()+'-'+n;}return!1;};_.atRule=z;var E=_._config.usePrefixes?b.toLowerCase().split(' '):[];_._domPrefixes=E;var P={elem:s('modernizr')};Modernizr._q.push(function(){delete P.elem;});var T={style:P.elem.style};Modernizr._q.unshift(function(){delete T.style;}),_.testAllProps=g;var k=_.prefixed=function(e,n,t){return 0===e.indexOf('@')?z(e):(-1!=e.indexOf('-')&&(e=a(e)),n?g(e,n,t):g(e,'pfx'));};Modernizr.addTest('objectfit',!!k('objectFit'),{aliases:['object-fit']}),o(),i(h),delete _.addTest,delete _.addAsyncTest;for(var N=0;N<Modernizr._q.length;N++)Modernizr._q[N]();e.Modernizr=Modernizr;}(window,document);

    if ( ! Modernizr.objectfit ) {
        $('.thumb, .photoGrid2A, .hero__bg-content, .photoGrid3A, .photoGrid2A, .photoGrid1A, .hero__bg').each(function () {
            var $container = $(this),
                buImgUrl = $container.find('img').data('src'),
                imgUrl = $container.find('img').prop('src');
    
            if (imgUrl) {
                if(buImgUrl) {
                    $container
                        .css('backgroundImage', 'url(' + buImgUrl + ')')
                        .addClass('compat-object-fit');
                } else {
                    $container
                        .css('backgroundImage', 'url(' + imgUrl + ')')
                        .addClass('compat-object-fit');
                }
            }  
        });
    }

    /**
     * Slideout off-screen nav menu
     * <div id="nav" class="inactive"></div>
     * <a id="nav-toggle"></a>
     * <a id="nav-close"></a>
     */
    var transitionEvent = (function () {
        var t, el = document.createElement('fakeelement');
        var transitions = {
            'transition'      : 'transitionend',
            'OTransition'     : 'oTransitionEnd',
            'MozTransition'   : 'transitionend',
            'WebkitTransition': 'webkitTransitionEnd'
        };
        for (t in transitions){
            if (el.style[t] !== undefined){
                return transitions[t];
            }
        }
    })();

    // Prevent bubbling of off-screen nav clicks
    var $nav = $('#nav').on('click', function (e) {
            e.stopPropagation();
        }), showNav = function () {
            $nav.removeClass('inactive');
            $nav.attr('aria-hidden', 'false');
            $('body').addClass('nav-open');
            $nav.addClass('animating').removeClass('nav-done').one(transitionEvent, function () {
                $nav.addClass('nav-done').removeClass('animating');
            });
        }, hideNav = function () {
            $nav.addClass('inactive');
            $nav.attr('aria-hidden', 'true');
            $('body').removeClass('nav-open');
            $nav.addClass('animating').removeClass('nav-done').one(transitionEvent, function () {
                $nav.addClass('nav-done').removeClass('animating');
            });
        };

    // Toggle display of off-screen navigation
    $('#nav-toggle').on('click', function (e) {
        e.stopPropagation();
        if ($nav.hasClass('inactive')) {
            showNav();
            $(document).on('click', function () {
                hideNav();
            });
        } else {
            hideNav();
        }
    });

    // Close off-screen navigation menu
    $('#nav-close').on('click', function () {
        hideNav();
    });

    /**
     * Toggle sidebar navigation for content pages
     * <div id="sidebar-nav"></div>
     * <a id="sidebar-toggle"></a>
     */
    var $sideNav = $('#sidebar-nav');
    $('#sidebar-toggle').on('click', function () {
        var $icon = $(this).find('i.fa');
        if ($sideNav.is('.active')) {
            $icon.removeClass('fa-caret-up').addClass('fa-caret-down');
            $sideNav.removeClass('active');
        } else {
            $icon.removeClass('fa-caret-down').addClass('fa-caret-up');
            $sideNav.addClass('active');
        }
    });

})();