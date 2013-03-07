<?php
/**
 * $Id: SvnLogTask.php 1120 2011-05-30 13:22:37Z mrook $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

require_once 'phing/Task.php';
require_once 'phing/tasks/ext/svn/SvnBaseTask.php';

/**
 * Stores the output of a log command on a workingcopy or repositoryurl in a property.
 * This stems from the SvnLastRevisionTask.
 *
 * @author Anton Stöckl <anton@stoeckl.de>
 * @author Michiel Rook <michiel.rook@gmail.com> (SvnLastRevisionTask)
 * @version $Id: SvnLogTask.php 1120 2011-05-30 13:22:37Z mrook $
 * @package phing.tasks.ext.svn
 * @see VersionControl_SVN
 * @since 2.1.0
 */
class SvnLogTask extends SvnBaseTask
{
    private $propertyName = "svn.log";
    private $forceCompatible = true;
    private $limit = null;

    /**
     * Sets the name of the property to use
     */
    function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * Returns the name of the property to use
     */
    function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Sets whether to force compatibility with older SVN versions (< 1.2)
     */
    public function setForceCompatible($force)
    {
        //$this->forceCompatible = (bool) $force;
        // see below, we need this to be true as xml mode does not work
    }

    /**
     * Sets the max num of log entries to get from svn
     */
    function setLimit($limit)
    {
        $this->limit = (int) $limit;
    }

    /**
     * The main entry point
     *
     * @throws BuildException
     */
    function main()
    {
        $this->setup('log');

        $switches= array();
        if ($this->limit > 0) {
            $switches['limit'] = $this->limit;
        }

        if ($this->forceCompatible) {
            $output = $this->run(array(), $switches);
            $result = null;

            foreach ($output as $line) {
                $result .= (!empty($result)) ? "\n" : '';
                $result .= "{$line['REVISION']} | {$line['AUTHOR']}  | {$line['DATE']}  | {$line['MSG']}";
            }

            if (!empty($result)) {
                $this->project->setProperty($this->getPropertyName(), $result);
            } else {
                throw new BuildException("Failed to parse the output of 'svn log'.");
            }
        } else {
            // this is not possible at the moment as SvnBaseTask always uses fetchmode ASSOC
            // which transfers everything into nasty assoc array instead of xml
        }
    }
}
