FROM php:8.0.12-fpm-buster

# シェルの設定を変更
SHELL ["/bin/bash", "-c", "-oeux", "pipefail"]

# パッケージのインストール
# インストール完了後にaptキャッシュを削除
RUN apt-get update && apt-get install -y \
  git vim gcc make unzip nodejs npm\
  libzip-dev libicu-dev libonig-dev && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  docker-php-ext-install intl pdo_mysql zip bcmath && \
  pecl install xdebug && \
  docker-php-ext-enable xdebug

# apt-getでは、古いバージョンのnodejsがインストールされるので、nをインストールして管理
RUN npm install -g n && n stable

# apt-getで最初にインストールした古いnodejsとnpmは削除する
RUN apt-get purge -y nodejs npm

# 設定ファイルのコピー
COPY ./php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./php-ini/php.ini /usr/local/etc/php/php.ini
COPY ./xdebug-ini/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /www

# composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

ARG USERNAME=fpm
ARG GROUPNAME=fpm

RUN groupadd $GROUPNAME && \
  useradd -ms /bin/bash -g $GROUPNAME $USERNAME

USER $USERNAME
