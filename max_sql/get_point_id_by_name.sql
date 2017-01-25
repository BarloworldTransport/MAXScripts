select p.ID, l.name from udo_location as l left join udo_point as p on (p._udo_Location_id = l.id) where l.name like "Si Group%" and l._type="udo_Point";

