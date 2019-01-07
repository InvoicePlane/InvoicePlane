:: https://www.npmjs.com/package/node-sass - options: https://github.com/sass/node-sass#usage-1
::
@echo off
cd ..

:: .css + .css.map
call node_modules\.bin\node-sass --source-map true --output "assets/invoiceplane/css/" "assets/invoiceplane/sass/"
call node_modules\.bin\node-sass --source-map true --output "assets/invoiceplane_blue/css/" "assets/invoiceplane_blue/sass/"
