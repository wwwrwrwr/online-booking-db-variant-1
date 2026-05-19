SELECT 
    d.specialization,                    -- 1. Поле для группировки
    COUNT(a.appointment_id) AS total,    -- 2. Агрегатная функция
    SUM(s.price) AS revenue
FROM dentists d
JOIN appointments a ON d.dentist_id = a.dentist_id
JOIN services s ON a.service_id = s.service_id
WHERE a.status = 'проведено'             -- 3. Фильтрация ДО группировки
GROUP BY d.specialization                -- ← ВОТ ЗДЕСЬ GROUP BY
HAVING total > 5                         -- 4. Фильтрация ПОСЛЕ группировки
ORDER BY revenue DESC;
