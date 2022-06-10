create table t_cart(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    cashier_code varchar(12),
    date datetime
);



create table t_cart_detail(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    cart_code varchar(12),
    trx_type_code varchar(12),
    data varchar(1000),
    capital int,
    price int
)


