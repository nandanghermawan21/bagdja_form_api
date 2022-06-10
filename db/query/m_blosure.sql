create table blosure (
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    name varchar(50),
    valid_date_start datetime,
    valid_date_end datetime,
    is_active boolean
)