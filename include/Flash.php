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

    /**
     * @param string $message
     * @return void
     */
    public function info($message) {
        $this->messages['info'][] = $message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function error($message) {
        $this->messages['error'][] = $message;
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }
}
