import del from 'del';
import gulp from 'gulp';
import path from 'path';
import rev from 'gulp-rev';
import postcss from 'gulp-postcss';
import revFormat from 'gulp-rev-format';
import sourcemaps from 'gulp-sourcemaps';
const base = path.resolve('../assets/styles/');
require('es6-promise').polyfill();

// Delete current CSS build
gulp.task('clean-css', () => {
    return del([
        '../build/css/**/*',
        '../build/img/**/*'
    ], {
        force: true
    });
});

// Handle building of CSS files
gulp.task('build-css', ['clean-css'], () => {
    const context = path.resolve(__dirname, '../');
    const dest = path.resolve(context, 'build/css');
    const from = path.resolve(base, 'app.css');
    const to = path.resolve(dest, 'app.css');
    const publicPath = dest.replace(path.resolve(context, '../'), '' ) + '/';
    return gulp.src([
        from,
        path.resolve(base, 'pages/**')
    ], { base })
        .pipe(sourcemaps.init())
        .pipe(postcss([
            require('precss')({
                prefix: '',
                extension: 'pcss'
            }),
            require('postcss-copy')({
                src: path.resolve('../'),
                dest: path.join(dest, '../img'),
                relativePath(dirname, fileMeta, result, options) {
                    return result.opts.to ? path.dirname(result.opts.to) : options.dest;
                }
            }),
            require('autoprefixer')({browsers: ['> 0%']}),
            require('cssnano')({zindex: false}),
            require('postcss-reporter')({
                clearReportedMessages: true
            })
        ], { from, to }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(dest))
        .pipe(rev())
        .pipe(revFormat({
            prefix: '.'
        }))
        .pipe(gulp.dest(dest))
        .pipe(rev.manifest({
            path: 'assets.json',
            transformer: {
                parse: JSON.parse,
                stringify(value) {
                    for (let path in value) {
                        value[path] = publicPath + value[path];
                    }
                    return JSON.stringify(value, null, 4);
                }
            }
        }))
        .pipe(gulp.dest(dest))
    ;
});

// Watch for CSS file changes
gulp.task('watch-css', () => {
    gulp.watch(
        `${base}/**/*`,
        ['build-css']
    );
});

// Define detail task
gulp.task('default', [
    'build-css'
]);
