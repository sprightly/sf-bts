<?php
namespace AppBundle\Tests\Unit\Type;

use AppBundle\Type\IssueStatusType;
use Doctrine\DBAL\Platforms\MySqlPlatform;

class IssueStatusTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertToDatabaseValue()
    {
        $platform = new MySqlPlatform();
        $enumIssueStatus = IssueStatusType::getType('enum_issue_status');
        $this->expectException(\InvalidArgumentException::class);
        $enumIssueStatus->convertToDatabaseValue('not-allowed-value', $platform);
    }

    public function testGetSQLDeclaration()
    {
        $platform = new MySqlPlatform();
        $enumIssueStatus = IssueStatusType::getType('enum_issue_status');

        $expectedSQLDeclaration = "ENUM('open', 'in progress', 'closed') COMMENT '(DC2Type:enum_issue_status)'";
        $SQLDeclaration = $enumIssueStatus->getSQLDeclaration(array(), $platform);
        $this->assertEquals($expectedSQLDeclaration, $SQLDeclaration);
    }
}
