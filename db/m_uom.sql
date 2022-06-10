create table m_uom(
    uom_id int primary key,
    uom_name varchar(20),
    uom_code varchar(5)
);

create table m_uom_conversion(
    conversion_id int primary key,
    conversion_name varchar(20),
    uom_basic_id int,
    FOREIGN KEY (uom_basic_id) REFERENCES m_uom(uom_id)
);

create table m_uom_conversion_tree(
    conversion_id int,
    uom_source_id int,
    uom_target_id int,
    conversion_amount int,
    primary key (conversion_id,uom_source_id,uom_target_id),
    FOREIGN KEY (conversion_id) REFERENCES m_uom_conversion(conversion_id),
    FOREIGN KEY (uom_source_id) REFERENCES m_uom(uom_id),
    FOREIGN KEY (uom_target_id) REFERENCES m_uom(uom_id)
);

--insert sample uom
insert into m_uom values 
('1','Batang','BTG'),
('2','Bungkus','BKS'),
('3','Slup','SLP'),
('4','Buah','BH'),
('5','Lusin','LSN'),
('6','Kodi','KD'),
('7','DUS','DS'),
('8','Karton','KRT');

--insert sample uom_conversion
insert into m_uom_conversion values 
    (1,'ROKO 12 BTG',1),
    (2,'ROKO 16 BTG',1),
    (3,'ROKO 20 BTG',1);

--insert sampe uom_conversion_tree
insert into m_uom_conversion_tree values
(1,2,1,12),
(1,3,2,10),
(2,2,1,16),
(2,3,2,10),
(3,2,1,20),
(3,3,2,10)

select * from m_member
