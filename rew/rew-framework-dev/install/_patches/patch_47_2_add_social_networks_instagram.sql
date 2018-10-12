INSERT INTO `agents_social_networks` (`agent_id`, `name`, `url`) VALUES
(1, 'Instagram', '#')
ON DUPLICATE KEY UPDATE `agent_id` = `agent_id`;
