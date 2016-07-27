<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsProperSubTask extends Constraint
{
    public $message = 'Only usual tasks can be added as sub-tasks, and only to the story task.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
