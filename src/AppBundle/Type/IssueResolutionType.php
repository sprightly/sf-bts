<?php

namespace AppBundle\Type;

class IssueResolutionType extends EnumTypeAbstract
{
    protected $name = 'enum_issue_resolution';
    protected $values = array(self::BLOCKER, self::MAJOR, self::CRITICAL, self::MINOR, self::TRIVIAL);
    const BLOCKER = 'blocker';
    const MAJOR = 'major';
    const CRITICAL = 'critical';
    const MINOR = 'minor';
    const TRIVIAL = 'trivial';
}
