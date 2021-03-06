<?php



/**
 * Skeleton subclass for representing a row from the 'jenkins_group_run' table.
 *
 *
 *
 * This class was autogenerated by Propel 1.6.3 on:
 *
 * Fri Jan 20 17:32:29 2012
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class JenkinsGroupRun extends BaseJenkinsGroupRun
{

  /**
   * @param Jenkins $jenkins
   * @return null
   */
  public function getResult(Jenkins $jenkins)
  {
    $lastWeight = -1;
    $result     = null;

    foreach ($this->getJenkinsRuns() as $jenkinsRun)
    {
      /** @var JenkinsRun $jenkinsRun */
      $runResult = $jenkinsRun->getJenkinsResult($jenkins);
      if (($weight = $this->getResultWeight($runResult)) > $lastWeight)
      {
        $result     = $runResult;
        $lastWeight = $weight;
      }
    }

    return $result;
  }

  /**
   * @param $result
   * @return int
   */
  private function getResultWeight($result)
  {
    //from lower to higher weight
    $weigths = array(
      JenkinsRun::SUCCESS,
      JenkinsRun::ABORTED,
      JenkinsRun::SCHEDULED,
      JenkinsRun::DELAYED,
      JenkinsRun::UNSTABLE,
      JenkinsRun::WAITING,
      JenkinsRun::RUNNING,
      JenkinsRun::FAILURE,
      JenkinsRun::UNREACHABLE,
    );
    
    $weight = 0;
    if (false !== ($position = array_search($result, $weigths)))
    {
      $weight = pow($position, 2);
    }
    return $weight;
  }

  /**
   * @param \Jenkins $jenkins
   * @param array    $default
   *
   * @return array
   */
  public function buildDefaultFormValue(Jenkins $jenkins, $default = array())
  {
    $default['git_branch'] = $this->getGitBranch();
    $default['label']      = $this->getLabel();
    foreach ($this->getJenkinsRuns() as $run)
    {
      /** @var JenkinsRun $run  */
      $default['builds'][$run->getJobName()] = array(
        'job_name'   => true,
        'parameters' => $run->getJenkinsBuildCleanedParameter($jenkins)
      );
    }

    return $default;
  }

  /**
   * @param Jenkins $jenkins
   * @param bool    $delayed
   *
   * @return $this
   */
  public function rebuild(Jenkins $jenkins, $delayed = false)
  {
    /** @var JenkinsRun $run */
    foreach ($this->getJenkinsRuns() as $run)
    {
      if (!$run->isRebuildable())
      {
        continue;
      }
      $run->rebuild($jenkins, $delayed);
    }
    return $this;
  }

  /**
   * Get the slug, ensuring its uniqueness
   *
   * @param string $slug the slug to check
   * @param string $separator the separator used by slug
   * @param int $increment
   *
   * @return string the unique slug
   */
  protected function makeSlugUnique($slug, $separator = '-', $increment = 0)
  {
    $slug2 = empty($increment) ? $slug : $slug . $separator . $increment;
    $slugAlreadyExists = JenkinsGroupRunQuery::create()
      ->filterBySlug($slug2)
      ->filterBySfGuardUserId($this->getSfGuardUserId())
      ->prune($this)
      ->count();
    if ($slugAlreadyExists)
    {
      return $this->makeSlugUnique($slug, $separator, ++$increment);
    }
    else
    {
      return $slug2;
    }
  }

  
} // JenkinsGroupRun
