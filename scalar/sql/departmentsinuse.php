<?php

$sql1 = 'SELECT distinct dp.id, dp.department
FROM scalar_db.departments dp
inner join employees em on em.department_id=dp.id
order by dp.department';
