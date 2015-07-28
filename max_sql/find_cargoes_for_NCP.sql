select distinct ca.id from udo_cargo as ca
left join udo_location as cf on (cf.id=ca.cityFrom_id)
left join udo_location as ct on (ct.id=ca.cityTo_id)
where ca.customer_id = (select id from udo_customer where tradingName="NCP Chlorochem - Chlorine") and ct.name like "%kms Zone%" and ((cf.name ="Chloorkop Ext. (JHB)") or (cf.name="Durban") or (cf.name="Sasolburg") or (cf.name="Richards Bay"));