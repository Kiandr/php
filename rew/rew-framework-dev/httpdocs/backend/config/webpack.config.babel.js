import path from 'path';
import glob from 'glob';
import chalk from 'chalk';
import webpack from 'webpack';
import getDirsSync from './utils/getDirsSync';
import WebpackChunkHash from 'webpack-chunk-hash';
import ManifestPlugin from 'webpack-manifest-plugin';
import ChunkManifestPlugin from 'chunk-manifest-webpack-plugin';

// - publicPath to use a CDN URL ???
// - review dependency duplication via: npm ls

// https://forum-archive.vuejs.org/topic/4059/adding-gzip-to-webpack-using-compression-plugin
// https://medium.com/@rajaraodv/two-quick-ways-to-reduce-react-apps-size-in-production-82226605771a
// https://github.com/vigneshshanmugam/optimize-js-plugin
// https://github.com/egoist/rollup-loader

// https://github.com/alexkuz/webpack-chart
// https://github.com/th0r/webpack-bundle-analyzer
// https://github.com/chrisbateman/webpack-visualizer

// https://www.npmjs.com/package/parallel-webpack
// https://www.npmjs.com/package/webpack-uglify-parallel

// https://www.npmjs.com/package/hard-source-webpack-plugin
// https://robertknight.github.io/posts/webpack-dll-plugins/
// https://github.com/webpack/webpack/tree/master/examples/dll-user

// Build configuration
let devtool = 'cheap-module-eval-source-map';
let __DEV__ = true;
let __LOG__ = false;
let minify = false;
let debug = true;
let lint = true;

// Build environment
const env = process.env.NODE_ENV || 'development';
if (env === 'production') {
    devtool = false;
    __DEV__ = false;
    __LOG__ = false;
    minify = true;
    debug = false;
    lint = false;
}

// Path configuration
const context = path.resolve(__dirname, '../');
const buildPath = path.resolve(context, 'build/js');
const scriptPath = path.resolve(context, 'assets/scripts');
const vendorPath = path.resolve(scriptPath, 'vendor');
const modulePath = path.resolve(context, 'inc/modules');
const libPath = path.resolve(context, 'inc/lib');

// Public build path
const publicPath = buildPath.replace(
    path.resolve(context, '../'),
    ''
) + '/';

// Display CLI banner
const banner = (text) => {
    const len = text.length;
    console.log('\n');
    console.log(chalk.inverse('-'.repeat(len)));
    console.log(chalk.inverse(text));
    console.log(chalk.inverse('-'.repeat(len)));
    console.log('\n');
};

// Find module scripts
let moduleScripts = {};
getDirsSync(modulePath).forEach(moduleName => {
    const moduleChunk = `modules/${moduleName}`;
    const moduleFolder = `${modulePath}/${moduleName}`;
    const moduleAssets = glob.sync(`${moduleFolder}/module.js`);
    moduleAssets.forEach(moduleAsset => {
        const assetType = path.extname(moduleAsset);
        const assetName = path.basename(moduleAsset, assetType);
        moduleScripts[`${moduleChunk}`] = moduleAsset;
    });
});

if (__LOG__) {
    // Display list of module scripts
    banner('Building module scripts:');
    for (let moduleScript in moduleScripts) {
        console.log(' - ' + chalk.underline(moduleScript));
    }
}

// Find page scripts
let pageScripts = {};
const pageAssets = glob.sync(`${scriptPath}/pages/**/*.{js,jsx}`);
pageAssets.forEach(pageAsset => {
    const assetType = path.extname(pageAsset);
    const assetName = path.basename(pageAsset, assetType);
    const assetPath = path.relative(scriptPath, path.dirname(pageAsset));
    pageScripts[`${assetPath}/${assetName}`] = pageAsset;
});

if (__LOG__) {
    // Display list of page scripts
    banner('Building page scripts:');
    for (let pageScript in pageScripts) {
        console.log(' - ' + chalk.underline(pageScript));
    }
}

if (__LOG__) {
    banner('Starting webpack:');
}

