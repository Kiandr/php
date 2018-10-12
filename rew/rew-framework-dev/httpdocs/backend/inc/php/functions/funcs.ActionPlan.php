<?php
/*
 * Some global utility functions for the Action Plan module.
 */

/**
 * Draws nested task list for Action Plan builder. (Recursive)
 * @param array $tasks (Associative Array)
 * @param int|null $parent_id
 */
function plan_builder($tasks, $parent_id = null)
{

    foreach ($tasks as $task) {
        $children = !empty($task['children']) ? $task['children'] : false;

        $task = $task['self'];
        $due  = $task['offset'] . ' days after ' . (is_null($parent_id) ? ' plan assigned' : ' previous task completed') . ', at ' . date('g:i A', strtotime($task['time']));

        echo '<li class="node task' . (!empty($children) ? ' has-child' : ' no-child') . '">';

            echo '<div class="article">';
            echo '<span class="ttl"><a href="javascript: void(0);" class="task-action edit-task" data-task="' . $task['id'] . '" data-action="edit" title="Edit Task">' . $task['name'] . '</a></span>';

                    echo '<div class="btns R">';
                        echo '<span class="trig"><a class="btn btn--ghost select-type" data-task="' . $task['id'] . '"><svg class="icon icon-add mar0"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a></span>';
                        echo '<a class="btn btn--ghost task-action delete" data-task="' . $task['id'] . '" data-action="delete"><svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>';
                    echo '</div>';

/*
				echo '<td class="task-type">'
						. '<a class="btn task-action edit-task" data-task="' . $task['id'] . '" data-action="edit" title="Edit Task">'
							. '<i class="' . Backend_Task::getTypeIcon($task['type']) . ' icon-task"></i>'
							. $task['performer']
							. '<div class="automated-flag">' . (($task['automated'] == 'Y') ? 'Automated' : '') . '</div>'
						. '</a>'
					. '</td>';
				echo '<td class="task-description"><span class="task-title">' . $task['name'] . '</span><br>' . $due .'</td>';
				echo '<td class="task-actions">';
					echo '<div class="actions compact" style="display:none;">';
						echo '<a class="btn select-type" data-task="' . $task['id'] . '">Follow-Up</a>';
						echo '<a class="btn task-action" data-task="' . $task['id'] . '" data-action="edit">Edit</a>';
						echo '<a class="btn task-action delete" data-task="' . $task['id'] . '" data-action="delete">Delete</a>';
					echo '</div>';
				echo '</td>';
*/
        if ($children) {
            echo '<td class="task-has-subtask"><a class="toggle-subtasks open" data-task="' . $task['id'] . '"><i class="icon-minus-sign"></i></a></td>';
        }
            echo '</div>'; // .article

        if (!empty($children)) {
            echo '<ul>';
            echo '<li class="task-subtasks" data-task="' . $task['id'] . '">';
                echo '<td class="subtasks-container">';
                plan_builder($children, $task['id']);
                echo '</td>';
            echo '</li></ul>';
        }

        echo '</li>';
    }
}

/**
 * Returns available task type options
 * @return array
 */
function task_type_options()
{
    $type_options = array('Call', 'Email', 'Search', 'Group', 'Listing', 'Custom');
    if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) {
        array_splice( $type_options, 2, 0, 'Text' );
    }
    return $type_options;
}

/**
 * Returns available task performer options
 * @return array
 */
function task_performer_options()
{
    $performer_options = array('Agent');

    // [Morgan Temp Request] Remove performer options
// 	if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
// 		$performer_options[] = 'Lender';
// 	}
// 	if (!empty(Settings::getInstance()->MODULES['REW_ISA_MODULE'])){
// 		$performer_options[] = 'Associate';
// 	}

    return $performer_options;
}

/**
 * Convert timestamp to a duration to/from now. ex. "5 days, 16 hours from now" or "3 hours, 12 minutes ago"
 * @param string $timestamp
 * @returns string $relative
 */
function date_to_relative($time)
{

    $relative = $time - time();
    $past     = ($relative <= 0) ? true : false; // append "ago"/"from now" for past/future time
    $time_components = array();

    $relative = abs($relative);
    $days     = round($relative / 86400);
    if ($days > 0) {
        $time_components[] = $days . ' day' . ($days == 1 ? '' : 's');
    }

    // Display hours if less than 5 days
    if ($days < 5) {
        $relative -= ($days * 86400);
        $hours     = round($relative / 3600);
        if ($hours > 0) {
            $time_components[] = $hours . ' hour' . ($hours == 1 ? '' : 's');
        }
    }

    // Display minutes if less than 5 hours
    if ($days == 0 && $hours < 5) {
        $relative -= ($hours * 3600);
        $minutes   = round($relative / 60);
        if ($minutes > 0) {
            $time_components[] = $minutes . ' minute' . ($minutes == 1 ? '' : 's');
        }
    }

    if (!empty($time_components)) {
        $relative = implode(', ', $time_components) . ($past ? ' ago' : ' from now');
    } else {
        $relative = 'now';
    }

    return $relative;
}
