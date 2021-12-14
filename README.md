# unifi-stats
Provides a simple web page showing Ubiquiti Unifi Network overview and statistics.

## Requirements

* PHP >=7.4
* Composer

## Local development setup

Copy `.env.example` to `.env` and change the variables according to your setup.

Run

    composer install
    
to install the required dependencies.

## Docker image

Build docker image:
    
    docker build -t unifi-stats .

Run docker image:
    
    docker run -d -p 8080:80 unifi-stats

Use `--env-file` or `-e` to specify the necessary environment variables.
