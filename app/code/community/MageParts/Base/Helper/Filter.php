<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Helper_Filter extends MageParts_Base_Helper_Data
{

    /**
     * Negative words used in filtering.
     *
     * @var array
     */
    protected $_negativeWords;

    /**
     * Positive words used in filtering.
     *
     * @var array
     */
    protected $_positiveWords;

    /**
     * Fetch positive word list.
     *
     * @return string
     */
    public function fetchList()
    {
        $list = Mage::helper('mageparts_base/api')->fetch('list', 'positive_words');
        $negative = Mage::helper('mageparts_base/api')->fetch('list', 'negative_words');
    }

    /**
     * Checks a text to see if it's negative or positive.
     *
     * @param string $text
     * @return boolean
     */
    public function isPositive($text='')
    {

    }

    /**
     * Synonymous with isPositive but with reversed return value.
     *
     * @return boolean
     */
    public function isNegative($text='')
    {
        return !$this->isPositive($text);
    }

    /**
     * Censure negative words in a text.
     *
     * @param string $text
     * @return string
     */
    public function filterNegativeWords($text='')
    {

    }

    /**
     * Censure positive words in a text.
     *
     * @param string $text
     * @return string
     */
    public function filterPositiveWords($text='')
    {

    }

    /**
     *
     */

    public function getNegativeWords()
    {

    }

    public function getPositiveWords()
    {

    }

    public function calculate()
    {

    }

    /**
     * Check if debugger is enabled or not for content filtering functions.
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return parent::isDebugEnabled() && Mage::getStoreConfigFlag('mageparts_base/debug/filter');
    }

}


/*


// man bör kunna ställa in "undvik dessa ord" typ, för positive / negative

    $positive = array(
        'good' => 1,
        'terrific' => 2,
        'nice' => 1.5,
        'awesome' => 3,
        'great' => 1.5,
        'personal' => 1,
        'cool' => 1,
        'elite' => 1.5,
        'imba' => 1.5,
        'pro' => 1,
        'best' => 2,
        'better' => 1,
        'heroic' => 2,
        'epic' => 2,
        'real' => 0.5,
        'amazing' => 3
    );

    $modifiers_increase = array("don't", "aren't", "isn't", "dont", "isnt", "arent", "are not", "do not", "not");

    $negative = array(
        'hate' => 2,
        'bad' => 1,
        'sad' => 1,
        'lag' => 1,
        'hag' => 1,
        'brag' => 1,
        'damn' => 1.5,
        'hell' => 1.5,
        'murder' => 5,
        'muppet' => 2,
        'fuck' => 5,
        'falter' => 1,
        'suck' => 1,
        'clown' => 1.5,
        'die' => 3
    );

    $modifiers_decrease = array("don't", "aren't", "isn't", "dont", "isnt", "arent", "are not", "do not", "not");

    $score_neutral_words = true;

    $positive_word_radius = 3;
    $negative_word_radius = 2;
    $modifier_radius = 2;

    $str = "There are no better people than these guys, really, they are the best! No I'm just kidding, you are fucking clownshoes, die in hell I hate you forever! No but seriously these guys truly are amazing, best in the business, really! :-)";
    $strPieces = explode(' ', $str);

    $negative_points = 20;
    $positive_points = 10;
    $neutral_points = 1;

    $positive_word_catches = array();
    $negative_word_catches = array();

    $score = 0;

    for ($i=0; $i<count($strPieces); $i++) {
        $raw = $strPieces[$i];
        $word = preg_replace('/([\!\,\/\_\?\.\:\;])/', '', $raw);

        $scoreModifier = 0;

        if (isset($negative[$word])) {
            $positiveUpFront = false;

            for ($j=0; $j<($positive_word_radius+1); $j++) {
                if (isset($strPieces[$i+$j]) && isset($positive[$strPieces[$i+$j]])) {
                    $positiveUpFront = true;
                    break;
                }
            }

            $modifyMatch = false;

            for ($j=0; $j<($modifier_radius+1); $j++) {
                if (isset($strPieces[$i-$j]) && in_array($strPieces[$i-$j], $modifiers_decrease)) {
                    $modifyMatch = true;
                    break;
                }
            }

            if (!$modifyMatch && (!$positive_word_radius || !((count($positive_word_catches) && $positive_word_catches[count($positive_word_catches)-1]['index'] >= ($i-$positive_word_radius)) || $positiveUpFront))) {
                $scoreModifier = ($negative_points * $negative[$word]) - $negative_points;

                $scoreModifier+= $raw[(strlen($raw)-1)] == '!' ? 10 : 0;
                $scoreModifier+= $raw[(strlen($raw)-1)] == '?' ? 5 : 0;

                $wordScore = ($negative_points + $scoreModifier);

                if ($wordScore > 0 && count($negative_word_catches) && $negative_word_catches[count($negative_word_catches)-1]['index'] >= ($i-$negative_word_radius)) {
                    $wordScore*= 2;
                }

                $score+= $wordScore;

                $negative_word_catches[] = array(
                    'index' => $i,
                    'word' => $word,
                    'ra_word' => $raw,
                    'score' => $wordScore
                );
            }
        } else if (isset($positive[$word])) {
            $negativeUpFront = false;

            for ($j=0; $j<($negative_word_radius+1); $j++) {
                if (isset($strPieces[$i+$j]) && isset($negative[$strPieces[$i+$j]])) {
                    $negativeUpFront = true;
                    break;
                }
            }

            $modifyMatch = false;

            for ($j=0; $j<($modifier_radius+1); $j++) {
                if (isset($strPieces[$i-$j]) && in_array($strPieces[$i-$j], $modifiers_decrease)) {
                    $modifyMatch = true;
                    break;
                }
            }

            if (!$modifyMatch && (!$negative_word_radius || !((count($negative_word_catches) && $negative_word_catches[count($negative_word_catches)-1]['index'] >= ($i-$negative_word_radius)) || $negativeUpFront))) {
                $scoreModifier = ($positive_points * $positive[$word]) - $positive_points;

                $scoreModifier+= $raw[(strlen($raw)-1)] == '!' ? 10 : 0;
                $scoreModifier+= $raw[(strlen($raw)-1)] == '?' ? 5 : 0;

                $wordScore = ($positive_points + $scoreModifier);

                if ($wordScore > 0 && count($positive_word_catches) && $positive_word_catches[count($positive_word_catches)-1]['index'] >= ($i-$positive_word_radius)) {
                    $wordScore*= 2;
                }

                $score-= $wordScore;

                $positive_word_catches[] = array(
                    'index' => $i,
                    'word' => $word,
                    'raw_ord' => $raw,
                    'score' => $wordScore
                );
            }
        } else if ($score_neutral_words) {
            foreach ($negative as $k => $v) {
                if (preg_match("/" . $k . "/isU", $word)) {
                    $strPieces[$i] = $k;
                    $i--;
                    continue 2;
                }
            }

            foreach ($positive as $k => $v) {
                if (preg_match("/" . $k . "/isU", $word)) {
                    $strPieces[$i] = $k;
                    $i--;
                    continue 2;
                }
            }

            $score-= $neutral_points;
        }
    }

    var_dump($score);
    var_dump($positive_word_catches);
    var_dump($negative_word_catches);

*/