# Checkout System

## Table of Contents

1. [Requirements and Installation](#requirements-and-installation)
2. [Project Notes](#project-notes)
3. [Assumptions/Notes/TODOs](#assumptionsnotestodos)

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

## Project Notess
```
src
├── Rules
│   ├── DiscountFeeRule.php
│   ├── DeliveryFeeRule.php
│   └── RuleContract.php
├── Models
│   └── Product.php
├── CartCheckout.php
└── ProductUtils.php
tests
├── Integration
│   └── CartCheckoutTest.php
└── Unit
    ├── CartCheckoutTest.php
    └── Rules
        ├── DiscountFeeRuleTest.php
        └── DeliveryFeeRuleTest.php
```
Main Directory: **src** - Contains the main source code for the project.
- **Rules**: Directory for classes implementing various business rules.
  - `DiscountFeeRule.php`: Contains the rule for applying discounts.
  - `DeliveryFeeRule.php`: Contains the rule for calculating delivery fees.
  - `RuleContract.php`: Defines the interface for rule contracts.
- **Models**: Directory for model classes.
  - `Product.php`: Represents the product model.
- `CartCheckout.php`: Main class for handling cart operations and checkout process.
- `ProductUtils.php`: Utility class for product-related operations.

## Assumptions/Notes/TODOs
- I considered the sum of product prices and their discounts to calculate the delivery fees.
- Rounding the price values and converting between strings and floats turned out to be a bit messy. Alternatively, I would have created a helper function class to tackle this or used an external library for calculations for a long-term project.
- Docker-compose isn't really required, but it would prove useful if we have another container service for product-related data, a caching database, and a relational database.
- For rule conditions, I should have probably enforced passing them as DTOs with more validation so that if we were to extend, they could easily be fetched from a database where we can have a rules table with columns: rule_id, rule_type, rule_conditions (json).
- In some places, I may have missed out on strict validations of checking whether product information exists. I have used null coalescing to mitigate this.
- For PHPStan, I am using level 5. It helped me find logical errors, such as when a discount percentage is not passed. For higher levels, it returns "no value type" errors. Ideally, these should be completely resolved by extending the rule conditions into structured objects.   