# ROCGenerator

## How to build

本地必须安装 `protoc` 脚本

### PHP

- 使用源码

```shell
cd ./php
php -dphar.readonly=Off main.php phar:build -b main.php --name roc.phar
php roc.phar gen:roc example.proto
```

- 使用打包好的二进制文件

```shell
wget https://github.com/Gemini-D/roc-generator/releases/download/v0.1.0/roc-php_8.1_macos.x86_64
mv roc-php_8.1_macos.x86_64 /usr/local/bin/roc-php
roc-php gen:roc example.proto
```
