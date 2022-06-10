CREATE TABLE m_rekening (
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    name varchar(50),
    saldo int
);

INSERT into
    m_rekening
values
    (null, '0000000001', 'cash-laci', 0),
    (null, '0000000002', 'dana-nandang', 0);

select
    *
from
    m_rekening