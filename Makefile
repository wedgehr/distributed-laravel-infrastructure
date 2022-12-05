style:
	@composer install --working-dir=tools/php-cs-fixer
	@tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --allow-risky yes --config .php-cs-fixer.dist.php src tests

stan:
	@PHP_MEM_LIMIT=4g ./vendor/bin/phpstan analyse --memory-limit=3G

stan-baseline:
	@PHP_MEM_LIMIT=4g ./vendor/bin/phpstan analyse --generate-baseline --memory-limit=3G

stanly:
	@PHP_MEM_LIMIT=4g ./vendor/bin/phpstan clear-result-cache --memory-limit=3G
	@PHP_MEM_LIMIT=4g ./vendor/bin/phpstan analyse --memory-limit=3G
