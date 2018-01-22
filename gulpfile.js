var gulp = require('gulp'),
  wpPot = require('gulp-wp-pot')

/* Generate the latest language files */
gulp.task('language', function () {
  return gulp.src(['src/**/*.php', '*.php'])
    .pipe(wpPot({
      domain: 'gravity-pdf-developer-toolkit',
      package: 'Gravity PDF Developer Toolkit'
    }))
    .pipe(gulp.dest('languages/gravity-pdf-developer-toolkit.pot'))
})

gulp.task('default', ['language'])