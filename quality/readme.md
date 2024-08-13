# Some tips to use quality tools

## PHP Linters: Php-CS-Fixer and PHP-Stan 

```bash
docker exec spatial-php8 COMMAND
```
Example:
```bash
docker exec spatial-php8 composer install --working-dir=quality/php-cs-fixer
```

## Php-cs-fixer

To install PHP-CS-Fixer, run this command:
```bash
docker exec spatial-php8 composer update --working-dir=quality/php-cs-fixer
```

To test all files:
```bash
docker exec spatial-php8 quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --dry-run --allow-risky=yes --diff
```
To fix all files:
```bash
docker exec spatial-php8 quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --allow-risky=yes
```

## PhpStan

To install PHP-Stan, run this command:

```bash
docker exec spatial-php8 composer update --working-dir=quality/php-stan
```

To test files:
```bash
docker exec spatial-php8 quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M
```

To add a file at exception baseline:
```bash
docker exec spatial-php8 quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M --generate-baseline quality/php-stan/phpstan-baseline.neon
```

## PHP Mess Detector

To install PHP-Mess-Detector, run this command:

```bash
docker exec spatial-php8 composer update --working-dir=quality/php-mess-detector
```

To test files:
```bash
docker exec spatial-php8 quality/php-mess-detector/vendor/bin/phpmd lib text quality/php-mess-detector/ruleset.xml
docker exec spatial-php8 quality/php-mess-detector/vendor/bin/phpmd tests text quality/php-mess-detector/test-ruleset.xml
```

## PHP Code Sniffer

To install PHP-Code-Sniffer, run this command:

```bash
docker exec spatial-php8 composer update --working-dir=quality/php-code-sniffer
```

To test files:
```bash
 docker exec spatial-php8 quality/php-code-sniffer/vendor/bin/phpcs --standard=quality/php-code-sniffer/phpcs.xml -s
```
