# unifi-stats
Ubiquiti Unifi Network Overview and Statistics

## Requires

* PHP >=7.4
* Composer

Copy .env.example to .evn and change the variables according to your setup.

Run

    composer install
    
it install the required dependencies.

Build docker image:
    
    docker build -t unifi-stats .

Run docker image:
    
    docker run -d -p 8080:80 unifi-stats
