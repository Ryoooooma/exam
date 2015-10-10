create database exam;


use exam;

create table posts (
	id int primary key auto_increment not null,
	name varchar(255) not null,
	password varchar(255) not null,
	body varchar(400) not null,
	status enum('active', 'deleted') default 'active' not null,
	created datetime not null,
	modified datetime not null
);


create table comments (
	id int not null primary key auto_increment,
	name varchar(255) not null,
	password varchar(255) not null,
	body varchar(255) not null,
	post_id int not null,
	created datetime not null,
	modified datetime not null
);


insert into posts
(name, password, body, status, created, modified)
values 
(11111, 11111, 11111, 'deleted', now(), now());
