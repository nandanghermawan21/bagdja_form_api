create table m_member(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    username varchar(50),
    password varchar(200),
    name varchar(100),
    address varchar(200),
    phone varchar(20),
    email varchar(200),
    image_id varchar(12),
    dob date,
    point int,
    silver_ticket int,
    gold_ticket int
)

drop table m_member

select * from m_member

select * from svc_file
where id = '617423300978'

delete from m_member
where username Is null

delete from m_member where
id = '547606781341'

update m_member
set password = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'

delete * from svc_file