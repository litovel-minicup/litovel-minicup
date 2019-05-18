const path = require('path');
const webpack = require('webpack');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');


module.exports = {
    entry: {
        MatchOnline: path.resolve(__dirname, 'www/assets/vue/MatchOnline/main.js'),
        CategoryOnline: path.resolve(__dirname, 'www/assets/vue/CategoryOnline/main.js'),
        AdminPhotoPut: path.resolve(__dirname, 'www/assets/vue/AdminPhotoPut/main.js'),
        LiveBar: path.resolve(__dirname, 'www/assets/vue/LiveBar/main.js'),
        InstagramStories: path.resolve(__dirname, 'www/assets/vue/InstagramStories/main.js'),
    },
    output: {
        path: path.resolve(__dirname, 'www/assets/build/'),
        publicPath: '/assets/build/',
        filename: '[name].build.js'
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ],
            }, {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {}
                    // other vue-loader options go here
                }
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.(png|jpg|gif|svg|woff|woff2|eot|ttf)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]?[hash]'
                }
            },
            {
                test: /\.scss$/,
                use: ['style-loader', 'css-loader', 'sass-loader'],

                // postcss: {},
            },

        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery",
        })
    ],
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        },
        extensions: ['*', '.js', '.vue', '.json']
    },
    performance: {
        hints: false
    },
    devServer: {
        proxy: {
            "**": "http://localhost:8000/"
        }
    }
};
