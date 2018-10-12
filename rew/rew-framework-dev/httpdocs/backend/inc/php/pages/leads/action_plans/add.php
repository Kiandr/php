<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Export All Leads
if (!$leadsAuth->canManageActionPlans($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage action plans.')
    );
}

// Success
$success = array();

// Error
$errors = array();

// Label Colours Dependency
$di = Container::getInstance();
$labelColours = $di->get(REW\Backend\Store\LabelColourStore::class);
$actionPlanLabels = $labelColours->getLabelColours();

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'name',    'title' => __('Name'));
    $required[] = array('value' => 'style',   'title' => __('Label Color'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check for days
    if (empty($_POST['day_adjust'])) {
        $errors[] = __('At least one scheduled day is required to create an action plan.');
    }

    // Error Checking
    if (empty($errors)) {
        $day_adjust = implode(',', $_POST['day_adjust']);

        $action_plan = new Backend_ActionPlan(array(
            'name'        => $_POST['name'],
            'description' => $_POST['description'],
            'style'       => $_POST['style'],
            'day_adjust'  => $day_adjust,
        ));

        // Save the action plan
        if ($action_plan->save()) {
            // Save success notice for display on next page
            $success[] = __('Action plan has successfully been created.');
            $authuser->setNotices($success, $errors);

            // Redirect to edit form
            header('Location: ../edit/?id=' . $action_plan->getId());
            exit;
        }
    }
}

// Default values for Task Due Adjustment
$_POST['day_adjust'] = empty($_POST['day_adjust']) ? array(1,2,3,4,5) : $_POST['day_adjust'];
