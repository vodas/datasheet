<?php
namespace TimesheetBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * DayReport
 */
class DayReportForm
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \Time
     */
    private $start;

    /**
     * @var \Time
     */
    private $end;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var bool
     */
    private $canEdit;

    /**
     * @var \Date
     */
    private $date;

    private $project_1;

    /**
     * @return mixed
     */
    public function getProject1()
    {
        return $this->project_1;
    }

    /**
     * @param mixed $project_1
     */
    public function setProject1($project_1)
    {
        $this->project_1 = $project_1;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return DayReport
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return DayReport
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return DayReport
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return DayReport
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set canEdit
     *
     * @param boolean $canEdit
     *
     * @return DayReport
     */
    public function setCanEdit($canEdit)
    {
        $this->canEdit = $canEdit;

        return $this;
    }

    /**
     * Get canEdit
     *
     * @return bool
     */
    public function getCanEdit()
    {
        return $this->canEdit;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return DayReport
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}

