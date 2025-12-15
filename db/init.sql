CREATE TABLE IF NOT EXISTS naudotojas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vardas VARCHAR(50) NOT NULL,
  pavarde VARCHAR(50) NOT NULL,
  el_pastas VARCHAR(100) NOT NULL UNIQUE,
  slaptazodis_hash VARCHAR(255) NOT NULL,
  role ENUM('administratorius','klientas','technikas') NOT NULL,
  sukurimo_data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS uzsakymas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kliento_id INT NOT NULL,
  techniko_id INT NULL,
  aprasymas TEXT NOT NULL,
  sukurimo_data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  uzbaigimo_data DATETIME NULL,
  busena ENUM('pateiktas','vykdomas','ivykdytas') NOT NULL DEFAULT 'pateiktas',
  CONSTRAINT fk_uzsakymas_klientas FOREIGN KEY (kliento_id) REFERENCES naudotojas(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_uzsakymas_technikas FOREIGN KEY (techniko_id) REFERENCES naudotojas(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pranesimas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  uzsakymas_id INT NOT NULL,
  kliento_id INT NOT NULL,
  data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  turinys VARCHAR(255) NOT NULL,
  CONSTRAINT fk_pranesimas_uzsakymas FOREIGN KEY (uzsakymas_id) REFERENCES uzsakymas(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pranesimas_klientas FOREIGN KEY (kliento_id) REFERENCES naudotojas(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO naudotojas (vardas, pavarde, el_pastas, slaptazodis_hash, role) VALUES
('Admin', 'Istratorius', 'admin@example.com', SHA2('demo',256), 'administratorius'),
('Tekas', 'Nikas', 'tech@example.com', SHA2('demo',256), 'technikas'),
('Kla', 'Ientas', 'client@example.com', SHA2('demo',256), 'klientas');

INSERT INTO uzsakymas (kliento_id, techniko_id, aprasymas, busena)
VALUES ((SELECT id FROM naudotojas WHERE el_pastas='client@example.com'), NULL, 'Sutaisyti spausdintuvą', 'pateiktas');