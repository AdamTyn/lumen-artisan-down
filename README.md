# lumen-artisan-down

移植Laravel的 `php artisan down` [进入维护模式]指令到Lumen

# Usage

在 **'app/commands/kernel.php'** 中注册指令：

```
protected $commands = [
	\AdamTyn\Lumen\Artisan\DownCommand::class
];
```
