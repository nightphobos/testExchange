FROM alpine:20200917

LABEL architecture="AMD64/x86_64" \
      version="0.1" \
      description="Alpine+Nginx+php7.2+php7.2-fpm"

RUN addgroup -g 1500 -S nginx && \
    adduser -S -H -D -s /sbin/nologin -u 1500 -G nginx nginx && \
    apk update && \
    apk add bind-tools \
    busybox-extras \
    curl \
    make \
    nginx \
    supervisor \
    tzdata \
    php7 \
    php7-bcmath \
    php7-common \
    php7-ctype \
    php7-curl \
    php7-dev \
    php7-dom \
    php7-fileinfo \
    php7-fpm \
    php7-gd \
    php7-gettext \
    php7-iconv \
    php7-imap \
    php7-intl \
    php7-json \
    php7-ldap \
    php7-pecl-mailparse \
    php7-mbstring \
    php7-pdo_dblib \
    php7-pdo_mysql \
    php7-mysqli \
    php7-opcache \
    php7-pdo \
    php7-phar \
    php7-posix \
    php7-session \
    php7-simplexml \
    php7-sysvmsg \
    php7-sysvsem \
    php7-sysvshm \
    php7-soap \
    php7-tokenizer \
    php7-xml \
    php7-xmlrpc \
    php7-xmlwriter \
    php7-xdebug \
    php7-zip && \
    rm /etc/nginx/conf.d/default.conf \
    rm -f /var/cache/apk/*

ADD ./nginx /etc/nginx/conf.d
ADD ./supervisord.conf /etc/

RUN mkdir -p /var/lib/php/session && chown -R nginx:nginx /var/lib/php/session/ && \
    mkdir /run/nginx && \
    sed -i -e 's~^;date.timezone =$~date.timezone = Europe/Moscow~g' /etc/php7/php.ini && \
    sed -i -e 's~^memory_limit = 128M$~memory_limit = 512M~g' /etc/php7/php.ini && \
    php -i | grep 'date.timezone\|upload_max_filesize\|post_max_size\|memory_limit'

VOLUME ["/var/www"]

EXPOSE 80

CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]