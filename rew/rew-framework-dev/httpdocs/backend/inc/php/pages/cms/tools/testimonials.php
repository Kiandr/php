<?php

// Create Auth Classes
$toolsAuth = new REW\Backend\Auth\ToolsAuth(Settings::getInstance());
if (!$toolsAuth->canManageTestimonials($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage testimonials.')
    );
}

// DB connection
$db = DB::get();

// Notices
$success = array();
$errors = array();

// TDisplay forms
$add_form = false;
$edit_form = false;

// Can assign agent to testimonial
$can_assign_agent = Skin::hasFeature(Skin::TESTIMONIAL_ASSIGN_AGENT);
$can_include_link = Skin::hasFeature(Skin::INCLUDE_TESTIMONIAL_LINK);

/**
 * Delete existing testimonial
 */
if (!empty($_GET['delete'])) {
    try {
        // Require selected testimonial
        $query = $db->prepare("SELECT * FROM `testimonials` WHERE `id` = :id LIMIT 1;");
        $query->execute(array('id' => $_GET['delete']));
        if (!$testimonial = $query->fetch()) {
            throw new Exception(__('Not found'));
        }
        try {
            // Delete selected testimonial
            $delete = $db->prepare("DELETE FROM `testimonials` WHERE `id` = :id;");
            $delete->execute(array('id' => $testimonial['id']));
            $success[] = __('The selected testimonial has successfully been deleted.');

        // Database error
        } catch (PDOException $e) {
            $errors[] = __('The selected testimonial could not be deleted.');
            //$errors[] = $e->getMessage();
        }

    // Error occurred
    } catch (Exception $e) {
        $errors[] = __('The selected testimonial could not be found. Please try again.');
        //$errors[] = $e->getMessage();
    }
}

/**
 * Update existing testimonial
 */
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];
if (!empty($_GET['edit'])) {
    try {
        // Require selected testimonial
        $query = $db->prepare("SELECT * FROM `testimonials` WHERE `id` = :id LIMIT 1;");
        $query->execute(array('id' => $_GET['edit']));
        if (!$testimonial = $query->fetch()) {
            throw new Exception(__('Not found'));
        }
        $edit_form = true;

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $_POST['link'] = strtolower($_POST['link']);
                // Updated testimonial
                $testimonial = array_merge($testimonial, array(
                    'client' => $_POST['client'],
                    'agent_id' => $_POST['agent_id'],
                    'testimonial' => $_POST['testimonial'],
                    'link' => $_POST['link']
                ));

                // Required fields
                $required   = array();
                $required[] = array('value' => 'testimonial', 'title' => __('Testimonial'));
                foreach ($required as $require) {
                    $value = Format::trim($testimonial[$require['value']]);
                    if (empty($value)) {
                        throw new UnexpectedValueException (
                            __('%s is a required field.', $require['title'])
                        );
                    }
                }

                // Assigned agent
                $agent_id = null;
                if ($can_assign_agent) {
                    $agent_id = $testimonial['agent_id'] ?: null;
                }

                // Update database record
                $db->prepare("UPDATE `testimonials` SET "
                    . "`client` = :client, "
                    . "`agent_id` = :agent_id, "
                    . ($can_include_link ? "`link` = :link, " : '')
                    . "`testimonial` = :testimonial, "
                    . "`timestamp_updated` = NOW()"
                    . " WHERE `id` = :id"
                . ";")->execute(($can_include_link ? array(
                        'link'      => $testimonial['link'],
                ) : array()) + array(
                    'agent_id'      => $agent_id,
                    'testimonial'   => $testimonial['testimonial'],
                    'client'        => $testimonial['client'],
                    'id'            => $testimonial['id']
                ));

                // Save notices and redirect
                $success[] = __('Testimonial has successfully been saved.');
                $authuser->setNotices($success, $errors);
                header('Location: ?success');
                exit;

            // Database error
            } catch (PDOException $e) {
                $errors[] = __('The selected testimonial could not be saved.');
                //$errors[] = $e->getMessage();

            // Validation error
            } catch (UnexpectedValueException $e) {
                $errors[] = $e->getMessage();
            }
        }

    // Error occurred
    } catch (Exception $e) {
        $errors[] = __('The selected testimonial could not be found. Please try again.');
        //$errors[] = $e->getMessage();
    }
}

/**
 * Add new testimonial
 */
