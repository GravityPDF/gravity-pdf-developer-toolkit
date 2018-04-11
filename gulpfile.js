var gulp = require('gulp'),
  uglify = require('gulp-uglify'),
  rename = require('gulp-rename'),
  wpPot = require('gulp-wp-pot')

/* Minify our non-react JS (handled by webpack) */
gulp.task('compress', function () {
  return gulp.src('src/assets/js/*.js')
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('dist/assets/js/'))
})

/* Generate the latest language files */
gulp.task('language', function () {
  return gulp.src(['src/**/*.php', '*.php'])
    .pipe(wpPot({
      domain: 'gravity-pdf-developer-toolkit',
      package: 'Gravity PDF Developer Toolkit'
    }))
    .pipe(gulp.dest('languages/gravity-pdf-developer-toolkit.pot'))
})

gulp.task('watch', function () {
  watch('src/assets/js/*.js', function () { gulp.start('compress') })
})

gulp.task('default', ['language', 'compress'])