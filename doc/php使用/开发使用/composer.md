- composer是什么？
> php管理依赖关系工具，你可以在自己的项目中声明所依赖的外部工具库（libraries），Composer 会帮你安装这些依赖的库文件。

- mac安装
1. brew install composer 

2. 直接下载

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```


### 常用命令

1. composer install [package]
> 安装依赖.

2. composer require [package]
> 引用扩展，无需再composer.json添加

3. composer update [package]
> 更新依赖

4. composer remove [package]
> 移除依赖


### composer.json
> json 格式

##### 属性
1.  **name 名称** 
> 包的名称，由作者名称和项目名称组成，使用 / 分割，例如：
```
monolog/monolog
igorw/event-source
```

包名称可以包含任何字符，包括空格，并且不区分大小写 ( foo/bar 和 Foo/Bar 会被认为是同一个包)。为了简化安装，建议定义一个不包含非字母数字字符或空格的短名称。发布一个包（库）的必要条件

2. **description 描述**
> 包的简短描述，通常这是一行介绍就行。发布一个包（库）的必要条件

3. **version 版本**
> 包的版本，在大多数情况下，这不是必须的，应该省略（参考下文）。必须遵循  X.Y.Z 或 vX.Y.Z，可选后缀 -dev, -patch ( -p ), -alpha ( -a ), -beta ( -b ) 或 -RC， patch, alpha , beta 和 RC 后缀也可以跟一个数字。  
 如果包仓库可以从某个位置推断出版本，则可填，例如 VCS 仓库中的 VCS 标记名称，这种情况还是建议省略掉。   
注意: 包列表使用 VCS 存储，因此上面的语句对于包列表也是有效。由于人为错误，您自己指定版本很有可能最终会在某些时候出现问题（建议搜索学习：语义化版本）。

4. **type 类型**
> 包的类型，默认为库 library 。   
    包类型用于自定义安装逻辑。如果您有一个包需要一个特殊逻辑，您可以定义一个自定义类型。 这可以是 symfony-bundle， wordpress-plugin 或者 typo3-module。这些类型都将用于某些特定的项目。而对应的项目将要提供一种能够安装该类型包的安装程序。

```
Composer 原生支持以下 4 种类型：

library: 默认类型，它只需要将文件复制到 vendor 目录。
project: 当前包是一个项目，而不是一个库。例：框架应用程序 Symfony standard edition，内容管理系统  SilverStripe installer 或者完全成熟的分布式应用程序。使用 IDE 创建一个新的工作区时，这可以为其提供项目列表的初始化。
metapackage: 包含需求并将触发其安装的空包，但不包含文件，并且不会向系统写入任何内容。因此这种安装类型并不需要一个 dist 或 source。
composer-plugin: 一个安装类型为 composer-plugin 的包，它有一个自定义安装类型，可以为其它包提供一个 installler。详细请查看自定义安装类型。
只有在安装过程中需要自定义逻辑时才使用自定义类型。建议省略该字段并将其默认为库 library。
```


5. **keywords**
> 一组用于搜索与筛选的与包相关的关键字。   
非必须。

6.** time**
> 版本发布日期。  
    必须是 YYYY-MM-DD 或者 YYYY-MM-DD HH:MM:SS 格式。  
    非必须。

7. **license**
> 包的许可证。可以是一个字符串或者是一个字符串数组。

8. **authors**
> 包的作者。这是一个对象数组。

```
每一个作者对象可以包含以下属性：
name: 作者的名字。通常是真实姓名。
email: 作者的邮件地址。
homepage: 作者个人网站的 URL 地址。
role: 作者在项目中担任的角色（如：开发者或者译者）。
```


9. **support**
> 获取对项目支持的信息对象。   
    可选。
```
对象信息必须包括以下属性：

