<?php
namespace grunge\system\service;

/**
 * Description of mailer
 *
 * @author GreeveX <greevex@gmail.com>
 */
class mailer {
    
    public static $instance;
    
    /**
     *
     * @return \PHPMailer
     */
    public static function factory()
    {
        if(self::$instance == null) {
            fileLoader::load(\grunge\system\systemConfig::$pathToRoot . '/libs/PHPMailer/class.phpmailer.php');
            self::$instance = new \PHPMailer();
            self::$instance->IsSendmail();
            self::$instance->IsHTML();
            self::$instance->CharSet = 'utf-8';
            self::$instance->ContentType = 'text/html';
            self::$instance->SetFrom('noreply@ilook.ru', 'iLook NoReply');
            self::$instance->AddCustomHeader('Content-Type: text/html; charset="UTF-8"');
        }
        return self::$instance;
    }
    
}
?>