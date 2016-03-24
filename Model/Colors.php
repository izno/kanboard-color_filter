<?php

namespace Kanboard\Plugin\Color_filter\Model;

use Kanboard\Helper;

use DateInterval;
use DateTime;
use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Model\Base;
use Kanboard\Model\Task;
use Kanboard\Model\User;

/**
 * Colors
 *
 * @package  model
 * @author   Martin Middeke
 */
class Colors extends Base
{

    /**
     * Get all colornames from system and overwrite with custom board names
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */

#$this->colors->getColorUsage($project['id'], $color_id);

     public function getColors($project_id)
    {
        $colors_assigned = $this->projectMetadata->getAll($project_id);
		    $colors = $this->helper->task->getColors();
		    $returncolors = array();

        foreach ($colors as $color_id => $color_name) {
            if (!$this->colors->getColorUsage($project_id, $color_id)){
                if (array_key_exists ('color_filter_' . $color_id, $colors_assigned)){
                    $returncolors[$color_id] = $colors_assigned['color_filter_' . $color_id];
                }
                else
                    $returncolors[$color_id] = $color_name;
                }
        }
            
        return $returncolors;
    }

    /**
     * Get all assigned colornames for a project
     *
     * @access public
     * @param  integer   $project_id
     * @return array
     */
    public function getAssigned($project_id)
    {
        $colors_assigned = $this->projectMetadata->getAll($project_id);
        $colors = $this->helper->task->getColors();

        foreach ($colors as $color_id => $color_name) {
            if (array_key_exists ('color_filter_' . $color_id, $colors_assigned))
                $color_assigned_clean[$color_id] = $colors_assigned['color_filter_' . $color_id];
            else
                $color_assigned_clean[$color_id] = "";
            if (array_key_exists ('color_filter_' . $color_id . '_projectuse', $colors_assigned))
                if ($colors_assigned['color_filter_' . $color_id . '_projectuse'])
				    $color_assigned_clean['color_filter_' . $color_id . '_projectuse'] = t('No');
                else
				    $color_assigned_clean['color_filter_' . $color_id . '_projectuse'] = t('Yes');
			else
                $color_assigned_clean['color_filter_' . $color_id . '_projectuse'] = t('Yes');
        }
            
        return $color_assigned_clean;
    }

    /**
     * Get colorusage from color_id
     *
     * @access public
     * @param  integer   $color_id
     * @return array
     */
    public function getColorUsage($project_id, $color_id)
    {
        return $this->projectMetadata->get($project_id, 'color_filter_' . $color_id . '_projectuse');
    }

    /**
     * Get colorname from color_id
     *
     * @access public
     * @param  integer   $color_id
     * @return array
     */
    public function getColorName($project_id, $color_id)
    {
        return $this->projectMetadata->get($project_id, 'color_filter_' . $color_id);
    }

    /**
     * Remove a specific colorname
     *
     * @access public
     * @param  integer  $project_id
     * @param  string   $name
     * @return boolean
     */
    public function remove($project_id, $name)
    {
		return $this->projectMetadata->remove($project_id, 'color_filter_' . $name);
    }

    /**
     * Create a custom colorname
     *
     * @access public
     * @param  array    $values    Form values
     * @return boolean
     */
    public function create(array $values)
    {
        $createarray = array();
        if (isset ($values['projectcolorname']))
        {
            $createarray['color_filter_' . $values['color_id']] = $values['projectcolorname'];
        }
        if (isset ($values['projectuse']))
        {
            $createarray['color_filter_' . $values['color_id'] . '_projectuse'] = $values['projectuse'];
        }
        return $this->projectMetadata->save($values['project_id'], $createarray);
    }

    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('projectcolorname', t('Field required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}