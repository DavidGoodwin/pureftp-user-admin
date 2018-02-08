<?php
/**
 *
 */

namespace PureFTPAdmin;


class Flash
{

    private $messages = [];

    public function __construct() {
        $this->messages = ['info' => [], 'error' => [] ];
    }

    public function info($message) {
        $this->messages['info'][] = $message;
    }

    public function error($message) {
        $this->messages['error'][] = $message;
    }

    public function getMessages() {
        return $this->messages;
    }
}