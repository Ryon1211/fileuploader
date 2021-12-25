FROM mysql:8.0.25

# 設定ファイルのコピー
COPY ./my.cnf /etc/mysql/conf.d/my.cnf
RUN chmod 644 /etc/mysql/conf.d/my.cnf

# logファイルの格納場所の指定と所有権の変更
RUN mkdir /var/log/mysql && \
  chown -R mysql:mysql /var/log/mysql

USER mysql
