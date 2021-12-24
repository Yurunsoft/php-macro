# php-macro

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/macro.svg)](https://packagist.org/packages/yurunsoft/macro)
![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/yurunsoft/php-macro/ci/master)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)

## 介绍

支持在 PHP 代码中使用类似 C/C++ 中的宏，进行代码预编译。可以方便兼容不同版本和环境下运行的 PHP 代码。

## 使用

安装：`composer require yurunsoft/macro`

### 支持的宏

#### 常量

**宏：**`#define`、`#const`、`#ifdef`、`#ifndef`

**例子：**

```php
#ifndef IN_SWOOLE
    # define IN_SWOOLE extension_loaded('swoole')
#endif
#ifdef IN_SWOOLE
#if IN_SWOOLE
\Co\run(function(){
    echo 'hello world';
});
#endif
#endif
```

> 注意：使用宏定义的常量，仅在生成代码时有效，运行时无效

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

### MacroParser 方法

类：`\Yurun\Macro\MacroParser`

#### setTmpPath

设置生成 PHP 代码的临时目录

`MacroParser::setTmpPath(string $tmpPath): void`

#### getTmpPath

获取生成 PHP 代码的临时目录

`MacroParser::getTmpPath(): string`

#### includeFile

直接加载带有宏代码的文件，内部会自动预编译并加载最终的文件

`MacroParser::includeFile(string $file, string $destFile = '', bool $deleteFile = true): mixed`

> 这个最为常用

#### convert

将带有宏的代码，转换为预编译后的 PHP 代码

`MacroParser::parse(string $content): string`

#### convertFile

将带有宏代码的文件，转换为预编译后的 PHP 代码并保存到目标文件。

方法返回值是预编译后的 PHP 代码。

`MacroParser::convertFile(string $srcFile, string $destFile = ''): string`

#### parse

将带有宏的代码，编译成预编译的 PHP 代码

`MacroParser::parse(string $content): string`

#### execParsedCode

执行预编译的 PHP 代码，返回预编译后的 PHP 代码

`MacroParser::execParsedCode(string $code): string`

## 注意事项

* 你的代码的 PHP 字符串中不能出现：`<?php`、`?>`。如果有可以拆开使用拼接的方式

**例子：**

```php
<?php
echo '<?php echo "hello world"; ?>'; // 错误的写法
echo '<?' . 'php echo "hello world"; ?' . '>'; // 正确的写法
```
