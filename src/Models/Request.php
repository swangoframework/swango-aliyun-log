<?php
namespace Swango\Aliyun\Log\Models;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * The base request of all log request.
 *
 * @author log service dev
 */
class Request {

    /**
     *
     * @var string project name
     */
    private $project;

    /**
     * Aliyun_Log_Models_Request constructor
     *
     * @param string $project
     *            project name
     */
    public function __construct(string $project) {
        $this->project = $project;
    }

    /**
     * Get project name
     *
     * @return string project name
     */
    public function getProject(): string {
        return $this->project;
    }

    /**
     * Set project name
     *
     * @param string $project
     *            project name
     */
    public function setProject(string $project): void {
        $this->project = $project;
    }
}
