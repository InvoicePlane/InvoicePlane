:: https://www.npmjs.com/package/node-sass - options: https://github.com/sass/node-sass#usage-1
::
cd ..

:: watch and output .css + .css.map
start /B node_modules\.bin\node-sass --watch --recursive --source-map true --output "assets/invoiceplane/css/" "assets/invoiceplane/sass/"
start /B node_modules\.bin\node-sass --watch --recursive --source-map true --output "assets/invoiceplane_blue/css/" "assets/invoiceplane_blue/sass/"