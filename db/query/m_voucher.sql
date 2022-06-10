
create table m_voucher(
    id int AUTO_INCREMENT primary key,
    code varchar(12),
    count int,
    used int,
    point int,
    silver_ticket int,
    gold_ticket int,
    start_date datetime,
    end_date datetime,
    create_date datetime
)

select * from m_voucher
where id = '283725440099'

update m_voucher
set point = 5 where point is null

delete from m_voucher

drop table trx_voucher

select * from m_member