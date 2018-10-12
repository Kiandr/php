const {resolve} = require('path');

// Load default config generator
const genDefaultConfig = require('@storybook/react/dist/server/config/defaults/webpack.config.js');

module.exports = (baseConfig, env) => {

    const config = genDefaultConfig(baseConfig, env);
    config.module.rules.push({
        test: /\.scss$/,
        loaders: ['style-loader', 'css-loader', 'sass-loader'],
        include: resolve(__dirname, './')
    });
    config.module.rules.push({
        test: /.pcss$/,
        use: {
            loader: 'postcss-loader',
            options: {
                plugins: () => [
                    require('postcss-partial-import')({
                        prefix: '',
                        extension: 'pcss'
                    }),
                    require('precss')({
                        prefix: '',
                        extension: 'pcss'
                    }),
                    require('postcss-url')({
                        url: 'rebase'
                    }),
                    require('postcss-cssnext')({
                        browsers: ['> 0%'],
                        features: {
                            customProperties: {
                                warnings: false
                            }
                        }
                    })
                ]
            }
        }
    });
    config.module.rules.push({
        test: /.vue$/,
        use: {
            loader: 'vue-loader',
            options: {
                loaders: {
                    scss: [
                        'vue-style-loader!css-loader!sass-loader',
                        {
                            loader: 'sass-resources-loader',
                            options: {
                                resources: [
                                    resolve(__dirname, './src/styles/scss/_variables.scss')
                                ]
                            }
                        }
                    ],
                },
                postcss: [require('postcss-cssnext')()]
            }
        }
    });
    config.resolve.extensions.push('css', '.scss', '.vue');
    config.resolve.alias = {vue: 'vue/dist/vue.js'};
    return config;

}