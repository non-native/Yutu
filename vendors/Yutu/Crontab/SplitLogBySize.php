<?php
/**
 * SplitLogBySize.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/21 0021
 * Time: 15:05
 */

namespace Yutu\Crontab;


use Yutu\Env;
use Yutu\Interfaces\ICrontab;

/**
 * 根据日志文件大小进行归档
 * Class SplitLogBySize
 * @package Yutu\task
 */
class SplitLogBySize implements ICrontab
{
    /**
     * 单个文件最大
     * @var int
     */
    private $maxSize = 2097152;

    /**
     * @return int|mixed
     */
    public function Type()
    {
        return ICrontab::TickTask;
    }

    /**
     * 两分钟秒执行一次
     * @return int|mixed
     */
    public function Time()
    {
        return 120;
    }

    /**
     * @return mixed|void
     */
    public function Executor()
    {
        clearstatcache();

        $file = PATH_LOGS . "/" . Env::YUTU_LOG_FILE;

        // 服务器日志
        if (!is_file($file) || floor($this->maxSize) > filesize($file)) {
            return;
        }

        $path = PATH_BACKUP . "/" . date("Ymd") . "/";
        !is_dir($path) && mkdir($path);

        $newFile = str_replace(".log", "-" . time() . ".log", Env::YUTU_LOG_FILE);
        $newDestination = $path . "/" . $newFile;
        rename($file, $newDestination);

        // 通知swoole重新加载日志文件
        $serverPid = Env::ServerPid();
        !empty($serverPid) && exec("kill -34 {$serverPid}");
    }
}