-- 
-- Databáze: `db_databic`
-- 

-- --------------------------------------------------------

-- 
-- Struktura tabulky `wa_files_data`
-- 

CREATE TABLE `wa_files_data` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) NOT NULL default '0',
  `original_filename` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `create_date` int(11) NOT NULL default '0',
  `modify_date` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `wa_files_data`
-- 


-- --------------------------------------------------------

-- 
-- Struktura tabulky `wa_files_folders`
-- 

CREATE TABLE `wa_files_folders` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `wa_files_folders`
-- 

-- --------------------------------------------------------

-- 
-- Struktura tabulky `wa_images_data`
-- 

CREATE TABLE `wa_images_data` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) NOT NULL default '0',
  `original_filename` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `create_date` int(11) NOT NULL default '0',
  `modify_date` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Struktura tabulky `wa_images_folders`
-- 

CREATE TABLE `wa_images_folders` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


-- 
-- Struktura tabulky `wa_urls_data`
-- 

CREATE TABLE `wa_urls_data` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) NOT NULL default '0',
  `url_data` varchar(255) NOT NULL default '',
  `create_date` int(11) NOT NULL default '0',
  `modify_date` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Struktura tabulky `wa_urls_folders`
-- 

CREATE TABLE `wa_urls_folders` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
-- 
-- Struktura tabulky `wa_users`
-- 

CREATE TABLE `wa_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `sa` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `wa_users`
-- 

INSERT INTO `wa_users` (`id`, `username`, `password`,`sa`) VALUES (1, 'admin', '955db0b81ef1989b4a4dfeae8061a9a6',1);

-- --------------------------------------------------------

-- 
-- Struktura tabulky `web_settings`
-- 

CREATE TABLE `web_settings` (
  `id` int(11) NOT NULL auto_increment,
  `param_name` varchar(128) NOT NULL default '',
  `param_value` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `web_settings`
-- 


-- --------------------------------------------------------

-- 
-- Struktura tabulky `web_texts`
-- 

CREATE TABLE `web_texts` (
  `id` int(11) NOT NULL default '0',
  `value` text NOT NULL,
  `lang_id` char(2) NOT NULL default 'cs',
  FULLTEXT KEY `value` (`value`)
) TYPE=MyISAM;

-- 
-- Vypisuji data pro tabulku `web_texts`
-- 

CREATE TABLE `wa_languages` (
  `id` int(11) NOT NULL  auto_increment,
  `lang_id` char(2) NOT NULL default '',
  `lang_name` varchar(64) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0',
   PRIMARY KEY  (`id`)
) TYPE=MyISAM  AUTO_INCREMENT=1;


INSERT INTO `wa_languages` ( `id` , `lang_id` , `lang_name` ,`active`) 
VALUES (
'', 'cs', 'Cestina','1'
);


CREATE TABLE `wa_sitemap` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `def_name` varchar(64) NOT NULL default '',
  `moduls` varchar(255) NOT NULL default '001',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Vypisuji data pro tabulku `wa_sitemap`
-- 

INSERT INTO `wa_sitemap` (`id`, `parent_id`, `def_name`, `moduls`) VALUES (NULL, 0, 'Mapa Webu', '001');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `wa_files_desc` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `uniq_id` int(11) NOT NULL,
  `modul_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

 ALTER TABLE `wa_files_desc`  DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;

-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `wa_images_desc` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `uniq_id` int(11) NOT NULL,
  `modul_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

 ALTER TABLE `wa_images_desc`  DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `wa_urls_desc` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `uniq_id` int(11) NOT NULL,
  `modul_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

 ALTER TABLE `wa_urls_desc`  DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
 
 --
-- Table structure for table `wa_videos_data`
--

CREATE TABLE IF NOT EXISTS `wa_videos_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_id` int(11) NOT NULL DEFAULT '0',
  `server_id` int(11) NOT NULL DEFAULT '0',
  `converted_filename` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `video_width` int(11) NOT NULL DEFAULT '0',
  `video_height` int(11) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `thumb_original_filename` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `thumb_filename` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `modify_date` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wa_videos_folders`
--

CREATE TABLE IF NOT EXISTS `wa_videos_folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
