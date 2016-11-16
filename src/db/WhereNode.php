<?php

namespace penguin\db;

class WhereNode
{
    const AND_NODE = 'AND';
    const OR_NODE = 'OR';
    public $children;
    public function add($clause, $type)
    {
    }
}
