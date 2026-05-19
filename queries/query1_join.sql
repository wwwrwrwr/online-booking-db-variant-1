-- Запрос 1: JOIN трёх таблиц 
SELECT 
  c.last_name AS familiya_klienta,
  c.first_name AS imya_klienta,
  d.last_name AS familiya-vracha,
  d.specialization AS specializatsiya,
  s.service_name AS usluga,
  s.price AS tsena,
  a.appointment_datetime AS data_priema,
  a.status AS status 
FROM appointments a
JOIN clients c ON a.client_id = c.client_id
JOIN dentists d ON a.dentist_id = d.dentist_id
JOIN services s ON a.service_id = s.service_id
ORDER BY a.appointment_datetime;
