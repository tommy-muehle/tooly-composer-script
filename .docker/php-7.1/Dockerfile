FROM php:7.1-alpine
RUN apk --update add libbz2 bzip2-dev && \
    apk del build-base && \
    rm -rf /var/cache/apk/*
RUN docker-php-ext-install bz2
VOLUME ["/app"]
WORKDIR /app
