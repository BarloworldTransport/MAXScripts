select fv.id, cu.tradingName, drv.beginDate, drv.endDate, drv.value, drv.type, fv.variableCostRate_id from udo_fandvcontract as fv left join daterangevalue as drv on (drv.objectInstanceId=fv.variableCostRate_id) left join udo_customer as cu on (cu.id=fv.customer_id) where drv.type="Rate" and fv.id;

