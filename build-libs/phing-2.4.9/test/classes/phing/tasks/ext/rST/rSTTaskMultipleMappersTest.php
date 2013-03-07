<?php

/**
 * Unit test for reStructuredText rendering task.
 *
 * PHP version 5
 *
 * @category   Tasks
 * @package    phing.tasks.ext
 * @author     Christian Weiske <cweiske@cweiske.de>
 * @license    LGPL v3 or later http://www.gnu.org/licenses/lgpl.html
 * @link       https://gitorious.org/phing/rsttask
 * @version    SVN: $Id: rSTTaskMultipleMappersTest.php 1277 2011-08-17 17:06:22Z mrook $
 */

require_once 'phing/BuildFileTest.php';

/**
 * Unit test for reStructuredText rendering task.
 *
 * PHP version 5
 *
 * @category   Tasks
 * @package    phing.tasks.ext
 * @author     Christian Weiske <cweiske@cweiske.de>
 * @license    LGPL v3 or later http://www.gnu.org/licenses/lgpl.html
 * @link       https://gitorious.org/phing/rsttask
 */
class rSTTaskMultipleMappersTest extends BuildFileTest 
{
    /**
     * @expectedException BuildException
     * @expectedExceptionMessage Cannot define more than one mapper
     */
    public function testMultipleMappers() 
    {
        $this->configureProject(
            PHING_TEST_BASE . '/etc/tasks/ext/rst/build-error-multiple-mappers.xml'
        );
    }

}