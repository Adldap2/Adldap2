<?php

namespace Adldap\Tests\Classes;

use Adldap\Classes\AdldapUtils;
use Adldap\Tests\FunctionalTestCase;


class AdldapUtilityTest extends FunctionalTestCase
{
    protected function newUtilityMock()
    {
        return $this->mock('Adldap\Classes\AdldapUtils');
    }

    public function testUtilityValidateLdapIsBoundPass()
    {
        $adldap = $this->mock('Adldap\Adldap');

        $adldap
            ->shouldReceive('getLdapConnection')->andReturn(true)
            ->shouldReceive('getLdapBind')->andReturn(true)
            ->shouldReceive('close')->andReturn(true);

        $utility = new AdldapUtils($adldap);

        $this->assertTrue($utility->validateLdapIsBound());
    }

    public function testUtilityValidateLdapIsBoundFailure()
    {
        $adldap = $this->mock('Adldap\Adldap');

        $adldap
            ->shouldReceive('getLdapConnection')->andReturn(true)
            ->shouldReceive('getLdapBind')->andReturn(false)
            ->shouldReceive('close')->andReturn(true);

        $utility = new AdldapUtils($adldap);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $utility->validateLdapIsBound();
    }

    public function testUtilityValidateNotNullPass()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $null = null;

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $utility->validateNotNull('Null', $null);
    }

    public function testUtilityValidateNotNullFailure()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $notNull = 'Not Null';

        $this->assertTrue($utility->validateNotNull('Not Null', $notNull));
    }

    public function testUtilityValidateNotEmptyPass()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $empty = '';

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $utility->validateNotEmpty('Empty', $empty);
    }

    public function testUtilityValidateNotEmptyFailure()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $notEmpty = 'Not Empty';

        $this->assertTrue($utility->validateNotEmpty('Not Empty', $notEmpty));
    }

    public function testUtilityStrGuidToHex()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $str = '{25892e17-80f6-415f-9c65-7395632f0223}';

        $result = $utility->strGuidToHex($str);

        $this->assertEquals('\e1\92\58\{2\0f\78\15\64\f9\c6\57\39\56\32\f0\22\3}', $result);
    }

    public function testUtilityBoolToStr()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $boolTrue = true;
        $boolFalse = false;

        $true = $utility->boolToStr($boolTrue);
        $false = $utility->boolToStr($boolFalse);

        $this->assertEquals('TRUE', $true);
        $this->assertEquals('FALSE', $false);
    }

    public function testUtilityDnStrToArray()
    {
        $utility = $this->newUtilityMock()->makePartial();

        $dn = 'CN=Karen Berge,CN=admin,DC=corp,DC=Fabrikam,DC=COM';

        $includedExpected = [
            'count' => 5,
            "CN=Karen Berge",
            "CN=admin",
            "DC=corp",
            "DC=Fabrikam",
            "DC=COM",
        ];

        $includedAttributes = $utility->dnStrToArr($dn, true, true);

        $this->assertEquals($includedExpected, $includedAttributes);

        $notIncludedExpected = [
            'count' => 5,
            "Karen Berge",
            "admin",
            "corp",
            "Fabrikam",
            "COM"
        ];

        $notIncludedAttributes = $utility->dnStrToArr($dn, true, false);

        $this->assertEquals($notIncludedExpected, $notIncludedAttributes);

    }
}