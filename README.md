# api_bagja_form

## muat update 30 juli

1. Copy model/application_model.php
2. Copy controller/application.php

## mudat update 22 agustus 2022 dev done server notyet
1. copy file core/my_controller
   => pada function get device info
   => pada fuction __contruct

2. copy file auth
   => pada function loginwithcmo suzuki

## mudat update 24 agustus 2022 dev notyet
note menambahkan with(nolock) pada query
1. model/application_model (sever done)
2. model/form_model
3. model/page_model
4. model/questiongroup_model

## update 31 Agustus 2022
1. model/application_model f getFinished
2. controller/application  f getFinished
3. model/questiontype_model
3. model/dicissiontype_model

update database
1. alter tabel sys_question_type
   ALTER TABLE sys_question_type
   ADD is_active int;

2 alter tabel sys_dicission_type
  ALTER TABLE sys_dicission_type
  ADD is_active int;