<?php

namespace TimesheetBundle\Entity;

/**
 * ProjectReport
 */
class ProjectReport
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $timeSpent;

    /**
     * @var int
     */
    private $projectId;

    /**
     * @var int
     */
    private $dayReportId;


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
     * Set timeSpent
     *
     * @param \DateTime $timeSpent
     *
     * @return ProjectReport
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    /**
     * Get timeSpent
     *
     * @return \DateTime
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * Set projectId
     *
     * @param integer $projectId
     *
     * @return ProjectReport
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set dayReportId
     *
     * @param integer $dayReportId
     *
     * @return ProjectReport
     */
    public function setDayReportId($dayReportId)
    {
        $this->dayReportId = $dayReportId;

        return $this;
    }

    /**
     * Get dayReportId
     *
     * @return int
     */
    public function getDayReportId()
    {
        return $this->dayReportId;
    }
}

