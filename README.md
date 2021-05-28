# pluggit-api
PHP package for pluggit ventilation units

## how to use?
Have a look at sample.php for some ideas

## docker
There is a docker-compose file with three services
* sample
* test
* dev

We need customized image. Build like this:

    docker-compose build

You can pass IP of ventilation unit by CLI parameter.
On Linux run services like this:

    PLUGGIT_IP=192.168.x.x docker-compose run --rm sample

On Windows run like this:

    cmd /C "set PLUGGIT_IP=192.168.1.x && docker-compose run --rm sample"
