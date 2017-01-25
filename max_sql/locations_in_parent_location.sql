select l2.name, l1.name as parent_name from udo_location as l1 left join udo_location as l2 on (l2.parent_id=l1.id) where l1.name IN ("") and l1._type="udo_City";
