FROM php:7.4-cli
COPY ./src /usr/src/grocytobring

WORKDIR /usr/src/grocytobring
CMD [ "php", "./index.php" ]