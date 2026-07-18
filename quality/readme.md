# Tips for Using Quality Tools

## How to run Composer commands with Docker 

Please read the Docker [documentation](../docker/README.md) for more information.

```bash
docker exec spatial-php8 COMMAND
```
Example:

```bash
docker exec spatial-php8 composer install --working-dir=quality/php-cs-fixer
```

## Installing and Running Quality Tools

### Php-cs-fixer

To install PHP-CS-Fixer, run the following command:

```bash
docker exec spatial-php8 composer install --working-dir=quality/php-cs-fixer
```

To dry-run (test) all files:

```bash
docker exec spatial-php8 quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --dry-run --allow-risky=yes --diff
```

To fix all files:

```bash
docker exec spatial-php8 quality/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=quality/php-cs-fixer/.php-cs-fixer.php --allow-risky=yes
```

### PhpStan

To install PHP-Stan, run the following command:

```bash
docker exec spatial-php8 composer install --working-dir=quality/php-stan
```

To analyze (test) files:

```bash
docker exec spatial-php8 quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M
```

To add exceptions to the baseline file:

```bash
docker exec spatial-php8 quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M --generate-baseline quality/php-stan/phpstan-baseline.neon
```

### PHP Mess Detector

To install PHP-Mess-Detector, run the following command:

```bash
docker exec spatial-php8 composer install --working-dir=quality/php-mess-detector
```

To analyze (test) files:

```bash
docker exec spatial-php8 quality/php-mess-detector/vendor/bin/phpmd lib text quality/php-mess-detector/ruleset.xml
docker exec spatial-php8 quality/php-mess-detector/vendor/bin/phpmd tests text quality/php-mess-detector/test-ruleset.xml
```

### PHP Code Sniffer

To install PHP-Code-Sniffer, run the following command:

```bash
docker exec spatial-php8 composer install --working-dir=quality/php-code-sniffer
```

To analyze (test) files:

```bash
 docker exec spatial-php8 quality/php-code-sniffer/vendor/bin/phpcs --standard=quality/php-code-sniffer/phpcs.xml -s
```

### Applying Security Updates to Quality Tools

Replace `install` by `update` to start security updates. Remember to commit each `composer.lock` after resolving any issues found during quality tests.

## Running All Quality Tools

To run all quality tools at once (after installing them), use the following command:

```bash
 docker exec spatial-php8 composer quality
```
