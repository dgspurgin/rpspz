<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

 /**
  * @ORM\Entity(repositoryClass="AppBundle\Repository\ChoiceRepository")
 */
class Choice
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Id
     */
    protected $choiceID;


    /**
     * @ORM\Column(type="string", length=100)
     */
	protected $choiceName;

    /**
     * Set choiceID
     *
     * @param integer $choiceID
     *
     * @return Choice
     */
    public function setChoiceID($choiceID)
    {
        $this->choiceID = $choiceID;

        return $this;
    }

    /**
     * Get choiceID
     *
     * @return integer
     */
    public function getChoiceID()
    {
        return $this->choiceID;
    }

    /**
     * Set choiceName
     *
     * @param string $choiceName
     *
     * @return Choice
     */
    public function setChoiceName($choiceName)
    {
        $this->choiceName = $choiceName;

        return $this;
    }

    /**
     * Get choiceName
     *
     * @return string
     */
    public function getChoiceName()
    {
        return $this->choiceName;
    }
}
