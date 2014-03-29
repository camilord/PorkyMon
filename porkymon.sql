-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(64) DEFAULT '127.0.0.1',
  `data` longtext,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS users;
CREATE TABLE  IF NOT EXISTS users (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT primary key,
  username varchar(64) not null,
  passcode varchar(200) not null,
  full_name varchar(200),
  last_login varchar(64) default '0000-00-00 00:00:00',
  last_ip varchar(64) default '127.0.0.1',
  created datetime
);
INSERT INTO users (username,passcode,full_name,created) VALUES ('admin',MD5('secret'),'Administrator',NOW());

DROP TABLE IF EXISTS servers;
CREATE TABLE  IF NOT EXISTS servers (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT primary key,
  hostname varchar(200) not null,
  secret_key varchar(200) not null,
  ip varchar(64) default '127.0.0.1',
  created datetime
);
ALTER TABLE servers ADD deleted enum('n','y') default 'n' AFTER ip;
ALTER TABLE servers ADD port_check int(5) default 80 AFTER hostname;

DROP TABLE IF EXISTS server_data;
CREATE TABLE  IF NOT EXISTS server_data (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT primary key,
  server_id bigint(20) default 0,
  hostname varchar(200) not null,
  data longtext,
  created datetime
);
ALTER TABLE server_data ADD INDEX(server_id);

DROP TABLE IF EXISTS server_updates;
CREATE TABLE  IF NOT EXISTS server_updates (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT primary key,
  server_id bigint(20) default 0,
  report_type enum('error','warning','info') default 'info',
  report text,
  created datetime
);