$_GET['add'] = isset($_POST['add']) ? $_POST['add'] : $_GET['add'];
if (isset($_GET['add'])) {
    $add_form = true;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $_POST['link'] = strtolower($_POST['link']);

            // Required fields
            $required   = array();
            $required[] = array('value' => 'testimonial', 'title' => __('Testimonial'));
            foreach ($required as $require) {
                $value = Format::trim($_POST[$require['value']]);
                if (empty($value)) {
                    throw new UnexpectedValueException(
                        __('%s is a required field.', $require['title'])
                    );
                }
            }

            // Assigned agent
            $agent_id = null;
            if ($can_assign_agent) {
                $agent_id = $_POST['agent_id'] ?: null;
            }

            // Update database record
            $db->prepare("INSERT INTO `testimonials` SET "
                . "`client` = :client, "
                . "`agent_id` = :agent_id, "
                . ($can_include_link ? "`link` = :link, " : '')
                . "`testimonial` = :testimonial, "
                . "`timestamp_created` = NOW()"
            . ";")->execute(($can_include_link ? array(
                    'link'      => $_POST['link'],
                ) : array()) + array(
                'agent_id'      => $agent_id,
                'testimonial'   => $_POST['testimonial'],
                'client'        => $_POST['client']
            ));

            // Save notices and redirect
            $success[] = __('Testimonial has successfully been created.');
            $authuser->setNotices($success, $errors);
            header('Location: ?success');
            exit;

        // Database error
        } catch (PDOException $e) {
            $errors[] = __('Your testimonial could not be saved.');
            //$errors[] = $e->getMessage();

        // Validation error
        } catch (UnexpectedValueException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Pagination
// Cursor details
$beforeCursor = $_GET['before'];
$afterCursor = $_GET['after'];
$primaryKey = 'id';
$searchLimit = 10;
$orderBy = 'timestamp_created';
$sortDir = 'ASC';

// Next
if (!empty($afterCursor)) {
    $cursor = REW\Pagination\Cursor\After::decode($afterCursor);

    // Prev
} else if (!empty($beforeCursor)) {
    $cursor = REW\Pagination\Cursor\Before::decode($beforeCursor);

    // First
} else {
    $cursor = new REW\Pagination\Cursor($primaryKey, $searchLimit, $orderBy, $sortDir);

}

// Create pagination instance
$pagination = new REW\Pagination\Pagination($cursor);

$limit = $pagination->getLimit();
$limitQuery = $limit ? " LIMIT " . $limit : "";
$order = $pagination->getOrder();
$orderQuery = "";
foreach ($order as $sort => $dir) {
    $sortString = "`" . $sort . "` ";
    $orderQuery .= $sortString . $dir . ", ";
};
$orderQuery = rtrim(" ORDER BY " . $orderQuery, ", ");
$paginationWhere = $pagination->getWhere();
$paramsPagination = $pagination->getParams();
if (!empty($paginationWhere)) {
    $paginationWhere = " WHERE " . $paginationWhere;
}

try {
    // Load testimonials
    $testimonials = $db->fetchAll("SELECT * FROM `testimonials`" . $paginationWhere . $orderQuery . $limitQuery . ";", $paramsPagination);

// Database error
} catch (PDOException $e) {
    $errors[] = __('Error occurred while loading available testimonials.');
    //$errors[] = $e->getMessage();
}

$pagination->processResults($testimonials);

for ($i = 0; $i < count($testimonials); $i++) {
    // Delete Link
    $deleteLink = sprintf('delete=%s%s', $testimonials[$i]['id'], $appendFilter);
    if (!empty($_GET['after'])) {
        $testimonials[$i]['deleteLink'] = sprintf('?after=%s&%s', $_GET['after'], $deleteLink);
    } else if (!empty($_GET['before'])) {
        $testimonials[$i]['deleteLink'] = sprintf('?before=%s&%s', $_GET['before'], $deleteLink);
    } else {
        $testimonials[$i]['deleteLink'] = sprintf('?%s', $deleteLink);
    }
}

// Pagination link URLs
$nextLink = $pagination->getNextLink();
$prevLink = $pagination->getPrevLink();
$paginationLinks = ['nextLink' => $nextLink, 'prevLink' => $prevLink];

// Assign to agent
if ($can_assign_agent) {
    try {
        // Load available agents
        $agents = array();
        $query = $db->query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` ORDER BY `name`;");
        foreach ($query->fetchAll() as $agent) {
            $agents[$agent['id']] = $agent;
        }

        // Assigned testimonials
        $testimonials = array_map(function ($testimonial) use ($agents) {
            $agent_id = $testimonial['agent_id'];
            if (!empty($agent_id) && ($agent = $agents[$agent_id])) {
                $testimonial['agent_name'] = $agent['name'];
            }
            return $testimonial;
        }, $testimonials);

    // Database error
    } catch (PDOException $e) {
        $errors[] = __('Error occurred while loading available agents.');
        //$errors[] = $e->getMessage();
    }
}
