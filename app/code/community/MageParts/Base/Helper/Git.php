<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

/**
 * This classes bases it's functions on the library libgit2 (https://libgit2.github.com/).
 *
 * Class MageParts_Base_Helper_Git
 */
class MageParts_Base_Helper_Git extends MageParts_Base_Helper_Data
{

    /**
     * System path of git executable.
     *
     * @var string
     */
    protected $_path = '/usr/bin/git';

    /**
     * Check whether or not a requested path is a git repository.
     *
     * @param string $dir git repository (where .git resides)
     * @return bool
     */
    public function isRepo($dir)
    {
        $result = false;

        if (is_dir($this->getPackageDir($dir))
            && is_dir($dir . '/.git')
            && @git_repository_open($dir)) {
            $result = true;
        }

        return $result;
    }

    public function getPackageDir($dir)
    {
        return is_dir($dir) && is_dir($dir . '/package') ? $dir . '/package' : null;
    }

    /**
     * Fetch branch(es) in git repository.
     *
     * @param string $dir git repository (where .git resides)
     * @param string|array $branches
     * @throws Exception
     */
    public function fetch($dir, $branches='--all')
    {
        $this->_prepareGitCall($dir);

        if (!is_array($branches)) {
            $branches = array((string) $branches);
        }

        if (count($branches)) {
            foreach ($branches as $branch) {
                $process = shell_exec("git  --git-dir=" . $dir . "/.git fetch " . $branch . " 2>&1");

                if (strtolower(substr($process, 0, 8)) !== 'fetching') {
                    throw new Exception("I think something went wrong while I was fetching " . $dir . " :: " . $branch);
                }
            }
        }
    }

    /**
     * Get repository log entries.
     *
     * @param string $dir git repository (where .git resides)
     * @param string|array $branches
     * @param int $from timestamp to limit number of results
     * @param string|array $typeFilter for example "commit" or array("commit", "merge", "checkout")
     * @return array
     * @throws Exception
     */
    public function getLogEntries($dir, $branches='master', $from=0, $typeFilter=null)
    {
        $result = array();

        $this->_prepareGitCall($dir);

        if (is_string($branches)) {
            $branches = array($branches);
        }

        if (is_string($typeFilter)) {
            $typeFilter = array($typeFilter);
        }

        $repo = git_repository_open($dir);

        if (is_array($branches) && count($branches)) {
            foreach ($branches as $branch) {
                $result[$branch] = array();

                // Retrieve log entries from branch
                $entries = git_reflog_read($repo, "refs/heads/" . $branch);

                for ($i=0; $i<git_reflog_entrycount($entries); $i++) {
                    // Retrieve log entry object
                    $entry = git_reflog_entry_byindex($entries, $i);

                    // Retrieve entry information (author info & time)
                    $info = git_reflog_entry_committer($entry);

                    if ($info) {
                        $time = $info['time']->getTimestamp();

                        if ($time < $from) {
                            break;
                        }

                        $message = git_reflog_entry_message($entry);

                        $type = substr($message, 0, strpos($message, ':'));

                        if (is_array($typeFilter) && count($typeFilter) && !in_array($type, $typeFilter)) {
                            continue;
                        }

                        $id = git_reflog_entry_id_new($entry);

                        if (!$id) {
                            continue;
                        }

                        $result[$branch][] = array(
                            'user_name'  => $info['name'],
                            'user_email' => $info['email'],
                            'id'         => $id,
                            'time'       => $time,
                            'message'    => $message
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Preparations before a git action can be executed.
     *
     * This function will...
     *
     * 1) Check that a git executable exists on the server.
     * 2) Ensure that the directory used for git operations is in fact a repository.
     *
     * @param string $dir git repository (where .git resides)
     * @return MageParts_Base_Helper_Git
     * @throws Exception
     */
    protected function _prepareGitCall($dir)
    {
        if (!$this->getPath()) {
            throw new Exception("No git path specified, execution halted.");
        }

        if (!$this->isRepo($dir)) {
            throw new Exception($dir . " is not a git directory!");
        }

        return $this;
    }

    /**
     * Set system path of git executable.
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        if ($path) {
            $this->_path = $path;
        }

        return $this;
    }

    /**
     * Get system path of git executable.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }


    /**
     * Archive git repository.
     *
     * @param string $dir git repository (where .git resides)
     * @param string $destination path where archive will be exported to (example /var/archives/whatever/my.tar.gz)
     * @param string $branch
     * @param string $format example zip or tar
     * @return null|string package path
     * @throws Exception
     */
    public function archive($dir, $destination, $branch='master', $format='tar')
    {
        $result = null;

        $this->_prepareGitCall($dir);

        $process = shell_exec("git  --git-dir=" . $dir . "/.git archive --format=" . $format . " --output=" . $destination . " " . $branch . " 2>&1");

        if (!is_null($process)) {
            throw new Exception((is_string($process) ? $process : "Something went wrong while creating the archive."));
        }

        return $result;
    }

}
