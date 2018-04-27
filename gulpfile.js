var fs = require('fs')
var del = require('del');
var gulp = require('gulp');
var glob = require('glob');
var babel = require('gulp-babel');
var uglify = require('gulp-uglify');
var named = require('vinyl-named');
var webpack = require('webpack-stream');
var cleanCSS = require('gulp-clean-css');
var strip = require('strip-json-comments');

var config = JSON.parse(strip(fs.readFileSync('data/config.json').toString(), {
	whitespace: false
}));

var src_paths = {
	css: 'resources/css/*.css',
	js_all: 'resources/js*/*.js',
	js: 'resources/js/*.js'
};

var dest_paths = {
	css: 'public/assets/css',
	js: 'public/assets/js'
};

gulp.task('clean-css', function() {
	return del(dest_paths.css);
});

gulp.task('clean-js', function() {
	return del(dest_paths.js);
});

gulp.task('clean', ['clean-css', 'clean-js']);

gulp.task('css', ['clean-css'], function() {
	var css_task = gulp.src(src_paths.css);

	if (config.environment.phase === 'production') {
		css_task.pipe(cleanCSS());
	}

	css_task.pipe(gulp.dest(dest_paths.css));
});

gulp.task('js', ['clean-js'], function() {
	var js_task = gulp.src(glob.sync(src_paths.js))
					  .pipe(named())
					  .pipe(webpack())
					  .pipe(babel());

	if (config.environment.phase === 'production') {
		js_task.pipe(uglify());
	}

	js_task.pipe(gulp.dest(dest_paths.js));
});

gulp.task('watch', ['default'], function() {
	gulp.watch(src_paths.css, ['css']);
	gulp.watch(src_paths.js_all, ['js']);
});

gulp.task('default', ['css', 'js']);
