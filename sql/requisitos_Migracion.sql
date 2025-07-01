
ALTER TABLE categorias_evento 
ADD COLUMN requiere_nota BOOLEAN DEFAULT FALSE COMMENT 'indica si la categoría requiere calificación',
ADD COLUMN requiere_asistencia BOOLEAN DEFAULT FALSE COMMENT 'indica si la categoría requiere control de asistencia';

