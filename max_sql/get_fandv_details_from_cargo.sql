select fv.id, cu.tradingName, drv.beginDate, drv.endDate, drv.value, drv.type, fv.variableCostRate_id from udo_cargo as ca left join udo_fandvcontract as fv on (fv.id=ca.fandVContract_id) left join daterangevalue as drv on (drv.objectInstanceId=fv.variableCostRate_id) left join udo_customer as cu on (cu.id=ca.customer_id) where drv.type="Rate" and ca.id=;

