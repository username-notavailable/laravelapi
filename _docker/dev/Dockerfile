FROM etcdevelop/php:8.3-debian

COPY data/nodesource_setup.sh /nodesource_setup.sh

RUN set -eux; \
/nodesource_setup.sh; \
apt-get update; \
apt-get upgrade -y; \
apt-get install -y nodejs; \
apt-get install -y apt-utils; \
apt-get install -y gettext-base; \
apt-get install -y nano; \
apt-get install -y git; \
apt-get install -y net-tools; \
apt-get install -y iputils-ping; \
apt-get install -y iptables; \
apt-get install -y bind9-dnsutils; \
apt-get install -y supervisor; \
apt-get install -y procps; \
apt-get install -y jq; \
apt-get install -y zlib1g-dev; \
apt-get install -y libmemcached-dev; \
apt-get install -y libssl-dev; \
install-php-extensions pdo_pgsql intl opcache igbinary; \
docker-php-ext-enable pdo_pgsql; \
docker-php-ext-enable intl; \
docker-php-ext-enable opcache; \
docker-php-ext-enable igbinary;

RUN rm /usr/local/etc/php/conf.d/docker-php-ext-redis.ini

RUN pecl install openswoole-25.2.0 \
	&& pecl install redis-6.2.0 \
    && pecl install memcached-3.3.0 \
	&& docker-php-ext-enable openswoole redis memcached

COPY data/start.sh /start.sh
COPY data/init_dns.sh /init_dns.sh

COPY data/supervisord.skel.conf /etc/supervisor/supervisord.skel.conf
    
WORKDIR /app

ENTRYPOINT /start.sh
