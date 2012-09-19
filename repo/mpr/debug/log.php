<?php
namespace mpr\debug;

class log
{
    private static $trace = array();

    private static $output;
    private static $logfile;

    private static $initialized = 0;

    public static function isInitialized()
    {
        return self::$initialized;
    }

    private static $date_format = "H:i:s";

    public static function init()
    {
        if(!self::$initialized) {
            require_once __DIR__ . '/../io/response.php';
            if(GRUNGE_DEBUG_TYPE == 'stdout') {
                self::$output = new \grunge\system\io\response('stdout', defined("GRUNGE_DEBUG_BUFFER") ? GRUNGE_DEBUG_BUFFER : false);
            }
            self::$initialized = 1;
        }
    }

    public static function getBackTrace()
    {
        self::init();
        return self::$trace;
    }

    public static function log($string)
    {
        if(!self::$logfile) {
            if(!file_exists(GRUNGE_DEBUG_FILE)) {
                touch(GRUNGE_DEBUG_FILE);
            }
            self::$logfile = fopen(GRUNGE_DEBUG_FILE, 'a');
        }
        fwrite(self::$logfile, "{$string}\n");
    }

    public static function put($comment, $method = "", $lvl = 1)
    {
        if(GRUNGE_DEBUG) {
            self::init();
            $mtime = explode('.', microtime(true));
            $item = array(
                'time'      => date(self::$date_format, $mtime[0]) . '.' . (isset($mtime[1]) ? round($mtime[1], 4) : '0000'),
                'comment'   => $comment,
                'method'    => $method,
                'level'     => $lvl
            );
            if($lvl <= GRUNGE_DEBUG) {
                $string = "[{$item['time']}|{$item['method']}] {$item['comment']}";
                self::log($string);
                if(GRUNGE_DEBUG_TYPE == 'stdout') {
                    self::$output->writeLn($string);
                }
                if(GRUNGE_CONSOLE_SHOW_DEBUG) {
                    error_log($string);
                }
            }
        }
    }
}