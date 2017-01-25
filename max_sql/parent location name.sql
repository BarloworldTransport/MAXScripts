select lp.name, lc.name from udo_location as lc left join udo_location as lp on (lp.id=lc.parent_id) where lc.name = "";
