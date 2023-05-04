up:	artisan own chmod

artisan:
	docker exec parking_php-fpm $(COM)

own:
	docker exec parking_php-fpm chown 1000:1000 -R ./

chmod:
	chmod 777 -R ./app
