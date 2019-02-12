VERSIONS = php-5.6 php-7.0 php-7.1 php-7.2 php-7.3 nightly

all: build test clean

build:
	@echo ">> building images"
	docker-compose build

test:
	@- $(foreach version,$(VERSIONS), \
		echo ">> run tests with $(version)"; \
		docker-compose run --rm $(version) php bin/security-checker.phar security:check ./composer.lock --end-point=http://security.sensiolabs.org/check_lock; \
		docker-compose run --rm $(version) php bin/phpcs.phar --standard=PSR2 ./src -v; \
		docker-compose run --rm $(version) php bin/phpmd.phar ./src text ./phpmd.xml; \
		docker-compose run --rm $(version) php bin/phpcpd.phar ./src; \
		docker-compose run --rm $(version) php bin/phpunit.phar; \
		printf "\033c"; \
	)

clean:
	@echo ">> cleanup"
	docker-compose rm -f &>/dev/null
