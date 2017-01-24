<?php

use PHPUnit\Framework\TestCase;

class SafeUrlTest extends PHPUnit_Framework_TestCase {

    public function testMakeUrl() {
        $this->assertEquals( SafeUrl::makeUrl(
            SafeUrl::convertCharacters("Vivid Recollections"), ['lowercase' => false]),
            "Vivid-Recollections");
    
        $this->assertEquals( SafeUrl::makeUrl(
            'i\'m a test string!! do u like me. or not......., billy bob!!@#', ['lowercase' => true]),
            'im-a-test-string-do-u-like-me-or-not-billy-bob');

        $this->assertEquals( SafeUrl::makeUrl(
            '<b>some HTML</b> in <i>here</i>!!~'),
            'some-html-in-here');

        $this->assertEquals( SafeUrl::makeUrl(
            'i!@#*#@ l#*(*(#**$*o**(*^v^*(e d//////e\\\\\\\\v,,,,,,,,,,n%$#@!~e*(+=t'),
            'i-love-devnet');

        $this->assertEquals( SafeUrl::makeUrl(
            'A lOng String wiTh a buNchess of words thats! should be -chopped- at the last whole word'),
            'a-long-string-with-a-bunchess-of-words-thats');

        $this->assertEquals( SafeUrl::makeUrl(
            'Eyjafjallajökull Glacier', ['lowercase' => false]),
            'Eyjafjallajokull-Glacier');

        $this->assertEquals( SafeUrl::makeUrl(
            'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ', ['maxlength' => 100]),
            'AAAAAAACEEEEIIIIDjNOOOOOOUUUUYBSsaaaaaaaceeeeiiiionoooooouuuyybyRr');

        $this->assertEquals( SafeUrl::makeUrl(
                $this->big_mess, ['maxlength' => 20]),
                'safeurl-new-safeurl');

        /**
         * Regresstion test:
         *
         * If max length was so small that we where left with only one
         * word, then whole_word would leave us with an empty string.
         */
        $this->assertEquals( SafeUrl::makeUrl(
            'supercalafragalisticexpialadoshus', ['maxlength' => 5, 'whole_word' => true]),
            'super');
        

        /**
         * Acceptable Bug:
         *
         * It would be nice if we put a space between block level elements,
         * but it is kind of too much to ask for.
         */
        $html = <<<HTML
            <div>
                <h1>Title</h1>
                <h2>Subtitle!</h2>Read the <a href="ReleaseNotes.html">Release Notes</a> for this Revision.<br/>
            </div>
HTML;
        $this->assertEquals( SafeUrl::makeUrl(
                $html, ['maxlength' => 200]),
                'Title-SubtitleRead-the-Release-Notes-for-this-Revision');
        /**                    ^
         * Look: --------------|
         *
         * Should be:
         *     'Title-Subtitle-Read-the-Release-Notes-for-this-Revision'
         */
    }
    
    var $big_mess = '
            </span></li><li style=\"\" class=\"li2\"><span style=\"color:
            #ff0000;\">\$safeurl = new safeurl(); </span></li><li style=\"\"
            class=\"li1\"><span style=\"color: #ff0000;\">\$safeurl->lowercase
            = false;</span></li><li style=\"\" class=\"li2\"><span
            style=\"color: #ff0000;\">\$safeurl->whole_word = false;</span></li>
            <li style=\"\" class=\"li1\">&nbsp;</li><li style=\"\"
            class=\"li2\"><span style=\"color: #ff0000;\">\$tests = array(
            </span></li><li style=\"\" class=\"li1\"><span style=\"color:
            #ff0000;\"> &nbsp; &nbsp; &nbsp; &nbsp;\'</span>i\span
            style=\"color: #ff0000;\">\'m a test string!! do u like me. or
            not......., billy bob!!@#\'</span>, </li><li style=\"\"
            class=\"li2\">&nbsp; &nbsp; &nbsp; &nbsp; <span
            style=\"color: #ff0000;\">\'<b>some HTML</b> in <i>here</i>!!~\'
            </span>, </li><li style=\"\" class=\"li1\">&nbsp; &nbsp; &nbsp;
            &nbsp; <span style=\"color: #ff0000;\">\'i!@#*#@ l#*(*(#**$*o**(*^v
            ^*(e d//////e<span style=\"color: #000099; font-weight: bold;\">\\
            </span><span style=\"color: #000099; font-weight: bold;\">\\</span>
            <span style=\"color: #000099; font-weight: bold;\">\\</span><span
            style=\"color: #000099; font-weight: bold;\">\\</span>v,,,,,,,,,,n%
            $#@!~e*(+=t\'</span>,</li>';
}