email: 项目支持 email 地址。
issues: 跟踪问题的 URL 地址。
forum: 论坛 URL 地址。
wiki:  Wiki URL 地址。
irc: IRC 聊天频道地址，as irc://server/channel.
source: 网址浏览或下载源。
docs: 文件的 URL 。
rss: RSS 源的 URL 。
```

10. **require**
> 必须安装的依赖包列表，这些包必须满足条件，否则不会安装。

11. **require-dev (root-only)**
> 开发或运行测试时的依赖包列表。根目录下的 composer.json 即 root 包，root 包需要的 dev 依赖默认安装， 安装和更新都支持 —no-dev 选项，以避免 dev 依赖被安装。

12. c**onflict**
> 列表中的包与当前包冲突，不允许同时安装。  
    请注意，在 conflict 中，比如指定 <1.0 >=1.1 的版本范围时，表示小于 1.0 且大于或等于 1.1 的版本冲突，这种约束写法是错误的，应为 <1.0 | >= 1.1 。

13. **replace**
> 列表中的包将被当前包替换。所以你可以 fork 一个包，以不同的名称、版本号发布。之后如果有其他任何包依赖原包，将依赖于你 fork 的包。  
这对于一个内部包含子包的主包很有用。例如 symfony/symfony 这个主包，包含了 Symfony 的所有组件，而这些组件又可以单独的发布。如果你 require 了主包，那么它就会自动完成其下各个子包的依赖，因为主包取代了子包。  
请注意，在上述方法取代子包时，你应该只对子包使用 self.version 这一个版本约束，以确保主包仅替换子包的准确版本，而不是其他版本。

14. **provider**
> 此程序包提供了一个其他程序包列表。 这对于通用接口非常有用， 一个包可能依赖于一些虚拟的 logger 包，任何实现这个 logger 接口的库都可以在 provide 中列出来。

15. **suggest**
> 建议的包可以增强或者适用于当前的包，这些信息，在这个包安装完成之后显示，以便给用户提供可供安装的更多包的信息，即使他们不是被严格要求的。

16. **autoload**
> PHP 自动加载的映射。

支持 PSR-4 和 PSR-0 自动加载，class 映射 和 files 引用。

推荐使用 PSR-4 规范（添加类时，无需重新生成自动加载映射）。

**PSR-4**
> 使用 PSR-4 时，你可以定义从命名空间到路径的映射。
例如，当加载 Foo\\Bar\\Baz 这样的类，且在 composer.json 定义命名空间前缀 Foo\\ 指向目录 src/ 时 ，自动加载器将查找 src/Bar/Baz.php 且命名空间前缀为 Foo\\ 的文件并包含它（如果存在）。

请注意，与旧的 PSR-0 不同的是，PSR-4 的文件路径中不存在与前缀 Foo\\ 对应的 Foo/ 目录。

命名空间前缀必须以 \\ 结尾，以避免类似前缀间的冲突。例如，前缀 Foo 可能匹配 ForBar 命名空间的类，因此尾部的反斜杠解决了这种问题。
在安装、更新时，PSR-4 的引用都被生成单个 key=>value 的数组，该数组可以在生成文件 vendor/composer/autoload_psr4.php 中查看。
```
例:

{
    "autoload": {
        "psr-4": {
            "Monolog\\": "src/",
            "Vendor\\Namespace\\": ""
        }
    }
}
```

如果需要在多个目录中加载相同前缀，可以指定为数组。

```
{
    "autoload": {
        "psr-4": { "Monolog\\": ["src/", "lib/"] }
    }
}
```

如果需要加载目录中所有命名空间的类，可以使用一个空前缀，如：
```
{
    "autoload": {
        "psr-4": { "": "src/" }
    }
}
```

** PSR-0**
> 使用 PSR-0 时，可以定义相对于包的根目录，命名空间到路径的映射。也支持 PEAR-style 的非命名空间约定。

请注意，PSR-0 的命名空间前缀也必须以 \\ 结尾，以避免类似前缀间的冲突。

在安装、更新时，PSR-0 的引用都被生成单个 key=>value 的数组，该数组可以在生成文件 vendor/composer/autoload_namespaces.php 中查看。
```
例:

