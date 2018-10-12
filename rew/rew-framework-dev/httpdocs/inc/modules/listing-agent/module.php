<?php

/**
 * Agent CTA
 * This checks the 'bcse.agent' setting which stores an array containing following keys:
 *  'listing' (array)
 *    - Listing data
 *  'agent' (mixed)
 *    - If "RAND", random agent will be selected and displayed (This is the default behavior)
 *    - If FALSE, do not display this CTA
 *    - If (int), select agent by id
 *  'phone' (bool)
 *    - If FALSE, do not display office phone number
 *    - If TRUE, display agent's office phone number (This is default)
 *  'cell' (bool)
 *    - If FALSE, do not display office phone number
 *    - If TRUE, display agent's office phone number (This is default)
 */

// Require MLS Listing
$listing = $this->config('listing');
if (empty($listing)) {
    return;
}

// Display Agent
$agent_id = $this->config['agent'] ?: 'RAND';

// Display Agent's Office #
$display_phone = $this->config['phone'] ?: false;

// Display Agent's Cell #
$display_cell = $this->config['cell'] ?: false;

// Thumbnail Size
$thumbnails = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : '125x155';

// Listing Agent
$agent = false;

// Team Subdomain Agents
$team_subdomain = false;
$no_listing_agent = false;

try {
    // DB Connection
    $db = DB::get();

    // Agent columns
    $agent_cols = "`id`, CONCAT(`first_name`, ' ', `last_name`) AS `name`, `office_phone`, `cell_phone`, `title`, `image`";

    // Find Agent by ID
    $query = $db->prepare("SELECT " . $agent_cols . " FROM `agents` WHERE `id` = :id AND `display_feature` = 'Y' LIMIT 1;");

    // Already assigned to agent
    $user = User_Session::get();
    if ($user->isValid()) {
        $assigned = $user->info('agent');
        if (!empty($assigned)) {
            $query->execute(array('id' => (int) $assigned));
            $agent = $query->fetch();
        }
    }

    // Find Listing's Agent
    if (empty($agent)) {
        if ($listing['idx'] != 'cms') {
            $result = $db->query("SELECT `id`, `agent_id` FROM `agents` WHERE `agent_id` != '' AND `display_feature` = 'Y';");
            while ($office_agent = $result->fetch()) {
                $agent_ids = json_decode($office_agent['agent_id'], true);
                foreach ($agent_ids as $feed => $aid) {
                    $isFeed = $feed == $listing['idx'];
                    $isAgentId = strtolower($aid) == strtolower($listing['ListingAgentID']);
                    if ($isFeed && (!empty($aid) && $isAgentId )) {
                        $query->execute(['id' => $office_agent['id']]);
                        $agent = $query->fetch();
                    }
                }
            }
        } else {
            // Get Listing Agent Id
            $cms_query = $db->prepare("SELECT `agent` FROM `_listings` WHERE `id` = :id;");
            $cms_query->execute(['id' => $listing['ListingMLS']]);
            $agent_id = $cms_query->fetchColumn();

            // Get Listing Agent
            $query->execute(array('id' => $agent_id));
            $agent = $query->fetch();
        }
    }

    // Agent-subdomain/Team-subdomain - agent over-ride
    if (Settings::getInstance()->SETTINGS['agent'] != 1) {
        if (Settings::getInstance()->SETTINGS['team'] && !empty(Settings::getInstance()->MODULES['REW_TEAMS'])) {
            // Team subdomain
            $team = Backend_Team::load(Settings::getInstance()->SETTINGS['team']);
            $team_agent_ids = $team->getAgents([Backend_Team::PERM_FEATURE_LISTINGS]);
            if (!empty($team_agent_ids)) {
                foreach ($team_agent_ids as $team_agent_id) {
                    // Get Agent Details
                    $query->execute(['id' => $team_agent_id]);
                    $team_subdomain_agent = $query->fetch();
                    if (!empty($team_subdomain_agent)) {
                        // Agent Photo
                        $team_subdomain_agent['image'] = (!empty($thumbnails) ? '/thumbs/' . $thumbnails . '/f' : '') . '/' . (!empty($team_subdomain_agent['image']) ? 'uploads/agents/' . $team_subdomain_agent['image'] : 'img/404.gif');

                        // Team Title
                        $team_subdomain_agent['team']['title'] = $team->info('name');

                        // Do not display phone #
                        if (empty($display_phone)) {
                            unset($team_subdomain_agent['office_phone']);
                        }
                        if (empty($display_cell)) {
                            unset($team_subdomain_agent['cell_phone']);
                        }
                        $team_subdomain['agents'][] = $team_subdomain_agent;
                    }
                }
                if (!empty($team_subdomain['agents'])) {
                    $team_subdomain['id'] = $team->getId();
                    $team_subdomain['title'] = $team->info('name');
                }
            } else {
                $no_listing_agent = true;
            }
        } else {
            // Agent subdomain
            $agent_id = Settings::getInstance()->SETTINGS['agent'];
        }
        unset($agent);
    }

    // Unknown Listing Agent
    if (empty($agent) && empty($team_subdomain) && empty($no_listing_agent)) {
        // Use Specific Agent
        if (is_numeric($agent_id)) {
            $query->execute(array('id' => (int) $agent_id));
            $agent = $query->fetch();

        // Random Featured Agent
        } else {
            $query = $db->query("SELECT " . $agent_cols . " FROM `agents` WHERE `display_feature` = 'Y' ORDER BY RAND() LIMIT 1;");
            $agent = $query->fetch();
        }
    }

    // Agent Found
    if (!empty($agent) && empty($team_subdomain)) {
        // Agent Photo
        $agent['image'] = (!empty($thumbnails) ? '/thumbs/' . $thumbnails . '/f' : '') . '/' . (!empty($agent['image']) ? 'uploads/agents/' . $agent['image'] : 'img/404.gif');

        // Do not display phone #
        if (empty($display_phone)) {
            unset($agent['office_phone']);
        }
        if (empty($display_cell)) {
            unset($agent['cell_phone']);
        }

        // Get Primary Agent Team
        $agent_teams = Backend_Team::getTeams($agent['id'], [], [Backend_Team::PERM_SHARE_FEATURE_LISTINGS]);
        foreach ($agent_teams as $agent_team) {
            // Get Agents with Permissions
            $valid_agents = $agent_team->getAgents([Backend_Team::PERM_FEATURE_LISTINGS]);
            if (!empty($valid_agents)) {
                // Load featured agents
                $team_agents_query = $db->prepare("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name`, `office_phone` AS `phone`, `image`"
                        . " FROM `agents` `a`"
                        . " JOIN `team_agent_listings` `tl` ON `a`.`id` = `tl`.`agent_id`"
                        . " WHERE `tl`.`agent_id` != ? AND `tl`.`team_id` = ? AND `tl`.`listing_id` = ? AND `tl`.`listing_feed` = ?"
                        . " AND `id` IN (" . implode(', ', array_fill(0, count($valid_agents), '?')) . ")"
                        . " ORDER BY `order`;");
                $team_agents_query->execute(
                    array_merge([
                        $agent['id'],
                        $agent_team->getId(),
                        $listing['ListingMLS'],
                        $listing['idx']
                    ], $valid_agents)
                );
                $team_agents = $team_agents_query->fetchAll();
                if (!empty($team_agents)) {
                    $team_agents = array_map(function ($team_agent) use ($thumbnails, $display_phone) {
                        $team_agent['image'] = (!empty($thumbnails) ? '/thumbs/' . $thumbnails : '') . '/' . (!empty($team_agent['image']) ? 'uploads/agents/' . $team_agent['image'] : 'img/404.gif');
                        if (empty($display_phone)) {
                            unset($team_agent['phone']);
                        }
                        return $team_agent;
                    }, $team_agents);

                    $agent['team'] = [
                        'id'      => $agent_team->getId(),
                        'title'   => $agent_team->info('name'),
                        'agents'  => $team_agents
                    ];
                    break;
                }
            }
        }
    }

// Database error
} catch (PDOException $e) {
    //echo '<pre>' . $e->getMessage() . '</pre>';
}
