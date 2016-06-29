<?php

namespace AppBundle\Type;

class IssuePriorityType extends EnumTypeAbstract
{
    protected $name = 'enum_issue_priority';
    protected $values = array(self::BLOCKER, self::MAJOR, self::CRITICAL, self::MINOR, self::TRIVIAL);
    const BLOCKER = 'blocker';
    const MAJOR = 'major';
    const CRITICAL = 'critical';
    const MINOR = 'minor';
    const TRIVIAL = 'trivial';
}
