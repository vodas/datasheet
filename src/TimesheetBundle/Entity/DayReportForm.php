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

    private $projectId_1;
    
    private $timeSpent_1;

    private $comment_1;

    private $projectId_2;

    private $timeSpent_2;

    private $comment_2;

    private $projectId_3;

    private $timeSpent_3;

    private $comment_3;

    /**
     * @return mixed
     */
    public function getComment1()
    {
        return $this->comment_1;
    }

    /**
     * @param mixed $comment_1
     */
    public function setComment1($comment_1)
    {
        $this->comment_1 = $comment_1;
    }

    /**
     * @return mixed
     */
    public function getComment2()
    {
        return $this->comment_2;
    }

    /**
     * @param mixed $comment_2
     */
    public function setComment2($comment_2)
    {
        $this->comment_2 = $comment_2;
    }

    /**
     * @return mixed
     */
    public function getComment3()
    {
        return $this->comment_3;
    }

    /**
     * @param mixed $comment_3
     */
    public function setComment3($comment_3)
    {
        $this->comment_3 = $comment_3;
    }

    


    /**
     * @return mixed
     */
    public function getProjectId2()
    {
        return $this->projectId_2;
    }

    /**
     * @param mixed $projectId_2
     */
    public function setProjectId2($projectId_2)
    {
        $this->projectId_2 = $projectId_2;
    }

    /**
     * @return mixed
     */
    public function getTimeSpent2()
    {
        return $this->timeSpent_2;
    }

    /**
     * @param mixed $timeSpent_2
     */
    public function setTimeSpent2($timeSpent_2)
    {
        $this->timeSpent_2 = $timeSpent_2;
    }

    /**
     * @return mixed
     */
    public function getProjectId3()
    {
        return $this->projectId_3;
    }

    /**
     * @param mixed $projectId_3
     */
    public function setProjectId3($projectId_3)
    {
        $this->projectId_3 = $projectId_3;
    }

    /**
     * @return mixed
     */
    public function getTimeSpent3()
    {
        return $this->timeSpent_3;
    }

    /**
     * @param mixed $timeSpent_3
     */
    public function setTimeSpent3($timeSpent_3)
    {
        $this->timeSpent_3 = $timeSpent_3;
    }
    
    
    
    

    /**
     * @return mixed
     */
    public function getTimeSpent1()
    {
        return $this->timeSpent_1;
    }

    /**
     * @param mixed $timeSpent
     */
    public function setTimeSpent1($timeSpent_1)
    {
        $this->timeSpent_1 = $timeSpent_1;
    }

    /**
     * @return mixed
     */
    public function getProjectId1()
    {
        return $this->projectId_1;
    }

    /**
     * @param mixed $project_1
     */
    public function setProjectId1($projectId_1)
    {
        $this->projectId_1 = $projectId_1;
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

