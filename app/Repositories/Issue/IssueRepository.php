<?php

namespace App\Repositories\Issue;

use LaravelEasyRepository\Repository;

interface IssueRepository extends Repository
{

  public function issueStore($data);
  public function resolutionStore($data, $issue);
  public function approveResolution($issue);
  public function rejectResolution($data, $issue);
}
