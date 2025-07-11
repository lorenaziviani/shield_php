test:
	./vendor/bin/phpunit

test-waf:
	curl -s http://localhost:8080/teste.php | grep -q "OK"

test-metrics:
	curl -s http://localhost:8080/metrics.php | grep -q "shieldphp_blocked_requests_total"

test-rate-limit:
	curl -s http://localhost:8080/teste.php | grep -q "Too Many Requests"

test-waf-malicious:
	curl -s "http://localhost:8080/teste.php?input=<script>alert(1)</script>" | grep -q "Forbidden: Malicious request detected."

test-all: test-waf test-metrics test-rate-limit test-waf-malicious

test-local:
	REDIS_HOST=127.0.0.1 make test

build:
	docker-compose build

up:
	docker-compose up --build

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up --build

logs:
	docker-compose logs -f app

lint:
	find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

phpcs:
	./vendor/bin/phpcs --standard=PSR12 src/ public/

phpstan:
	./vendor/bin/phpstan analyse src/ public/

security-check:
	./vendor/bin/security-checker security:check

fix:
	./vendor/bin/phpcbf src/ public/

bench:
	chmod +x bench/ab-test.sh
	./bench/ab-test.sh