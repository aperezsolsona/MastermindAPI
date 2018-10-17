<?php
/**
 * Created by PhpStorm.
 * User: slimbook
 * Date: 9/10/18
 * Time: 11:02
 */

namespace MastermindAPI\Test;

use MastermindAPI\DTO\GuessResultDTO;
use MastermindAPI\Exception\ValidationException;
use MastermindAPI\Service\MastermindService;
use PHPUnit\Framework\TestCase;


//require_once 'MastermindTestCase.php';

final class MastermindServiceTest extends TestCase
{

    /**
     *
     */
    public function testGetMatches() {

        $mastermind = new MastermindService();

        $guessArray = array('R','R','O','B');
        $secretCode = array('R','R','R','R');
        $result = $this->invokeMethod($mastermind, 'getMatches', array(&$guessArray, &$secretCode));
        $this->assertEquals(2, $result);

        $guessArray = array('R','O','O','B');
        $secretCode = array('R','R','O','R');
        $result = $this->invokeMethod($mastermind, 'getMatches', array(&$guessArray, &$secretCode));
        $this->assertEquals(2, $result);

        $guessArray = array('R','O','O','B');
        $secretCode = array('R','R','O','B');
        $result = $this->invokeMethod($mastermind, 'getMatches', array(&$guessArray, &$secretCode));
        $this->assertEquals(3, $result);

        $guessArray = array('R','Y','O','B');
        $secretCode = array('R','Y','O','B');
        $result = $this->invokeMethod($mastermind, 'getMatches', array(&$guessArray, &$secretCode));
        $this->assertEquals(4, $result);

        $guessArray = array('R','R','R','R');
        $secretCode = array('R','R','R','R');
        $result = $this->invokeMethod($mastermind, 'getMatches', array(&$guessArray, &$secretCode));
        $this->assertEquals(4, $result);

        $guessArray = array('R','Y','O','R');
        $secretCode = array('R','Y','O','R');
        $result = $this->invokeMethod($mastermind, 'getMatches', array(&$guessArray, &$secretCode));
        $this->assertEquals(4, $result);

    }


    /**
     *
     */
    public function testNullValuesGetMAtches() {

        $mastermind = new MastermindService();

        $this->expectException(\Exception::class);
        $guessArray = array('R',null,'O','B');
        $secretCode = array('R','R','R','R');
        $result = $this->invokeMethod($mastermind, 'getMatches', array($guessArray, $secretCode));


        $this->expectException(\Exception::class);
        $guessArray = array('R','R','O','B');
        $secretCode = array('R','R',null,'R');
        $result = $this->invokeMethod($mastermind, 'getMatches', array($guessArray, $secretCode));
    }


    /**
     *
     */
    public function testGetImperfectMatches() {

        $mastermind = new MastermindService();

        $guessArray = array(null,null,null,null);
        $secretCode = array('G','O','R','Y');
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(0, $result); //empty case, no white pegs

        $guessArray = array('R',null,null,null);
        $secretCode = array('G','O','R','Y');
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(1, $result); // one white peg

        $guessArray = array('R',null,'Y','O');
        $secretCode = array('G','O','R','Y');
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(3, $result); // three white pegs
        $guessArray = array('R','G','Y','O');
        $secretCode = array('G','O','R','Y');
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(4, $result); // four white pegs

        $guessArray = array('R',null,null,null);
        $secretCode = array('G',null,'R',null);
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(1, $result); // one white peg

        $guessArray = array('R','G',null,null);
        $secretCode = array('G',null,'R',null);
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(2, $result); // one white peg

        $guessArray = array(null,null,'R',null);
        $secretCode = array('G',null,'R',null);
        $result = $this->invokeMethod($mastermind, 'getImperfectMatches', array($guessArray, $secretCode));
        $this->assertEquals(1, $result); // one white peg in same position. This case is exact match, but algorythm should not reach this case. Anyway, doesnt crash.
    }

