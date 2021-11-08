FROM nginx:1.21.3

WORKDIR /www

# パッケージのインストール
# インストール完了後にaptキャッシュを削除
RUN apt-get update && apt-get install -y \
  git vim &&\
  apt-get clean && \
  rm -rf /var/lib/apt/lists/*

COPY ./default.conf /etc/nginx/nginx.conf

# workerユーザーをnginxにする
RUN touch /var/run/nginx.pid && \
  chown -R nginx:nginx /var/run/nginx.pid && \
  chown -R nginx:nginx /var/cache/nginx

USER nginx
