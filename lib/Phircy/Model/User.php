<?php

namespace Phircy\Model;

/**
 * @package Phircy\Model
 */
class User {
    /**
     * @var string
     */
    public $nick;

    /**
     * @var string
     */
    public $address;

    /**
     * @var array
     */
    public $modes = array();
}