/**
 * @author Greenrivers
 * @copyright Copyright (c) 2022 Greenrivers
 */

const gulp = require('gulp');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const dartSass = require('sass');
const gulpSass = require('gulp-sass');
const sass = gulpSass(dartSass);
const cleancss = require('gulp-clean-css');
const csscomb = require('gulp-csscomb');
const rename = require('gulp-rename');
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');

// ES6 modules
const rollup = require('gulp-better-rollup');
const babel = require('rollup-plugin-babel');
const resolve = require('rollup-plugin-node-resolve');
const commonjs = require('rollup-plugin-commonjs');

const {src, series, parallel, dest, watch} = gulp;

const cssPath = './scss/**/*.scss';
const jsPath = './app/**/*.js';
const jsModulesPath = '!./app/modules/**/*.js';

const css_dest_dir = './assets/css';
const js_dest_dir = './assets/js';

const compileCSS = () => {
    return src(cssPath)
        .pipe(sourcemaps.init())
        .pipe(sass({
                outputStyle: 'compressed',
                precision: 10
            }).on('error', sass.logError)
        )
        .pipe(sourcemaps.write())
        .pipe(autoprefixer())
        .pipe(dest(css_dest_dir))
        .pipe(csscomb())
        .pipe(cleancss())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(dest(css_dest_dir));
}

const compileJS = () => {
    return src([jsPath, jsModulesPath])
        .pipe(rollup({ plugins: [babel(), resolve(), commonjs()] }, 'umd'))
        .pipe(sourcemaps.init())
        // .pipe(concat('script.js')) // into one
        .pipe(terser())
        .pipe(sourcemaps.write())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(dest(js_dest_dir));
}

const watchBuild = () => {
    return watch([cssPath, jsPath], {interval: 1000}, parallel(compileCSS, compileJS));
}

const build = series(parallel(compileCSS, compileJS));

exports.watch = watchBuild;
exports.build = build;
exports.default = build;
