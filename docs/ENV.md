```apacheconfig
# 反射、逻辑、运行异常文件名称
JMHC_EXCEPTION_FILE_NAME=handle.exception
# 数据库查询异常文件名称
JMHC_DB_EXCEPTION_FILE_NAME=handle_db.exception
# 错误文件名称
JMHC_ERROR_FILE_NAME=handle.error
```

```apacheconfig
# 是否允许保存debug日志
JMHC_LOG_DEBUG=true
# 日志保存路径
JMHC_LOG_PATH=runtime/logs
# 日志文件最大内存,0不限制,如（2m,2g）
JMHC_LOG_MAX_SIZE=0
# 目录下最大日志文件数量,0不限制
JMHC_LOG_MAX_FILES=0
```

```apacheconfig
# 运行调试模式,true:不加密
JMHC_RUNTIME_DEBUG=true
# 运行加密方法
JMHC_RUNTIME_METHOD=AES-128-CBC
# 运行加密向量
JMHC_RUNTIME_IV=
# 运行加密秘钥
JMHC_RUNTIME_KEY=
```

```apacheconfig
# 令牌加密方法
JMHC_TOKEN_METHOD=AES256
# 令牌加密向量
JMHC_TOKEN_IV=
# 令牌加密秘钥
JMHC_TOKEN_KEY=
# 令牌填充位置
JMHC_TOKEN_POS=5
# 令牌填充长度
JMHC_TOKEN_LEN=6
# 令牌允许刷新时间（秒） 3天
JMHC_TOKEN_ALLOW_REFRESH_TIME=259200
# 令牌提示刷新时间（秒） 2天
JMHC_TOKEN_NOTICE_REFRESH_TIME=172800
```

```apacheconfig
# 是否检测签名
JMHC_SIGNATURE_CHECK=false
# 签名秘钥
JMHC_SIGNATURE_KEY=
# 签名时间戳超时（秒）
JMHC_SIGNATURE_TIMESTAMP_TIMEOUT=60
```

```apacheconfig
# 请求锁定时间（秒）
JMHC_REQUEST_LOCK_SECONDS=5
# 请求锁定提示
JMHC_REQUEST_LOCK_TIPS=请求已被锁定，请稍后重试~
```

```apacheconfig
# 单设备登录临时缓存过期时间（秒）
JMHC_SDL_TMP_EXPIRE=10
```
