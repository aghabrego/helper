<?php

namespace Tests\Unit;

use Tests\TestCase;
use Weirdo\Helper\BaseClass;
use Weirdo\Helper\Lib\VCard\VCard;

class LibVCardTest extends TestCase
{
    public function testVCard()
    {
        $helper = new BaseClass;
        $strEncoding = 'Windows-1252';

        VCard::setEncoding($strEncoding);

        $base = new VCard();
        $dir = $helper->getDirname(__DIR__ . '/Unit');
        $resultPath = $helper->getSystemRoute($dir, "/contact.vcard");
        $iCC  = $base->read($resultPath);
        $this->assertEquals($iCC, 1);

        for ($i = 0; $i < $iCC; $i++) {
            $oContact = $base->getContact($i);
            echo '<h1>' . $oContact->getName() . '</h1>' . PHP_EOL;
            $strPortraitBlob = $oContact->getPortraitBlob();
            if (strlen($strPortraitBlob) > 0 ) {
                echo '<img style="float: left; margin: 0px 20px;" src="' . $strPortraitBlob . '">' . PHP_EOL;
                $oContact->savePortrait('myimage' . ($i + 1));
            }
            echo $oContact->getPrefix() . '<br/>' . PHP_EOL;
            echo $oContact->getLastName() . ', ' . $oContact->getFirstName() . '<br/>' . PHP_EOL;
            echo 'Nickname: ' . $oContact->getNickName() . '<br/>' . PHP_EOL;
            echo 'Birthday: ' . $oContact->getDateOfBirth() . '<br/><br/>' . PHP_EOL;
            echo 'Company: ' . $oContact->getOrganisation() . '<br/><br/><br/>' . PHP_EOL;

            // test iterating through all addresses
            $iAC = $oContact->getAddressCount();
            for ($j = 0; $j < $iAC; $j++) {
                $oAddress = $oContact->getAddress($j);
                echo '<div style="width: ' . (100.0/$iAC) . '%; float: left;">' . PHP_EOL;
                echo ' <b>Address: ' . $oAddress->getType() . '</b><br>' . PHP_EOL;
                echo ' ' . $oAddress->getStr() . '<br>' . PHP_EOL;
                echo ' ' . $oAddress->getPostcode() . ' ' . $oAddress->getCity() . '<br>' . PHP_EOL;
                echo ' ' . $oAddress->getRegion() . ' ' . $oAddress->getCountry() . '<br>' . PHP_EOL;
                echo '</div>' . PHP_EOL;
            }

            // test for direct access via type
            echo '<div style="clear: both;"><br/>' . PHP_EOL;
            $oAddress = $oContact->getAddress(VCard::WORK);
            if ($oAddress) {
                echo '<div style="width: 50%; float: left;">' . PHP_EOL;
                echo ' <b>Address at Work:</b><br>' . PHP_EOL;
                echo ' ' . $oAddress->getStr() . '<br>' . PHP_EOL;
                echo ' ' . $oAddress->getPostcode() . ' ' . $oAddress->getCity() . '<br>' . PHP_EOL;
                echo ' ' . $oAddress->getRegion() . ' ' . $oAddress->getCountry() . '<br>' . PHP_EOL;
                echo '</div>' . PHP_EOL;
            }
            $oAddress = $oContact->getAddress(VCard::HOME);
            if ($oAddress) {
                echo '<div style="width: 50%; float: left;">' . PHP_EOL;
                echo ' <b>Address at Home:</b><br>' . PHP_EOL;
                echo ' ' . $oAddress->getStr() . '<br>' . PHP_EOL;
                echo ' ' . $oAddress->getPostcode() . ' ' . $oAddress->getCity() . '<br>' . PHP_EOL;
                echo ' ' . $oAddress->getRegion() . ' ' . $oAddress->getCountry() . '<br>' . PHP_EOL;
                echo '</div>' . PHP_EOL;
            }

            // phonenumbers
            echo '<div style="clear: both;"><br/>' . PHP_EOL;
            echo '<b>Phonenumbers:</b><br/>' . PHP_EOL;
            $iPC = $oContact->getPhoneCount();
            for ($j = 0; $j < $iPC; $j++) {
                $aPhone = $oContact->getPhone($j);
                echo $aPhone['strType'] . ': ' . $aPhone['strPhone'] . '<br>' . PHP_EOL;
            }

            // mailaddresses
            echo '<br/>' . PHP_EOL;
            echo '<b>e-Mailaddresses:</b><br/>' . PHP_EOL;
            $iPC = $oContact->getEMailCount();
            for ($j = 0; $j < $iPC; $j++) {
                echo 'Mail' . ($j+1) . ': ' . $oContact->getEMail($j) . '<br>' . PHP_EOL;
            }
            echo '<br/>' . PHP_EOL;

            $strNote = $oContact->getNote();
            if (strlen($strNote) > 0 )
            {
                echo '<b>Annotation (may contain multiline value)</b><br/>' . PHP_EOL;
                echo '<textarea cols="80" rows="5">' . $strNote . '</textarea>' . PHP_EOL;
                echo '<br/>' . PHP_EOL;
            }
        }
    }

    public function testVCardB()
    {
        $helper = new BaseClass;
        $strEncoding = 'Windows-1252';

        VCard::setEncoding($strEncoding);

        $base = new VCard();
        $dir = $helper->getDirname(__DIR__ . '/Unit');
        $resultPath = $helper->getSystemRoute($dir, "/b0ef0930b4e0b4d1f8cd9f0485b54648.vcf");
        $iCC  = $base->read($resultPath);
        $this->assertEquals($iCC, 1);

        for ($i = 0; $i < $iCC; $i++) {
            $oContact = $base->getContact($i);
            echo '<h1>' . $oContact->getName() . '</h1>' . PHP_EOL;
            // phonenumbers
            echo '<div style="clear: both;"><br/>' . PHP_EOL;
            echo '<b>Phonenumbers:</b><br/>' . PHP_EOL;
            $iPC = $oContact->getPhoneCount();
            for ($j = 0; $j < $iPC; $j++) {
                $aPhone = $oContact->getPhone($j);
                echo $aPhone['strType'] . ': ' . $aPhone['strPhone'] . '<br>' . PHP_EOL;
            }
        }
    }
}