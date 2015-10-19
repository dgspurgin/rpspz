<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Choice;
use AppBundle\Entity\PlayedGame;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DBTestController extends Controller
{

    /**
     * @Route("/DBTest/playGame/{p1Choice}", name="dbtest")
     */
    public function storeGamePlayedAction($p1Choice=1)
    {

		$em = $this->getDoctrine()->getManager();


		# If Player table is empty -> seed initial values
		$record = $this->getDoctrine()->getRepository("AppBundle:Player")
			->find(1);
		if (!$record) {
			$player = new Player();
			$player->setPlayerName("Human");
			$em->persist($player);

			$player = new Player();
			$player->setPlayerName("Computer");
			$em->persist($player);
		}


		# If Choice table is empty -> seed initial values
		$record = $this->getDoctrine()->getRepository('AppBundle:Choice')
			->find(1);
		if (!$record) {
			$choice = new Choice();
			$choice->setChoiceName("Scissors");
			$em->persist($choice);

			$choice = new Choice();
			$choice->setChoiceName("Paper");
			$em->persist($choice);

			$choice = new Choice();
			$choice->setChoiceName("Rock");
			$em->persist($choice);

			$choice = new Choice();
			$choice->setChoiceName("Plants");
			$em->persist($choice);

			$choice = new Choice();
			$choice->setChoiceName("Zombies");
			$em->persist($choice);
		}



		# Save a played game
		$p1ID = 1; 		# Human
		$p2ID = 2; 		# Computer
		$p2Choice = 2;
		$winningPlayerID = "";

		$slot1Away = "n/a";
		$slot3Away = "n/a";

		if ($p1Choice == $p2Choice) {
			$winningPlayerID = 0;
		}
		else {
			$slot1Away = ($p1Choice % 5) + 1;
			$slot3Away = (($p1Choice + 2) % 5) + 1;
			if ($p2Choice == $slot1Away  || $p2Choice == $slot3Away) {
				$winningPlayerID = $p1ID;
			}
			else {
				$winningPlayerID = $p2ID;
			}
		}

		$playedGame = new PlayedGame();
		$playedGame->setP1ID($p1ID);
		$playedGame->setP2ID($p2ID);
		$playedGame->setP1Choice($p1Choice);
		$playedGame->setP2Choice($p2Choice);
		$playedGame->setWinningPlayerID($winningPlayerID);
		$em->persist($playedGame);

		# Save everything to db
		$em->flush();

		$responseString = <<<EOD
Game # = {$playedGame->getPlayedGameID()} <br>
P1 Choice = {$playedGame->getP1Choice()} <br>
slot1 = {$slot1Away}<br>
slot3 = {$slot3Away}<br>
<br>
P2 Choice = {$playedGame->getP2Choice()} <br>
Winner = {$playedGame->getWinningPlayerID()} <br><br>
EOD;

		return new Response($responseString);

    }
}