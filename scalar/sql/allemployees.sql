SELECT em.id
,concat_ws(' ', lastname,concat(substring(firstname,1,1),'.'), concat(substring(secondname,1,1),'.')) as fullname
, birthdate
, department
, position
, salarytype
, salary
 FROM employees em
 left outer join departments dp on em.department_id= dp.id
 left outer join positions pn on pn.id= em.position_id
 where department like '%{param1}%'
and position like  '%{param2}%'
 order by id
 limit 1000;

