<?php

namespace App\Repositories\ChangeRequest;

use LaravelEasyRepository\Repository;

interface ChangeRequestRepository extends Repository
{

    public function StoreChangeRequest($data);
    public function saveAsDraft($data);
    public function approve($request, $changeRequest);
    public function reviewStore($request, $changeRequest);
    public function regulatoryAssessment($request, $changeRequest);
    public function requestOverdue($request);
    public function approveOverdue($request);
    public function rejectOverdue($request);
    public function submitActionPlan($request);
    public function saveQaRiskAssessment($changeRequest, $request);
    public function closeChangeRequest($changeRequest,$request);
    public function approveActionPlan($actionPlan);
    public function rejectActionPlan($actionPlan);
    public function savePendingActionPlan($request);
}
