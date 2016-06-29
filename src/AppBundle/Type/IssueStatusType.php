<?php

namespace AppBundle\Type;

class IssueStatusType extends EnumTypeAbstract
{
    protected $name = 'enum_issue_status';
    protected $values = array(self::OPEN, self::INPROGRESS, self::CLOSED);
    const OPEN = 'open';
    const INPROGRESS = 'in progress';
    const CLOSED = 'closed';
}
