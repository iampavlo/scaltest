<?php

$sql1 = 'SELECT distinct ps.id, ps.position
FROM scalar_db.positions ps
inner join employees em on em.position_id=ps.id
order by ps.position';
