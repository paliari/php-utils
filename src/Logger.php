<?php

namespace Paliari\Utils;

use Exception;

class Logger
{
    const CRITICAL = 'CRITICAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';

    public static $format = "%s - %s - %s - %s\n";

    public static $date_format = 'Y-m-d H:i:s';

    protected static $file = '';

    protected static $scope;

    protected static $write_custom;

    protected static function machine()
    {
        return A::get($_SERVER, 'SERVER_ADDR', 'localhost');
    }

    /**
     * Set custom write.
     * Should receive the params: ($machine, $level, $message, $scope)
     *
     * @param callable $callable
     */
    public static function setWriteCustom($callable)
    {
        static::$write_custom = $callable;
    }

    protected static function write($machine, $level, $message, $scope)
    {
        if (static::$write_custom) {
            return call_user_func(static::$write_custom, $machine, $level, $message, $scope);
        }

        return static::writeFile(static::prepare($machine, $level, $message, $scope));
    }

    protected static function prepare($machine, $level, $message, $scope)
    {
        if ($scope) {
            $level .= ".$scope";
        }
        $data = [date(static::$date_format), $machine, $level, $message];

        return vsprintf(static::$format, $data);
    }

    /**
     * Write a raw message to the log.
     *
     * @param string $content
     *
     * @return bool
     */
    protected static function writeFile($content)
    {
        $stdout = fopen('php://stdout', 'w');

        return fwrite($stdout, $content);
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
     * @param string $scope
     *
     * @return string
     */
    public static function scope($scope = null)
    {
        if (null !== $scope) {
            static::$scope = $scope;
        }

        return static::$scope;
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

        return static::$file;
    }

    /**
     * @param mixed $message
     * @param string $level
     *
     * @return bool
     */
    public static function log($message, $level = self::ERROR)
    {
        return static::write(static::machine(), $level, static::convertMessage($message), static::scope());
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
