# Checkout system

## Requirements and Installation
- Docker & Docker compose
    - PHP 8.2
    - Composer
- Follow the steps to setup & test the project
  ```
  # clone the repo
  > git clone git@github.com:skthon/checkout-system.git
  
  # Creates the docker container with php, composer and mounts the project
  > docker-compose build
  > docker-compose up -d
  
  # Login to the container & install composer packages
  > docker exec -it checkout-system bash
  > composer install
  
  # Run the test class
  > php CheckoutProcessor.php 
  Grand Total: 37.85
  Grand Total: 54.37
  Grand Total: 60.85
  Grand Total: 98.27
  
  # Execute the tests
  > composer run-tests
  PHPUnit 10.5.28 by Sebastian Bergmann and contributors.
  Runtime:       PHP 8.2.21
  Configuration: /checkout-system/phpunit.xml
  .................  17 / 17 (100%)
  Time: 00:00.049, Memory: 8.00 MB
  OK (17 tests, 24 assertions)
  
  # To view any linting fixes
  > composer lint

  # To fix linting
  > composer lint-fix
  
  # To run php static analysis
  > composer phpstan-analyze
  ```

## Assumptions
- 

## TODOs
-