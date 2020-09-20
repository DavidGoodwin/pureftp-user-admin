<?php
/**
 *
 */

namespace PureFTPAdmin;


class Flash
{

    /**
     * @var array
     */
    private $messages = [];

    public function __construct() {
        $this->messages = ['info' => [], 'error' => [] ];
    }


    public function info(string $message) : void {
        $this->messages['info'][] = $message;
    }

    public function error(string $message) : void {
        $this->messages['error'][] = $message;
    }

    public function getMessages() : array {
        return $this->messages;
    }
}
