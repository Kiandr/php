
--
-- Table structure for table `landing_pods`
--

CREATE TABLE IF NOT EXISTS `landing_pods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `active` enum('true','false') NOT NULL DEFAULT 'false',
  `order` int(10) NOT NULL DEFAULT '0',
  `markup` longtext NOT NULL,
  `type` varchar(25) NOT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `landing_pods`
--

REPLACE INTO `landing_pods` (`name`, `title`, `active`, `order`, `markup`, `type`) VALUES
('personalized-cta', 'Personalized CTA', 'false', 0, '<section class="pod sellersIntro">\r\n\r\n    <div class="wrap">\r\n\r\n        <div class="agentPhoto"><img src="{image}" alt="{image-alt-text}"></div>\r\n\r\n        <h1>{heading}</h1>\r\n\r\n        <p class="caption"><a href="javascript:openPage(''/contact.php'');">{sub-heading}</a></p>\r\n\r\n            {as-heard-on}\r\n\r\n        </div>\r\n\r\n</section>', 'radio'),
('phone-cta', 'Phone Contact CTA', 'false', 0, '<section class="pod readyToSell">\r\n\r\n    <div class="wrap">\r\n\r\n        <p><span>{heading}</span> <a href="javascript:openPage(''/contact.php'');">{contact-info}</a></p>\r\n\r\n    </div>\r\n\r\n</section>', 'radio');

--
-- Table structure for table `landing_pods_fields`
--

CREATE TABLE IF NOT EXISTS `landing_pods_fields` (
  `pod_name` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `order` int(10) NOT NULL DEFAULT '0',
  `type` enum('img','text','tinymce','audio','video','tabbed') NOT NULL,
  `hint` varchar(100) NOT NULL,
  `default` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`pod_name`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `landing_pods_fields`
--

REPLACE INTO `landing_pods_fields` (`pod_name`, `name`, `title`, `order`, `type`, `hint`, `default`, `value`) VALUES
('personalized-cta', 'sub-heading', 'Sub-Heading', 1, 'text', '', 'Contact us Today!', ''),
('phone-cta', 'contact-info', 'Contact Blurb', 1, 'text', 'Contact us Today! Call 1(877)555-5555', 'Contact us Today!', '');