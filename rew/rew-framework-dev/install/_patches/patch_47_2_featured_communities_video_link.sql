-- Add video_link column to featured communities
ALTER TABLE `featured_communities`
  ADD COLUMN `video_link` varchar(255) DEFAULT NULL AFTER `anchor_two_link`;