{
    "autoload": {
        "psr-0": {
            "Monolog\\": "src/",
            "Vendor\\Namespace\\": "src/",
            "Vendor_Namespace_": "src/"
        }
    }
}
```

如果需要在多个目录中加载相同前缀，可以指定为数组：
```
{
    "autoload": {
        "psr-0": { "Monolog\\": ["src/", "lib/"] }
    }
}
```

PSR-0 不仅可以用于命名空间的声明，还可以直接指定到类。这对于在全局命名空间中只有一个类的包非常有用。例如，只有一个 php 文件位于包的根目录中，则可能声明为：
```
{
    "autoload": {
        "psr-0": { "UniqueGlobalClass": "" }
    }
}
```

如果需要加载目录中所有命名空间的类，可以使用一个空前缀，如：
```
{
    "autoload": {
        "psr-0": { "": "src/" }
    }
}
```

**Classmap**
> 在安装或更新的时候，classmap 被组合成单个键值对数组（key=>value）的形式，这个数组可以在 vendor/composer/autoload_classmap.php 中找到。这个映射是通过扫描给定的 文件夹 / 文件中所有的.php 和.inc 来生成的。

您可以通过生成类映射来自动加载不遵循 PSR-0/4 的库。通过设定要搜索类的所有目录或文件来进行配置。


```
Example:

{
    "autoload": {
        "classmap": ["src/", "lib/", "Something.php"]
    }
}
```

**Files**
> 如果你想在每个请求中引入某个文件，那么你可以使用 files 自动加载机制。这有利于加载包中无法自动加载的 php 函数。


```
Example:

{
    "autoload": {
        "files": ["src/MyLibrary/functions.php"]
    }
}
```

**Exclude files from classmaps**
> 我们可以使用 exclude-from-classmap 从类库映射中排查某些文件或文件夹。这很方便我们在生成环境中排查测试类，例如，在构建加载的时候自动加载器会排除这些类。

类映射生成器将忽略此处配置的路径中的所有文件。这些路径是包根目录（即 composer.json 位置）的绝对路径，并且支持 * 来匹配除斜杠之外的任何东西，并且 ** 来匹配任何东西。 ** 隐含地添加到路径的末尾。


```
Example:

{
    "autoload": {
        "exclude-from-classmap": ["/Tests/", "/test/", "/tests/"]
    }
}
```

**优化自动加载**
> 自动加载会对请求时间产生较大的影响（类较多的大型框架中每个请求自动加载器将会耗时 50-100 ms）。点击查看如何 优化自动加载。

**autoload-dev (root-only)**
> 这个节点允许允许为开发阶段自定义加载规则。

运行测试套件所需的类不应包含在主自动加载规则中，以避免在生产中污染自动加载器以及其他人将您的包用作依赖项。

因此，最好使用专用路径进行单元测试，并将其添加到 autoload-dev 部分。


```
例如:

{
    "autoload": {
        "psr-4": { "MyLibrary\\": "src/" }
    },
    "autoload-dev": {
        "psr-4": { "MyLibrary\\Tests\\": "tests/" }
    }
}
```

**include-path**
> 弃用:
这里仅仅用于支持遗留项目，所有的新代码最好使用自动加载。因此，这是一种弃用的做法，但该功能本身不会从 Composer 中消失。

应该附加到 PHP 的 include_path 的路径列表。


```
例如:

{
    "include-path": ["lib/"]
}
```

这是可选的。

**target-dir**
> 弃用：这仅用于支持传统的 PSR-0 样式自动加载，并且所有新代码最好是没有使用 target-dir 的 PSR-4，鼓励使用 PHP 命名空间的 PSR-0 项目转移到 PSR-4。

定义安装目标。

如果包的根路径位于命名空间声明之下，则无法正确自动加载。 target-dir 解决了这个问题。

例如 Symfony，组件都是单独的包。 Yaml 组件位于 Symfony\Component\Yaml 下。包的根路径是 Yaml 目录。如果要自动加载，我们需要确保它没有安装到 vendor/symfony/ yaml 中，而是安装到 vendor/symfony/yaml/Symfony/Component/Yaml 中，以便自动加载器可以从 vendor/symfony/yaml 中加载。

为此，autoload 和 target-dir 定义如下：


```
{
    "autoload": {
        "psr-0": { "Symfony\\Component\\Yaml\\": "" }
    },
    "target-dir": "Symfony/Component/Yaml"
}
```

这是可选的。

**minimum-stability (root-only)**
> 这定义了按稳定性过滤包的默认值，默认为 stable。所以如果你依赖 dev 包，你应该在你的文件中指定。

所有包都将根据稳定性检出相应的版本，那些低于 minimum-stability 设置的版本将被自动忽略。（请注意，你还可以使用在 require 中指定的版本约束来定义每个包的稳定性要求（请参阅 package links）。

可用选项（按稳定性排序）是 dev，alpha，beta，RC 和 stable。

**prefer-stable (root-only)**
> 启用此项后，如果可以找到兼容的稳定包，Composer 将优先选择比不稳定包更稳定的包。如果您需要开发版本或者某个软件包只有 alpha 可用，那么仍然会选择那些允许最小稳定性允许的包。

使用 "prefer-stable": true 来开启此项.

**repositories (root-only)**
> 使用自定义的安装源。

Composer 默认只使用 packagist 的安装源。通过定义 repositories 你可以从任何其他地方获取包。

存储库不会递归解析，您只能将它们添加到主 composer.json 中。依赖项中 composer.json 的 repositories 配置被忽略。


```
支持以下安装源类型:

