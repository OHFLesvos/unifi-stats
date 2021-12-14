# unifi-stats
Ubiquiti Unifi Network Overview and Statistics

## Requirements

* PHP >=7.4
* Composer

## Local development setup

Copy .env.example to .env and change the variables according to your setup.

Run

    composer install
    
it install the required dependencies.

## Docker image

Build docker image:
    
    docker build -t unifi-stats .

Run docker image:
    
    docker run -d -p 8080:80 unifi-stats

Use `--env-file` or `-e` to specify the necessary environment variables.
