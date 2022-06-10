create table m_trx_type(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    name varchar(50),
    data varchar(1000)
);

INSERT into
    m_trx_type
values
    (
        null,
        'PULSA',
        'PULSA',
        '{"operator":"string","nomor":"string","nominal":"int"}'
    );

select * from m_trx_type