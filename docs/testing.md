# Testing (WIP)

To run all of the tests for the custom modules:

```bash
# Run all of the tests.
ddev composer test 

# Run only PHPUnit tests.
ddev composer test-phpunit

# Run only PHPCS tests.
ddev composer test-phpcs
```

Additonal arguments can be appended to the command. For example:

```
ddev composer test-phpunit -- --filter get_the_event_name
```
