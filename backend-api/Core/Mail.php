<?php

namespace BackendApi\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
    protected static $instance = null;
    protected $mailer;

    public function __construct() {
        $config = Config::get('services.mail');

        $this->mailer = new PHPMailer(true);

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['username'];
        $this->mailer->Password = $config['password'];
        $this->mailer->SMTPSecure = $config['encryption'];
        $this->mailer->Port = $config['port'];

        // Default sender
        $this->mailer->setFrom(
            $config['from']['address'],
            $config['from']['name']
        );
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function send($to, $subject, $body, $isHtml = true) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            return $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }
}