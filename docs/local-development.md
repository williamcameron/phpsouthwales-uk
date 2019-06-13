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

1. Install the website.

    ```
    ddev exec drush site:install -y
    ```

1. Import the initial configuration.

    ```
    ddev exec drush config:set -y system.site uuid 002d5245-82cf-4586-b57f-5ab64ebcd877

    ddev exec drush config:delete -y shortcut.set.default

    ddev exec drush config:import -y --source=../config/sync
    ```
