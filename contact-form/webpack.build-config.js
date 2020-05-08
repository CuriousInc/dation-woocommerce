/* eslint-disable global-require, no-unused-vars, import/no-extraneous-dependencies */
const webpack = require('webpack');
const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

const isDevelopment = process.env.NODE_ENV !== 'production';

const developmentPlugins = [
  new HtmlWebpackPlugin({
    template: require('html-webpack-template'),
    inject: false,
    appMountId: 'app',
    filename: 'index.html',
    bodyHtmlSnippet: `
    <script type="application/json" data-props="app">
    {
      "location": "Amsterdam"
    }
    </script>
    `,
  }),
];


const config = {
  mode: isDevelopment ? 'development' : 'production',
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/react'],
          },
        },
        exclude: /node_modules/,
      },
      {
        test: /\.css$/,
        use: [
          'style-loader',
          'css-loader',
        ],
        exclude: /\.module\.css$/,
      },
      {
        test: /\.css$/,
        use: [
          'style-loader',
          {
            loader: 'css-loader',
            options: {
              importLoaders: 1,
              modules: true,
            },
          },
        ],
        include: /\.module\.css$/,
      },
      {
        test: /\.scss$/,
        use: [
          { loader: 'style-loader' },
          { loader: 'css-loader' },
          {
            loader: 'sass-loader',
            options: {
              sassOptions: {
                outputStyle: 'compressed',
              },
            },
          },
        ],
      },
      {
        test: /\.svg$/,
        use: 'file-loader',
      },
      {
        test: /\.png$/,
        use: [
          {
            loader: 'url-loader',
            options: {
              mimetype: 'image/png',
            },
          },
        ],
      },
    ],
  },
  optimization: {
    ...(!isDevelopment && {
      minimize: true,
      minimizer: [new TerserPlugin()],
    }),
  },
  resolve: {
    extensions: [
      '.js',
    ],
  },
  plugins: [
    ...(isDevelopment ? [...developmentPlugins] : []),
  ],
};

module.exports = config;