1. composer: Composer 存储库只是通过网络（HTTP，FTP，SSH）提供的 packages.json 文件，其中包含带有额外 dist 或 source 信息的 composer.json 对象列表。 packages.json 文件使用 PHP 流加载。您可以使用 options 参数在该流上设置额外选项。
2. vcs: 版本控制系统存储库可以从 git, svn, fossil 或 hg repositories。
3. pear: 通过 pear，可以将任何 pear 存储库导入 Composer 项目。
4. package: 如果依赖于一个不支持 composer 的项目，可以使用 package 来定义包。只需要内联 composer.json 对象。
更多信息，请参阅 Repositories.
```


```
例如:

{
    "repositories": [
        {
            "type": "composer",
            "url": "http://packages.example.com"
        },
        {
            "type": "composer",
            "url": "https://packages.example.com",
            "options": {
                "ssl": {
                    "verify_peer": "true"
                }
            }
        },
        {
            "type": "vcs",
            "url": "https://github.com/Seldaek/monolog"
        },
        {
            "type": "pear",
            "url": "https://pear2.php.net"
        },
        {
            "type": "package",
            "package": {
                "name": "smarty/smarty",
                "version": "3.1.7",
                "dist": {
                    "url": "https://www.smarty.net/files/Smarty-3.1.7.zip",
                    "type": "zip"
                },
                "source": {
                    "url": "https://smarty-php.googlecode.com/svn/",
                    "type": "svn",
                    "reference": "tags/Smarty_3_1_7/distribution/"
                }
            }
        }
    ]
}
```

注意：顺序在这里很重要。在查找包时，Composer 将从第一个存储库查找到最后一个存储库，然后选择第一个匹配项。默认情况下，最后添加 Packagist ，这意味着自定义存储库可以覆盖它的包。

也可以使用 JSON 对象表示法。但是 JSON 的 key/value 是无序的，因此无法保证一致的行为。


```
{
   "repositories": {
        "foo": {
            "type": "composer",
            "url": "http://packages.foo.com"
        }
   }
}
```

**config (root-only)**
> 一组配置选项。它仅用于项目。详情请参阅 Config 。

**scripts (root-only)**
>Composer 允许再安装过程的各个部分中执行脚本。


**extra**
> scripts 使用的任意扩展数据。

这几乎可以是任意的东西，要从脚本事件处理程序中访问它，您可以执行以下操作：

$extra = $event->getComposer()->getPackage()->getExtra();
这是可选的。

**bin**
> 一组被链接到 bin-dir 的二进制文件（来自 config）。  
详情请参阅 Vendor Binaries 。  
这是可选的。

**archive**
> 一组用于创建包归档的选项。

支持以下选项：

exclude: 允许配置一组需要排除的路径，语法与 .gitignore 文件一致。如果开头使用（!）将代表包含任意匹配文件，即使它们之前被排除了。/ 表示从项目相对路径的根目录进行匹配，* 不会扩展到目录分隔符。

```
例如:

{
    "archive": {
        "exclude": ["/foo/bar", "baz", "/*.test", "!/foo/bar/baz"]
    }
}
```

这个例子将会包含 /dir/foo/bar/file, /foo/bar/baz, /file.php, /foo/my.test 并且排除 /foo/bar/any, /foo/baz, 和 /my.test.

这是可选的。
