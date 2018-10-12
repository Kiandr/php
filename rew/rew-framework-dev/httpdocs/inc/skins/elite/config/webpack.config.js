const path = require('path');
const webpack = require('webpack');
const base_path = '../../../../';
const context = path.resolve(__dirname, '../');

// Plugins
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

var plugins = [];

// Build configs
const env = process.env.NODE_ENV || 'development';
const production = (env === 'development' ? false : true);

// Run build-modules.php to generate `scss/inc/_auto_vars.scss`
const WebpackShellPlugin = require('webpack-shell-plugin');
plugins.push(new WebpackShellPlugin({
    onBuildStart: ['php build-modules.php'],
}));

// Plugins
plugins.push(new ExtractTextPlugin({
    filename: `./../css/[name].${production ? 'min' : 'dev'}.css`,
    allChunks: true,
}));
plugins.push(new webpack.ProvidePlugin({
    $: 'jquery',
    jQuery: 'jquery',
    'window.jQuery': 'jquery',
}));
plugins.push(new webpack.PrefetchPlugin('./scss/app.scss'));
plugins.push(new webpack.PrefetchPlugin('./js/utils/rew_map.js'));

const defaults = {
    cache: true,
    entry: {
        app: [
            path.resolve(context, 'js/app.js'),
            path.resolve(context, 'scss/app.scss'),
            path.resolve(context, 'js/utils/rew_map.js'),
        ],
        critical: [
            path.resolve(context, 'scss/critical.scss'),
        ],
        app_builder: [
            path.resolve(context, 'scss/app_builder.scss'),
        ],
        app_directory: [
            path.resolve(context, 'node_modules/jquery-ui-bundle/jquery-ui.js'),
            path.resolve(context, 'js/app_directory.js'),
            path.resolve(context, 'scss/app_directory.scss'),
        ],
    },
    output: {
        path: path.resolve('build/js'),
        filename: `[name].${production ? 'min' : 'dev'}.js`,
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [
                    path.join(__dirname, 'js'),
                ],
                exclude: [/node_modules/],
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['es2015'],
                        cacheDirectory: true,
                    },
                },
            },
            {
                include: path.resolve(base_path + 'httpdocs/inc/js/map/'),
                loader: ['expose-loader?REWMap', 'exports-loader?REWMap'],
            },
            {
                test: /\.(scss|css)$/,
                exclude: [
                    /node_modules/,
                ],
                include: [
                    path.join(__dirname, '../scss'),
                ],
                use: ExtractTextPlugin.extract('css-loader!fast-sass-loader'),
            },
            {
                test: /\.(jpg|png|gif|svg)$/,
                loader: 'url-loader',
                options: {
                    limit: 25000,
                },
            },
            {
                test: /\.(ttf|eot|svg|woff(2)?)(\?[a-z0-9=&.]+)?$/,
                loader: 'file-loader?name=[name].[ext]&outputPath=./../fonts/',
            },
        ],
    },
    resolve: {
        alias: {
            app: path.resolve('./js/'),
            'uikit/core$': path.resolve('./node_modules/uikit/dist/js/uikit.js'),
            'uikit/comp': path.resolve(context, 'node_modules/uikit/dist/js/components'),
            jquery: path.resolve('./node_modules/jquery/dist/jquery.js'),
            map: path.resolve(base_path + 'httpdocs/inc/js/map/'),
            'rew.core': path.resolve(base_path + 'httpdocs/inc/js/'),
            audiojs: path.resolve(base_path + 'httpdocs/inc/js/vendor/audiojs/audio.min.js'),
        },
    },
    plugins: plugins,
    stats: {
        colors: true,
        hash: false,
        version: false,
        timings: true,
        assets: true,
        chunks: false,
        modules: false,
        reasons: false,
        children: false,
        source: false,
        errors: true,
        errorDetails: false,
        warnings: false,
        publicPath: false,
    },
};

if (production) {
    // defaults.module.rules.push({
    //     test: /\.js/,
    //     enforce: 'pre',
    //     exclude: [
    //         /node_modules/,
    //         path.resolve(base_path + 'httpdocs/inc/js/vendor/audiojs/audio.min.js'),
    //         path.resolve(base_path + 'httpdocs/inc/js/map/'),
    //         path.resolve(__dirname, 'breakpoints.json')
    //     ],
    //     use: [{
    //         loader: 'eslint-loader',
    //         options: {
    //             configFile: path.resolve(__dirname, '.eslintrc.js')
    //         }
    //     }]
    // });
    defaults.plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: true,
                conditionals: true,
                unused: true,
                comparisons: true,
                sequences: true,
                dead_code: true,
                evaluate: true,
                join_vars: true,
                if_return: true,
            },
            output: {
                comments: false,
            },
        }),
        new OptimizeCssAssetsPlugin({
            assetNameRegExp: /\.css$/,
            cssProcessorOptions: {discardComments: {removeAll: true}},
        }),
        new webpack.NoEmitOnErrorsPlugin()
    );
}

module.exports = [defaults];