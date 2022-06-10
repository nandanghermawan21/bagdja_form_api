create table trx_point (
    id int primary key,
    noref varchar(50),
    date datetime,
    member_id_source int,
    type varchar(1),
    trx_code int,
    trx_note varchar(50),
    member_id_dest int,
    user_id int
)