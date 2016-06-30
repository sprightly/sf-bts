<?php

namespace AppBundle\Type;

class IssueResolutionType extends EnumTypeAbstract
{
    protected $name = 'enum_issue_resolution';
    protected $values = array(self::FIXED, self::WONTFIX, self::CANNOT_REPRODUCE, self::DONE, self::WONTDONE, null);
    const FIXED = 'fixed';
    const WONTFIX = 'wont fix';
    const CANNOT_REPRODUCE = 'cannot reproduce';
    const DONE = 'done';
    const WONTDONE = 'wont done';
}
