create table m_cashier(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    name varchar(50),
    username varchar(50),
    password varchar(50),
    status int
)

delete from m_cashier