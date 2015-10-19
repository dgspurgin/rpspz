<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PlayedGame
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Id
     */
    protected $playedGameID;


    /**
     * @ORM\Column(type="integer")
     */
    protected $p1ID;


    /**
     * @ORM\Column(type="integer")
     */
	protected $p2ID;


    /**
     * @ORM\Column(type="integer")
     */
	protected $p1Choice;


    /**
     * @ORM\Column(type="integer")
     */
	protected $p2choice;


    /**
     * @ORM\Column(type="string", length=10)
     */
	protected $outcome;

    /**
     * Set playedGameID
     *
     * @param integer $playedGameID
     *
     * @return PlayedGame
     */
    public function setPlayedGameID($playedGameID)
    {
        $this->playedGameID = $playedGameID;

        return $this;
    }

    /**
     * Get playedGameID
     *
     * @return integer
     */
    public function getPlayedGameID()
    {
        return $this->playedGameID;
    }

    /**
     * Set p1ID
     *
     * @param integer $p1ID
     *
     * @return PlayedGame
     */
    public function setP1ID($p1ID)
    {
        $this->p1ID = $p1ID;

        return $this;
    }

    /**
     * Get p1ID
     *
     * @return integer
     */
    public function getP1ID()
    {
        return $this->p1ID;
    }

    /**
     * Set p2ID
     *
     * @param integer $p2ID
     *
     * @return PlayedGame
     */
    public function setP2ID($p2ID)
    {
        $this->p2ID = $p2ID;

        return $this;
    }

    /**
     * Get p2ID
     *
     * @return integer
     */
    public function getP2ID()
    {
        return $this->p2ID;
    }

    /**
     * Set p1Choice
     *
     * @param integer $p1Choice
     *
     * @return PlayedGame
     */
    public function setP1Choice($p1Choice)
    {
        $this->p1Choice = $p1Choice;

        return $this;
    }

    /**
     * Get p1Choice
     *
     * @return integer
     */
    public function getP1Choice()
    {
        return $this->p1Choice;
    }

    /**
     * Set p2choice
     *
     * @param integer $p2choice
     *
     * @return PlayedGame
     */
    public function setP2choice($p2choice)
    {
        $this->p2choice = $p2choice;

        return $this;
    }

    /**
     * Get p2choice
     *
     * @return integer
     */
    public function getP2choice()
    {
        return $this->p2choice;
    }

    /**
     * Set outcome
     *
     * @param string $outcome
     *
     * @return PlayedGame
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get outcome
     *
     * @return string
     */
    public function getOutcome()
    {
        return $this->outcome;
    }
}
