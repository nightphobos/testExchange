envbuild:
	docker build ./env/web -t demoexchange

envstop:
	docker-compose -f ./env/docker-compose.yml stop

envstart:
	docker-compose -f ./env/docker-compose.yml up -d

envstatus:
	docker-compose -f ./env/docker-compose.yml ps

init:
	docker-compose -f ./env/docker-compose.yml exec demoexchange /bin/sh -c "cd /var/www/main; composer install -n"
	docker-compose -f ./env/docker-compose.yml exec demoexchange /bin/sh -c "cd /var/www/main; yes| php bin/console doctrine:fixtures:load"

envrm:
	yes | docker-compose -f ./env/docker-compose.yml rm