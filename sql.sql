CREATE TABLE `users` (
    id int auto_increment not null PRIMARY KEY,
    username varchar(255) not null,
    mail varchar(255) not null,
    password varchar (255) not null,
    authenticated boolean default 0,
    user_level int not null default 1,
    joindate datetime default CURRENT_TIMESTAMP);

CREATE TABLE `password_reset` (
  `id` int(11) auto_increment NOT NULL PRIMARY KEY,
  `mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `selector` char(16) COLLATE utf8_bin NOT NULL,
  `token` char(64) COLLATE utf8_bin NOT NULL,
  `expires` datetime(6) NOT NULL)