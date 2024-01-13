# 使用官方 PHP 8.1 镜像作为基础镜像
FROM php:8.1-apache

# 更新系统软件包并安装所需的依赖
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip

# 将你的 PHP 代码复制到镜像中的 /var/www/html 目录
COPY . /var/www/html

# 设置工作目录
WORKDIR /var/www/html

# 设置 Apache 配置以允许 URL 重写（如果需要）
RUN a2enmod rewrite

# 修改文件和目录的所有权，使其可以被 Apache 用户访问（如果需要）
RUN chown -R www-data:www-data /var/www/html

# 暴露 80 端口
EXPOSE 80