    /**
     *
     */
    public function testValidateCode() {

        $mastermind = new MastermindService();

        //empty arrays
        $this->expectException(ValidationException::class);
        $guessArray = array();
        $result = $mastermind->validateCode($guessArray);

        //bad JSON
        $this->expectException(ValidationException::class);
        $guessArray = '{hello,.}';
        $result = $mastermind->validateCode($guessArray);

        //incomplete
        $this->expectException(ValidationException::class);
        $guessArray = array('R','R');
        $result = $mastermind->validateCode($guessArray);
        //null and empty values
        $this->expectException(ValidationException::class);
        $guessArray = array('R',null,'R','');
        $result = $mastermind->validateCode($guessArray);
        //invalid value Z
        $this->expectException(ValidationException::class);
        $guessArray = array('R','Z','R','R');
        $result = $mastermind->validateCode($guessArray);
        //invalid value 8
        $this->expectException(ValidationException::class);
        $guessArray = array('R','8','R','8');
        $result = $mastermind->validateCode($guessArray);

        $this->expectException(ValidationException::class);
        $guessArray = json_encode(array('R','8','R','8'));
        $result = $mastermind->validateCode($guessArray);

        //valid
        $guessArray = array('R','O','B','R');
        $result = $mastermind->validateCode($guessArray);
        $this->assertTrue($result);
        //valid JSON
        $guessArray = json_encode(array('R','O','B','R'));
        $result = $mastermind->validateCode($guessArray);
        $this->assertTrue($result);

    }

    /**
     *
     */
    public function testPrintListWithCommas(){

        $mastermind = new MastermindService();

        $list = null;
        $result = $this->invokeMethod($mastermind, 'printListWithCommas', array($list));
        $this->assertEquals('',$result);

        $list = array();
        $result = $this->invokeMethod($mastermind, 'printListWithCommas', array($list));
        $this->assertEquals('',$result);

        $list = array(null, 'A', 'B' , '');
        $result = $this->invokeMethod($mastermind, 'printListWithCommas', array($list));
        $this->assertEquals('-NULL-, A, B, -NULL-',$result);
    }

    /**
     * Evaluates correct guesses
     */
    public function testEvaluateGuess() {

        $mastermind = new MastermindService();


        $secretCode = array('G','O','R','Y'); //FIRST CODE TO CRACK

        $guessArray = array('G','Y','O','B');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(1, $result->getBlackPegs());
        $this->assertEquals(2, $result->getWhitePegs());

        $guessArray = array('G','O','O','B');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(2, $result->getBlackPegs());
        $this->assertEquals(0, $result->getWhitePegs());

        $guessArray = array('G','O','B','Y');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(3, $result->getBlackPegs());
        $this->assertEquals(0, $result->getWhitePegs());

        $guessArray = array('G','O','Y','R');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(2, $result->getBlackPegs());
        $this->assertEquals(2, $result->getWhitePegs());

        $guessArray = array('G','O','R','Y');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertTrue($result->isCorrectGuess());
        $this->assertEquals(4, $result->getBlackPegs());
        $this->assertEquals(0, $result->getWhitePegs());


        $secretCode = array('R','O','R','G'); //SECOND CODE TO CRACK

        $guessArray = array('G','O','Y','R');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(1, $result->getBlackPegs());
        $this->assertEquals(2, $result->getWhitePegs());

        $guessArray = array('R','O','Y','R');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(2, $result->getBlackPegs());
        $this->assertEquals(1, $result->getWhitePegs());

        $guessArray = array('R','O','R','Y');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertFalse($result->isCorrectGuess());
        $this->assertEquals(3, $result->getBlackPegs());
        $this->assertEquals(0, $result->getWhitePegs());

        $guessArray = array('R','O','R','G');
        $result = $mastermind->evaluateGuess($guessArray, $secretCode);
        $this->assertInstanceOf(GuessResultDTO::class, $result);
        $this->assertTrue($result->isCorrectGuess());
        $this->assertEquals(4, $result->getBlackPegs());
        $this->assertEquals(0, $result->getWhitePegs());
    }

    /**
     * Creates random mastermind code in array or json format
     */
    public function testCreateRandomMastermindCode() {
        $mastermind = new MastermindService();
        $result = $mastermind->createRandomMastermindCode(); //array
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(MastermindService::NUMBER_OF_PEGS_IN_CODE - 1, $result);
        $this->assertCount(MastermindService::NUMBER_OF_PEGS_IN_CODE, $result);

        $result = $mastermind->createRandomMastermindCode(true); //json
        $this->assertJson($result);
        $resultArray = json_decode($result);
        $this->assertArrayHasKey(0, $resultArray);
        $this->assertArrayHasKey(MastermindService::NUMBER_OF_PEGS_IN_CODE - 1, $resultArray);
        $this->assertCount(MastermindService::NUMBER_OF_PEGS_IN_CODE, $resultArray);
    }

    /**
     * This function allows us to test private functions of classes
     *
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}