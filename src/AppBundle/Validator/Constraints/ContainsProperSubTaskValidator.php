<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Issue;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsProperSubTaskValidator extends ConstraintValidator
{
    /**
     * @param Issue $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ((!$value->getChildren()->isEmpty() && !$this->isStory($value))
            || ($value->getParent() && !$this->isSubTask($value))
        ) {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @noinspection PhpUndefinedFieldInspection */
            $this->context->buildViolation($constraint->message)
                ->setCode(1)
                ->addViolation();
        }
    }

    public function isStory(Issue $issue)
    {
        if (Issue::TYPE_STORY == $issue->getType()) {
            return true;
        } else {
            return false;
        }
    }

    private function isSubTask(Issue $issue)
    {
        if (Issue::TYPE_SUBTASK == $issue->getType()) {
            return true;
        } else {
            return false;
        }
    }
}
