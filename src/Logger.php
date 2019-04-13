<?php

namespace Paliari\Utils;

use RuntimeException,
    LogicException,
    Exception;

class Logger
{

    const CRITICAL = 'CRITICAL';
    const ERROR    = 'ERROR';
    const WARNING  = 'WARNING';
    const NOTICE   = 'NOTICE';
    const INFO     = 'INFO';
    const DEBUG    = 'DEBUG';

    public static $format = "%s - %s - %s - %s\n";

    public static $date_format = 'Y-m-d H:i:s';

    protected static $file = '';

    protected static function prepare($message, $level)
    {
        $data = [date(static::$date_format), static::machine(), $level, static::convertMessage($message)];

        return vsprintf(static::$format, $data);
    }

    protected static function machine()
    {
        return A::get($_SERVER, 'SERVER_ADDR', 'localhost');
    }

    /**
     * Write a raw message to the log.
     *
     * @param string $content
     *
     * @return bool
     */
    protected static function write($content)
    {
        $f = fopen(static::file(), 'a+');
        if (!$f) {
            throw new LogicException('Could not open file for writing!');
        }
        if (!flock($f, LOCK_EX)) {
            throw new RuntimeException('Could not lock file!');
        }
        fwrite($f, $content);
        flock($f, LOCK_UN);

        return fclose($f);
    }

    /**
     * @param mixed $message
     *
     * @return string
     */
    protected static function convertMessage($message)
    {
        $msg = '';
        if ($message instanceof Exception) {
            $msg .= "Error:\n$message\n";
            $msg .= str_repeat('-', 50);
        } else if (static::isNoPrint($message)) {
            $msg .= 'Object error:' . PHP_EOL . var_export($message, true) . PHP_EOL;
            $msg .= str_repeat('-', 50);
        } else {
            $msg = $message;
        }

        return $msg;
    }

    /**
     * @param mixed $v
     *
     * @return bool
     */
    protected static function isNoPrint($v)
    {
        return ((is_object($v) && !method_exists($v, '__toString')) || is_array($v) || is_bool($v) || is_null($v));
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public static function file($file = null)
    {
        if (null !== $file) {
            static::$file = $file;
        }
        if (!static::$file) {
            static::$file = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'php-util.log';
        }

        return static::$file;
    }

    /**
     * @param mixed  $message
     * @param string $level
     *
     * @return bool
     */
    public static function log($message, $level = self::ERROR)
    {
        return static::write(static::prepare($message, $level));
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public static function critical($message)
    {
        return static::log($message, static::CRITICAL);
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public static function error($message)
    {
        return static::log($message, static::ERROR);
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public static function warning($message)
    {
        return static::log($message, static::WARNING);
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public static function notice($message)
    {
        return static::log($message, static::NOTICE);
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public static function info($message)
    {
        return static::log($message, static::INFO);
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public static function debug($message)
    {
        return static::log($message, static::DEBUG);
    }

}
