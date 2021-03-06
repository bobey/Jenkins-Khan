<?php

class statusAction extends baseApiJenkinsAction
{

  /**
   *
   * @param sfRequest $request
   *
   * @return string
   */
  public function execute($request)
  {
    $branchName = $request->getParameter('git_branch_slug');
    $userId     = $this->getUser()->getUserId();
    $jenkins    = $this->getJenkins();

    if ($jenkins->isAvailable())
    {
      JenkinsRunPeer::fillEmptyJobBuildNumber($jenkins, $userId);
    }

    $groupRun = JenkinsGroupRunPeer::retrieveBySfGuardUserIdAndGitBranchSlug($userId, $branchName);

    $status = null;
    if (null !== $groupRun)
    {
      $status = $groupRun->getResult($jenkins);
    }

    $return = array(
      'status'=> $status,
    );
    return $this->renderText(json_encode($return));
  }

}
