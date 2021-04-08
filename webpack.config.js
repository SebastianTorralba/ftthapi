var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where all compiled assets will be stored
   .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create public/build/app.js and public/build/app.css
    .addEntry('js/red/disenio', './app/Resources/assets/js/red/disenio.js')
    .addEntry('js/red/gestion-obra', './app/Resources/assets/js/red/gestion-obra.js')
    .addEntry('js/red/apareo-nap-conexion', './app/Resources/assets/js/red/apareo-nap-conexion.js')


    .addEntry('js/utilidad/google-map/oca-control-georeferencia', './app/Resources/assets/js/utilidad/google-map/oca-control-georeferencia.js')

    // allow legacy applications to use $/jQuery as a global variable
    //.autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()

    // allow sass/scss files to be processed
    // .enableSassLoader()

    .enableReactPreset()

    .configureBabel(function(babelConfig) {
        babelConfig.plugins = ["transform-object-rest-spread","transform-class-properties"]
        //babelConfig.presets.push('es2015');
        babelConfig.presets.push('es2017');
        babelConfig.presets[0][1].targets.browsers = ["last 2 versions", "Explorer >= 9"]

    })
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
