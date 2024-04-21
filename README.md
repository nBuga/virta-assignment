Virta Assignment project


This recipe was tested on Docker Desktop 4.21.1
==============================================================================

Description
-----------------
Brings up a Linux Containers with Docker, which configures
and installs everything from mysql to php (and php modules), apache and so on.
For more info see docker-compose.yml

Requirements
------------

1. Install [Docker](docker.io)

Setup instructions for Docker environment
-----------------

1. Clone project virta-assignment
(e.g. `` git clone git@github.com:nBuga/virta-assignment.git``)
2. cd docker 
3. Run ``docker-compose up --build -d``
4. RUN ``composer install``
5. RUN ``bin/console doctrine:migrations:migrate``
6. RUN ``bin/console doctrine:fixtures:load``
   1. If we have error on mysql check if the container is still up. Otherwise, RUN ``docker-compose up -d`` to up the mysql container
- You can configure domain names by editing ``/etc/hosts`` on the host and putting the IP and domain names desired, such as:
````
##
# Host Database
#
# localhost is used to configure the loopback interface
# when the system is booting.  Do not change this entry.
##
127.0.0.1       localhost
255.255.255.255 broadcasthost
::1             localhost


10.254.254.225 local.virta virta.mysql
````

- Run sudo ifconfig lo0 alias 10.254.254.225