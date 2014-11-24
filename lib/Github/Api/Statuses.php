<?php
/**
 * User: pbrohman
 * Date: 14-11-24
 * Time: 9:54 AM
 */

namespace Github\Api;

/**
 * Create statuses, list statuses for a ref, and get combined statuses for a ref
 *
 * @link   https://developer.github.com/v3/repos/statuses/
 * @author Paul Brohman <pbrohman@gmail.com>
 */
class Statuses extends AbstractApi
{
    /**
     * Get all statuses about a a commit by its ref
     *
     * @link https://developer.github.com/v3/repos/statuses/#list-statuses-for-a-specific-ref
     *
     * @param string $username the organization to show
     * @param string $repository the repository to use
     * @param string $ref Ref to list the statuses from
     *
     * @return array informations about the organization
     */
    public function all($username, $repository, $ref, array $params = array())
    {
        return $this->get('repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($ref).'/statuses', array_merge(array('page' => 1), $params));
    }

    /**
     * Get combined status information about a commit by its ref
     *
     * @link https://developer.github.com/v3/repos/statuses/#get-the-combined-status-for-a-specific-ref
     *
     * @param string $username the organization to show
     * @param string $repository the repository to use
     * @param string $ref Ref to list the statuses from
     *
     * @return array informations about the organization
     */
    public function show($username, $repository, $ref)
    {
        return $this->get('repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($ref).'/status');
    }
}
