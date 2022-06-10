create table t_cash(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    cashier varchar(12),
    cart_code varchar(12),
    date datetime
);

create table t_cash_detail(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    t_cash_code varchar(12),
    rekening_code varchar(12),
    status varchar(1),
    nominal int
)