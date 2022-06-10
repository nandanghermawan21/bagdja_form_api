use u1276530_suzuki;
create table master_gender(
    `id` varchar(2) primary key,
    `name` varchar(20) NULL
)

use u1276530_suzuki;
insert into master_gender (`id`, `name`)
values
('L','Laki-Laki'),
('P','Perempuan')

use u1276530_suzuki;
select * from master_gender

use u1276530_suzuki;
drop table master_gender