# Local Development

## Prerequisites

* [DDEV](https://www.drud.com)
* [Docker](https://www.docker.com)
* [Git](https://git-scm.com)

## Initial installation

1. Clone the repository.

    ```
    git clone https://github.com/PHPSouthWales/php-south-wales-drupal.git php-south-wales

    cd php-south-wales
    ```

1. Start DDEV.

    ```
    ddev start
    ```

1. Install Composer dependencies.

    ```
    ddev composer install
    ```

1. Install the website.

    ```
    ./helper.sh refresh
    ```
