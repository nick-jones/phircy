<?php

namespace Phircy\Model;

/**
 * @package Phircy\Model
 */
class Channel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $modes = array();

    /**
     * @var User[]
     */
    public $users;

    /**
     * @param string $name
     * @param array $modes
     */
    public function __construct($name, array $modes = array())
    {
        $this->name = $name;
        $this->modes = $modes;
    }
}