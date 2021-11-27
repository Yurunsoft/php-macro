# php-macro

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/macro.svg)](https://packagist.org/packages/yurunsoft/macro)
![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/yurunsoft/php-macro/ci/dev)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)

## 介绍

支持在 PHP 代码中使用类似 C/C++ 中的宏，进行代码预编译。可以方便兼容不同版本和环境下运行的 PHP 代码。

## 使用

安装：`composer require yurunsoft/macro`

### 支持的宏

所有宏都要顶格编写，必须写在该行的开始位置

#### 条件语句

**宏：**`#if`、`#else`、`#elif`、`#endif`

**例子：**

```php
<?php
#if version_compare(\PHP_VERSION, '8.0', '>=')
function test(): string|false
#else
/**
 * @return string|false
 */
function test()
#endif
{
    return 'hello world';
}
```

PHP >= 8.0 环境下生成的代码：

```php
<?php
function test(): string|false
{
    return 'hello world';
}
```

PHP < 8.0 环境下生成的代码：

```php
<?php
/**
 * @return string|false
 */
function test()
{
    return 'hello world';
}
```

## 注意事项

* 你的代码的 PHP 字符串中不能出现：`<?php`、`?>`。如果有可以拆开使用拼接的方式

**例子：**

```php
<?php
echo '<?php echo "hello world"; ?>'; // 错误的写法
echo '<?' . 'php echo "hello world"; ?' . '>'; // 正确的写法
```
