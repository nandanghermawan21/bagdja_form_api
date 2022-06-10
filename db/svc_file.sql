use u1276530_suzuki;
create table svc_file(
    id varchar(12) primary key,
    filename varchar(500),
    size int,
    extention varchar(10),
    path varchar(50)
)

use u1276530_suzuki;
insert into svc_file (`id`, `filename`, `size`, `extention`, `path`)
values
('612295870834','22bf4eb5-0ba9-4b0c-bad0-03d4ee08064c7778914385997784763.jpg',null,'jpg','customer_photo')

use u1276530_suzuki;
select * from svc_file