<?php
/**
 * RestController.php
 *
 * Rest API Controller
 *
 * @category   Controller
 * @package    MastermindAPI
 * @author     Alex Perez
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */

namespace MastermindAPI\Controller;

use Doctrine\ORM\Mapping\Builder\ManyToManyAssociationBuilder;
use MastermindAPI\Entity\Board;
use MastermindAPI\Entity\Guess;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use MastermindAPI\Exception\ValidationException;
use MastermindAPI\MastermindEngine;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;



/**
 * Class RestController
 *
 * @Route("/")
 */
class RestController extends FOSRestController
{

    /**
     * @Rest\Post("/v1/board.{_format}", name="board_create", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Mastermind Board was created successfully"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="A validation error occurred trying to create Mastermind board"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error occurred trying to create Mastermind board"
     * )
     *
     * @SWG\Parameter(
     *     name="code",
     *     in="body",
     *     type="json",
     *     description="The Mastermind code. Provide in json format an array of 4 values to choose between these colours:
     *          R => red, O => orange, Y => yellow, G => green, B => blue, V => violet",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Board")
     */
    public function createBoardAction(Request $request) {

        $serializer = $this->getSerializerObject();
        $em = $this->getDoctrine()->getManager();
        $mastermind = new MastermindEngine();
        $board = [];
        $message = "";

        try {
            $responseCode = 201;
            $error = false;
            $codeJson = $request->request->get("code", null);

            if (empty($codeJson)) {
                $codeJson = $mastermind->createRandomMastermindCode(true);
            }

            if ($mastermind->validateCode($codeJson)) {
                $board = new Board();
                $board->setCode($codeJson);
                $em->persist($board);
                $em->flush();
            } else {
                $responseCode = 500;
                $error = true;
                $message = "An error has occurred trying to create new Mastermind board";
            }


        } catch (ValidationException $ex) {
            $responseCode = 400;
            $error = true;
            $message = "An error has occurred trying to create new Mastermind board - {$ex->getMessage()}";

        } catch (Exception $ex) {
            $responseCode = 500;
            $error = true;
            $message = "An error has occurred trying to create new Mastermind board - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $responseCode,
            'error' => $error,
            'data' => $responseCode == 201 ? $board : $message,
        ];
        return new Response($serializer->serialize($response, "json"));

    }


    /**
     * @Rest\Get("/v1/board/{id}.{_format}", name="board_list", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets board info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The board with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The board ID"
     * )
     *
     *
     * @SWG\Tag(name="Board")
     */
    public function getBoardAction(Request $request, $id) {
        $serializer = $this->getSerializerObject();
        $em = $this->getDoctrine()->getManager();

        $board = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $boardId = $id;

            $board = $em->find('MastermindAPI\Entity\Board', $boardId);
            //$board = $em->getRepository("MastermindAPI:Board")->find($boardId);

            if (is_null($board)) {
                $code = 500;
                $error = true;
                $message = "The board does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Board - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $board : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }



    /**
     * @Rest\Post("/v1/guess.{_format}", name="guess_add", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Guess was played successfully"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="A validation error occurred trying to place the guess"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error occurred trying to place the guess"
     * )
     *
     * @SWG\Parameter(
     *     name="pegs",
     *     in="body",
     *     type="json",
     *     description="The guess' pegs",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="board_id",
     *     in="body",
     *     type="string",
     *     description="The mastermind board ID against which you want to place the guess",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Guess")
     */
    public function addGuessAction(Request $request) {

        $serializer = $this->getSerializerObject();
        $em = $this->getDoctrine()->getManager();
        $mastermind = new MastermindEngine();

        $guessResult = [];
        $message = "";

        try {
            $responseCode = 201;
            $error = false;
            $pegsJson = $request->request->get("pegs", null);

            if ($mastermind->validateCode($pegsJson)) {

                $boardId = $request->request->get("board_id", null);
                if (!empty($boardId)){
                    /** @var Board $board */
                    $board = $em->find('MastermindAPI\Entity\Board', $boardId);
                    //$board = $em->getRepository("MastermindAPI:Board")->find($boardId);

                    $guess = new Guess();
                    $guess->setBoard($board);
                    $guess->setPegs($pegsJson);
                    $em->persist($guess);
                    $em->flush();

                    $guessResult = $mastermind->evaluateGuess(
                        json_decode($board->getCode()),
                        json_decode($pegsJson)
                    );


                }



            } else {
                $responseCode = 500;
                $error = true;
                $message = "An error has occurred trying to place a guess - Board ID was empty";
            }

        } catch (ValidationException $ex) {
            $responseCode = 400;
            $error = true;
            $message = "An error has occurred trying to place a guess - {$ex->getMessage()}";

        } catch (Exception $ex) {
            $responseCode = 500;
            $error = true;
            $message = "An error has occurred trying to place a guess - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $responseCode,
            'error' => $error,
            'data' => $responseCode == 201 ? $guessResult : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }


    /**
     * @return object
     */
    private function getSerializerObject()
    {
        $serializer = $this->get('jms_serializer');
        return $serializer;
    }


}
