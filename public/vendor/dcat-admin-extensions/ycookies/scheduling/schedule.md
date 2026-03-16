# 任务调度

- [简介](#introduction)
- [定义调度](#defining-schedules)
    - [Artisan 命令调度](#scheduling-artisan-commands)
    - [队列任务调度](#scheduling-queued-jobs)
    - [Shell 命令调度](#scheduling-shell-commands)
    - [调度频率设置](#schedule-frequency-options)
    - [时区](#timezones)
    - [避免任务 重复](#preventing-task-overlaps)
    - [任务只运行在一台服务器上](#running-tasks-on-one-server)
    - [后台任务](#background-tasks)
    - [维护模式](#maintenance-mode)
- [任务输出](#task-output)
- [任务钩子](#task-hooks)

<a name="introduction"></a>
## 简介

过去，你可能需要在服务器上为每一个调度任务去创建 Cron 入口。但是这种方式很快就会变得不友好，因为这些任务调度不在源代码中，并且你每次都需要通过 SSH 链接登录到服务器中才能增加 Cron 入口。

Laravel 命令行调度器允许你在 Laravel 中对命令调度进行清晰流畅的定义。且使用这个任务调度器时，你只需要在你的服务器上创建单个 Cron 入口接口。你的任务调度在 `app/Console/Kernel.php` 的 `schedule` 方法中进行定义。为了帮助你更好的入门，这个方法中有个简单的例子。

### 启动调度器

当使用这个调度器时，你只需要把下面的 Cron 入口添加到你的服务器中即可。如果你不知道怎么在服务器中添加 Cron 入口，可以考虑使用一些服务来管理 Cron 入口，比如 [Laravel Forge](https://forge.laravel.com)：

    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

这个 Cron 为每分钟执行一次 Laravel 的命令行调度器。当 `schedule:run` 命令被执行的时候，Laravel 会根据你的调度执行预定的程序。

<a name="defining-schedules"></a>
## 定义调度

你可以在 `App\Console\Kernel` 类的 `schedule` 方法中定义所有的调度任务。在开始之前，让我们来看一个例子。在这个例子中，我们计划每天午夜调用一个闭包。在闭包中，我们执行一个数据库查询来清空一张表：

    <?php

    namespace App\Console;

    use Illuminate\Support\Facades\DB;
    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

    class Kernel extends ConsoleKernel
    {
        /**
         * 应用里的自定义 Artisan 命令
         *
         * @var array
         */
        protected $commands = [
            //
        ];

        /**
         * 定义计划任务
         *
         * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
         * @return void
         */
        protected function schedule(Schedule $schedule)
        {
            $schedule->call(function () {
                DB::table('recent_users')->delete();
            })->daily();
        }
    }

除了使用闭包来定义任务调度外，你也可以用 [invokable objects](http://php.net/manual/zh/language.oop5.magic.php#object.invoke)。

    $schedule->call(new DeleteRecentUsers)->daily();



<a name="scheduling-artisan-commands"></a>
### Artisan 命令调度

除了使用调用闭包这种方式来调度外，你还可以调用  [Artisan 命令](/docs/{{version}}/artisan) 和操作系统命令。比如，你可以给 `command` 方法传递命令名称或者类来调度一个 Artisan 命令。

    $schedule->command('emails:send --force')->daily();

    $schedule->command(EmailsCommand::class, ['--force'])->daily();

<a name="scheduling-queued-jobs"></a>
### 队列任务调度

`job` 方法可以用来调度 [队列任务](/docs/{{version}}/queues)。此方法提供了一种快捷的方式来调度任务，而无需使用 `call` 方法创建闭包来调度任务。

    $schedule->job(new Heartbeat)->everyFiveMinutes();

    // 分发任务到「heartbeats」队列...
    $schedule->job(new Heartbeat, 'heartbeats')->everyFiveMinutes();

<a name="scheduling-shell-commands"></a>
### Shell 命令调度

`exec` 方法可用于向操作系统发送命令：

    $schedule->exec('node /home/forge/script.js')->daily();

<a name="schedule-frequency-options"></a>
### 调度频率设置

当然了，你可以给你的任务分配多种调度计划：

方法  | 描述
------------- | -------------
`->cron('* * * * *');`  |  自定义 Cron 时间表执行任务
`->everyMinute();`  |  每分钟执行一次任务
`->everyFiveMinutes();`  |  每五分钟执行一次任务
`->everyTenMinutes();`  |  每十分钟执行一次任务
`->everyFifteenMinutes();`  |  每十五分钟执行一次任务
`->everyThirtyMinutes();`  |  每三十分钟执行一次任务
`->hourly();`  |  每小时执行一次任务
`->hourlyAt(17);`  |  每小时第 17 分钟执行一次任务
`->daily();`  |  每天午夜执行一次任务（译者注：每天零点）
`->dailyAt('13:00');`  |  每天 13 点执行一次任务
`->twiceDaily(1, 13);`  |  每天 1 点 和 13 点分别执行一次任务
`->weekly();`  |  每周执行一次任务
`->weeklyOn(1, '8:00');`  |  每周一的 8 点执行一次任务
`->monthly();`  |  每月执行一次任务
`->monthlyOn(4, '15:00');`  |  每月 4 号的 15 点执行一次任务
`->quarterly();` |  每季度执行一次任务
`->yearly();`  |  每年执行一次任务
`->timezone('America/New_York');` | 设定时区

结合其他一些特定条件，我们可以生成在一周中特定时间运行的任务。举个例子，在每周一执行命令：

    // 每周一 13:00 执行...
    $schedule->call(function () {
        //
    })->weekly()->mondays()->at('13:00');

    // 工作日（周一至周五） 8点 至 17 点每小时执行一次...
    $schedule->command('foo')
              ->weekdays()
              ->hourly()
              ->timezone('America/Chicago')
              ->between('8:00', '17:00');

额外的限制条件列表如下：

方法  | 描述
------------- | -------------
`->weekdays();`  |  限制任务在工作日执行
`->weekends();`  |  限制任务在周末执行
`->sundays();`  |  限制任务在周日执行
`->mondays();`  |  限制任务在周一执行
`->tuesdays();`  |  限制任务在周二执行
`->wednesdays();`  |  限制任务在周三执行
`->thursdays();`  |  限制任务在周四执行
`->fridays();`  |  限制任务在周五执行
`->saturdays();`  |  限制任务在周六执行
`->between($start, $end);`  |  限制任务在 `$start` 和 `$end` 之间执行
`->when(Closure);`  |  当闭包返回为真时执行
`->environments($env);`  |  限制任务在特定环境中执行



#### 时间范围限制

使用 `between` 来限制任务在一天中的某个时间段来执行：

    $schedule->command('reminders:send')
                        ->hourly()
                        ->between('7:00', '22:00');

或者使用 `unlessBetween` 方法来为任务排除一个时间段：

    $schedule->command('reminders:send')
                        ->hourly()
                        ->unlessBetween('23:00', '4:00');

#### 闭包测试限制

使用 `when` 方法来根据测试结果来执行任务。也就是说，如果给定的闭包返回结果为 `true`，只要没有其他约束条件阻止任务运行，任务就会一直执行下去：

    $schedule->command('emails:send')->daily()->when(function () {
        return true;
    });

`skip` 方法可以看做是 `when` 方法的逆过程。如果 `skip` 方法返回 `true`，任务就不会执行：

    $schedule->command('emails:send')->daily()->skip(function () {
        return true;
    });

使用链式调用 `when` 方法时，只有所有的 `when` 都返回 `true` 时，任务才会执行。

#### 环境约束

`environments` 方法可用于仅在给定环境中执行任务：

    $schedule->command('emails:send')
                ->daily()
                ->environments(['staging', 'production']);

<a name="timezones"></a>
### 时区

使用 `timezone` 方法，你可以指定任务在给定的时区内执行：

    $schedule->command('report:generate')
             ->timezone('America/New_York')
             ->at('02:00')

如果要为所有计划任务分配相同的时区，则可能希望在 `app/Console/Kernel.php` 文件中定义 `scheduleTimezone` 方法。 此方法应返回应分配给所有计划任务的默认时区：

    /**
     * 获取默认情况下应为预定事件使用的时区。
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/Chicago';
    }

> {note} 请记住，有些时区会使用夏令时。当夏时制时间发生更改时，你的任务可能会执行两次，甚至根本不会执行。所以我们建议尽可能避免使用时区来安排计划任务。

<a name="preventing-task-overlaps"></a>
### 避免任务重复

默认情况下，即使之前的任务还在执行，调度内任务也会执行。你可以使用 `withoutOverlapping` 方法来避免这种情况：

    $schedule->command('emails:send')->withoutOverlapping();

在这个例子中，如果 `emails:send` [Artisan 命令](/docs/{{version}}/artisan) 没有正在运行，它将会每分钟执行一次。如果你的任务执行时间不确定，且你又不能准确预估出任务的执行时间，那么  `withoutOverlapping` 方法会显得特别有用。

如果有需要，你可以指定「without overlapping」锁指定的时间范围。默认情况下，锁将在 24 小时后过期。

    $schedule->command('emails:send')->withoutOverlapping(10);
		
<a name="running-tasks-on-one-server"></a>
### 任务只运行在一台服务器上

> {note} 要使用这个特性，你的应用默认缓存驱动必须是 `memcached` 或者 `redis`。除此之外，所有的服务器必须使用同一个中央缓存服务器通信。

如果你的应用在多个服务器上运行，你可能需要限制你的调度任务只在单个服务器上运行。假设你有一个调度任务：每周五晚生成一份新报告。如果这个任务调度器在三个服务器上运行，那么这个任务会在三台服务器上运行且生成三份报告。这样不好！

为了说明任务应该在单个服务器上运行，在定义调度任务时使用 `onOneServer` 方法。第一个获取到任务的服务器会生成一个原子锁，用来防止其他服务器在同一时刻执行相同任务。

    $schedule->command('report:generate')
                    ->fridays()
                    ->at('17:00')
                    ->onOneServer();

<a name="background-tasks"></a>
### 后台任务

默认情况下，同时调度的多个命令将按顺序执行。如果你有长时间运行的命令，这可能会导致后续命令的启动时间比预期的要晚。因此，你想在后台同时运行命令，可以使用 `runInBackground` 方法：

    $schedule->command('analytics:report')
             ->daily()
             ->runInBackground();



<a name="maintenance-mode"></a>
### 维护模式

Laravel 的队列任务在 [维护模式](/docs/{{version}}/configuration#maintenance-mode) 下不会运行。因为我们不想你的调度任务干扰到你服务器上可能还未完成的项目。不过，如果你确实是想在维护模式下强制调度任务执行，你可以使用 `evenInMaintenanceMode` 方法：

    $schedule->command('emails:send')->evenInMaintenanceMode();

<a name="task-output"></a>
## 任务输出

Laravel 调度器提供了一些方便的方法来处理调度任务输出。首先，你可以使用 `sendOutputTo` 方法来输出到文件以便于后续检查：

    $schedule->command('emails:send')
             ->daily()
             ->sendOutputTo($filePath);

如果希望将输出 `附加` 到给定文件，可以使用 `appendOutputTo` 方法

    $schedule->command('emails:send')
             ->daily()
             ->appendOutputTo($filePath);

使用 `emailOutputTo` 方法，你可以将输出发送到指定邮箱。在使用邮件发送之前，你需要配置 Laravel 的 [邮件服务](/docs/{{version}}/mail)：

    $schedule->command('foo')
             ->daily()
             ->sendOutputTo($filePath)
             ->emailOutputTo('foo@example.com');

> {note} `emailOutputTo`，`sendOutputTo` 和 `appendOutputTo` 方法是  `command` 和 `exec` 独有的。

<a name="task-hooks"></a>
## 任务钩子

使用 `before` 和 `after` 方法，你可以在调度任务执行前或者执行后来执行特定代码：

    $schedule->command('emails:send')
             ->daily()
             ->before(function () {
                 // Task is about to start...
             })
             ->after(function () {
                 // Task is complete...
             });

#### Ping 网址

使用 `pingBefore` 和 `thenPing` 方法，你可以在任务执行前或者执行后来 ping 指定的 URL。这个方法在通知外部服务（比如 [Laravel Envoyer](https://envoyer.io)）时将会特别有用：

    $schedule->command('emails:send')
             ->daily()
             ->pingBefore($url)
             ->thenPing($url);

只有在给定条件为 `true` 时，才能使用 `pingBeforeIf` 和 `thenPingIf` 方法 ping 指定的 URL：

    $schedule->command('emails:send')
             ->daily()
             ->pingBeforeIf($condition, $url)
             ->thenPingIf($condition, $url);

所有 ping 方法都需要 Guzzle HTTP 库。你可以使用 Composer 来添加 Guzzle 到你的项目中：

    composer require guzzlehttp/guzzle