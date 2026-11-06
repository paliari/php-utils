<?php
namespace Paliari\Utils;

class CatchFatalError
{
    /**
     * @var callable
     */
    protected static $handler;

    /**
     * @param callable $handler
     */
    public static function init($handler = null)
    {
        if (is_callable($handler)) {
            static::$handler = $handler;
        }
        ob_start(function ($output) {
            return static::handleOutput($output, error_get_last());
        });
    }

    /**
     * Output handlers must return a string; false keeps the original buffer.
     *
     * @param string     $output
     * @param array|null $e
     *
     * @return string
     */
    protected static function handleOutput($output, $e)
    {
        if (!isset($e['type']) || E_ERROR != $e['type']) {
            return $output;
        }
        $ret = static::fatalErrorHandler($e);

        return false === $ret ? $output : (string)$ret;
    }

    /**
     * se o erro for fatal, não adianta fazer nada, debug_backtrace ou exception,
     * só resta logar e retornar a string contendo o erro fatal.
     */
    protected static function fatalErrorHandler($e)
    {
        $ret = static::defaultErrorHandler($e);
        if ($handler = static::$handler) {
            $ret = $handler($e) ?: $ret;
        }
        Logger::critical(static::formatError($e));

        return $ret;
    }

    public static function defaultErrorHandler($e)
    {
        header('Content-Type: application/json');
        http_response_code(500);

        return json_encode(['error' => $e, 'message' => 'Fatal Error!']);
    }

    protected static function formatError($e)
    {
        $str = 'Fatal Error: ';
        foreach ($e as $k => $v) {
            $str .= "\n  $k: $v";
        }
        $str .= "\n" . str_repeat('-', 50);

        return $str;
    }
}
