const Encore = require('@symfony/webpack-encore');
const glob = require('glob-all')
const PurgecssPlugin = require('purgecss-webpack-plugin')

Encore
  .setOutputPath('build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/js/app.js')
  .disableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enablePostCssLoader()
  .enableSourceMaps(!Encore.isProduction())

if (Encore.isProduction()) {
  Encore.addPlugin(new PurgecssPlugin({
    defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
    paths: () => glob.sync([
      'templates/**/*.twig'
    ]),
    whitelist: ['p'],
    whitelistPatterns: [/^h[1-6]$/, /^[dou]l$/],
    whitelistPatternsChildren: [/^markup$/]
  }))
}

module.exports = Encore.getWebpackConfig()
