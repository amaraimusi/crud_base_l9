var gulp = require('gulp');
var concat = require('gulp-concat');
var terser = require('gulp-terser');

gulp.task('CrudBase', function () {
	gulp.src('./dev/resources/js/CrudBase/*.js')
	.pipe(concat('CrudBase.min.js'))
	.pipe(terser())
	.pipe(gulp.dest('./dev/public/js'));
});

gulp.task('CrudBaseForCss', function () {
	gulp.src('./dev/resources/css/CrudBase/*.css')
	.pipe(concat('CrudBase.min.css'))
	.pipe(gulp.dest('./dev/public/css'));
});