// Webpack config
module.exports = {
    cache: true,
    devtool,
    context,
    entry: {
        bundle: [
            path.resolve(scriptPath, 'app')
        ],
        vendor: [
            require.resolve('jquery'),
            require.resolve(`${vendorPath}/selectize-0.12.4.min`),
            require.resolve('jquery-ui/ui/widgets/autocomplete'),
            require.resolve('jquery-ui/ui/widgets/progressbar'),
            require.resolve('jquery-ui/ui/widgets/datepicker'),
            require.resolve('jquery-ui/ui/widgets/sortable'),
            require.resolve('jquery-ui/ui/widgets/dialog'),
            require.resolve('jquery-ui/ui/widgets/slider'),
            require.resolve('jquery-ui/ui/widgets/menu'),
            require.resolve('jquery-ui/ui/widgets/tabs'),
            require.resolve('jquery-ui-touch-punch'),
            __DEV__ && require.resolve('jquery-migrate')
        ].filter(v => !!v),
        ...moduleScripts,
        ...pageScripts
    },
    output: {
        chunkFilename: `[name].[chunkhash:10].${env === 'production' ? 'min' : 'dev'}.js`,
        filename: `[name].[chunkhash:10].${env === 'production' ? 'min' : 'dev'}.js`,
        path: buildPath,
        publicPath,
        sourceMapFilename: '[file].map'
    },
    module: {
        noParse: /(highcharts|jquery\.notify)/,
        rules: [{
            test: /\.jsx?/,
            exclude: [/node_modules/],
            use: [{
                loader: 'babel-loader',
                options: {
                    cacheDirectory: true
                }
            }]
        }, lint && {
            test: /\.(jsx?|vue)$/,
            enforce: 'pre',
            include: [scriptPath, modulePath],
            exclude: [vendorPath, /node_modules/],
            use: [{
                loader: 'eslint-loader',
                options: {
                    configFile: path.resolve(__dirname, '.eslintrc.js')
                }
            }]
        }, {
            include: path.resolve(context, '../inc/js/map'),
            loader: ['expose-loader?REWMap', 'exports-loader?REWMap']
        }, {
            include: path.resolve(scriptPath, 'legacy/fileuploader'),
            loaders: ['exports-loader?qq']
        }, {
            include: path.resolve(libPath, 'zeroclipboard/ZeroClipboard'),
            loaders: ['exports-loader?ZeroClipboard']
        }, {
            include: require.resolve('jquery-ui-timepicker-addon'),
            loaders: ['imports-loader?$.datepicker=jquery-ui/ui/widgets/datepicker']
        }, {
            include: path.resolve(libPath, 'tinymce/plugins/moxiemanager/js/moxman.api.min'),
            loaders: ['imports-loader?this=>window', 'exports-loader?window.moxman']
        }, {
            include: path.resolve(libPath, 'tinymce/tinymce.min'),
            loaders: ['imports-loader?this=>window', 'exports-loader?window.tinymce']
        }, {
            include: path.resolve(libPath, 'tinymce/jquery.tinymce.min'),
            loaders: ['imports-loader?this=>window,jQuery=jquery']
        }, {
            include: require.resolve('jquery'),
            loader: ['expose-loader?$', 'expose-loader?jQuery']
        }, {
            test: /pickadate/,
            loader: 'imports-loader?define=>false'
        }, {
            test: /\.vue$/,
            loader: 'vue-loader',
            options: {
                loaders: {
                    scss: [
                        'vue-style-loader!css-loader!sass-loader',
                        {
                            loader: 'sass-resources-loader',
                            options: {
                                resources: [
                                    path.resolve(context, 'assets/styles/sass/_variables.scss')
                                ]
                            }
                        }
                    ],
                },
                postcss: [require('postcss-cssnext')()]
            }
        },{
            test: /\.css?$/,
            loaders: ['style-loader', 'css-loader']
        },
        ].filter(v => !!v)
    },
    resolve: {
        unsafeCache: true,
        alias: {
            tinymce: path.resolve(libPath, 'tinymce/tinymce.min'),
            'jquery-tinymce': path.resolve(libPath, 'tinymce/jquery.tinymce.min'),
            'jquery-notify': path.resolve(vendorPath, 'jquery.notify-1.5.min'),
            selectize: path.resolve(vendorPath, 'selectize-0.12.4.min'),
            highcharts: path.resolve(vendorPath, 'highcharts-5.0.6.min'),
            moxiemanager: path.resolve(libPath, 'tinymce/plugins/moxiemanager/js/moxman.api.min'),
            zeroclipboard: path.resolve(libPath, 'zeroclipboard/ZeroClipboard'),
            jquery: path.resolve(context, 'node_modules/jquery/dist/jquery'),
            'vue$': 'vue/dist/vue.esm.js',
            'store': path.resolve(scriptPath, 'vue/store/store.js')
        },
        modules: [
            scriptPath,
            'node_modules'
        ],
        extensions: ['.js', '.jsx', '.vue']
    },
    stats: {
        assets: true,
        assetsSort: 'size',
        cached: true,
        children: true,
        chunks: true,
        chunkModules: true,
        chunkOPrigins: true,
        chunksSort: 'size',
        context: scriptPath,
        colors: true,
        errors: true,
        errorDetails: true,
        hash: true,
        modules: true,
        modulesSort: 'size',
        publicPath: true,
        reasons: true,
        source: true,
        timings: true,
        version: true,
        warnings: true
    },
    plugins: [
        new webpack.NoEmitOnErrorsPlugin(),
        new webpack.LoaderOptionsPlugin({
            minimize: minify,
            debug
        }),
        new webpack.DefinePlugin({
            __DEV__,
            'process.env.NODE_ENV': JSON.stringify(env),
            'window.tinyMCEPreInit': JSON.stringify({
                base: '/backend/inc/lib/tinymce',
                suffix: '.min',
                query: ''
            })
        }),
        new webpack.ProvidePlugin({
            React: 'react'
        }),
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendor',
            minChunks: function (module) {
                return module.context && (
                    module.context.indexOf(vendorPath) !== -1
                    || module.context.indexOf('node_modules') !== -1
                );
            }
        }),
        // Webpack manifest.js file (inline this)
        new webpack.optimize.CommonsChunkPlugin({
            name: 'manifest',
            minChunks: Infinity
        }),
        new webpack.HashedModuleIdsPlugin(),
        //new WebpackChunkHash(), // REWCRMP2-181
        new ManifestPlugin({
            fileName: 'assets.json',
            publicPath
        }),
        new ChunkManifestPlugin({
            filename: 'webpack.json'
        }),
        minify && new webpack.optimize.UglifyJsPlugin({
            sourceMaps: devtool && (devtool.indexOf('sourcemap') >= 0 || devtool.indexOf('source-map') >= 0),
            comments: false,
            beautify: false,
            mangle: true,
            compress: {
                warnings: false,
                screw_ie8: true,
                pure_getters: true,
                unsafe_comps: true,
                unsafe: false // BREW48-1188
            },
            // skip pre-minified libs
            //exclude: [/\.min\.js$/gi]
        })
    ].filter(v => !!v)
};
