style:
	@composer install --working-dir=tools/php-cs-fixer
	@tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --allow-risky yes --config .php-cs-fixer.dist.php src tests
