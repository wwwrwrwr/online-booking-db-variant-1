-- Найти клиентов, которые записывались на услуги дороже средней стоимости
SELECT DISTINCT 
    c.client_id,
    CONCAT(c.last_name, ' ', c.first_name, ' ', c.patronymic) AS client_full_name,
    c.phone,
    s.service_name,
    s.price
FROM clients c
INNER JOIN appointments a ON c.client_id = a.client_id
INNER JOIN services s ON a.service_id = s.service_id
WHERE s.price > (
    SELECT AVG(price) 
    FROM services
)
ORDER BY s.price DESC, c.last_name;
