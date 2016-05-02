app url 'http://dev.invoiceplane/'

in ~/invoiceplane/application/config/database.php
```
$db['default']['hostname'] = '127.0.0.1';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'invoiceplane';
$db['default']['dbdriver'] = 'mysqli';
```
nginx configuration

```
server {
    listen   80;
    server_name    dev.invoiceplane;
    #access_log    /var/log/nginx/dev.invoiceplance.access.log;
    error_log    /var/log/nginx/dev.invoiceplane.error.log;
    root    /home/longnguyen/sandbox/invoiceplane;
    index index.html index.php index.htm;

    # set expiration of assets to MAX for caching
    location ~* \.(ico|css|js|gif|jpe?g|png)(\?[0-9]+)?$ {
        expires max;
        log_not_found off;
    }


    location / {
        index       index.php;
        try_files   $uri $uri/ /index.php;    
    }

    location ~* \.php(/|$) {
	fastcgi_pass php-fpm;
        #fastcgi_split_path_info ^(.+\.php)(/.+)$;
	#fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;    
     }

     # deny access to .htaccess files
    location ~ /\.ht {
        deny        all;
    }

}
```
run http://dev.invoiceplane/setup
