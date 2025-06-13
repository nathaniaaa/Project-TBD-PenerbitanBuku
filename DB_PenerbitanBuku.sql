-- ====================================================================
-- SKEMA DATABASE PENERBITAN BUKU
-- ====================================================================

DROP VIEW IF EXISTS Buku_Lengkap;
DROP TABLE IF EXISTS KERJA_SAMA_DISTRIBUSI;
DROP TABLE IF EXISTS KONTRIBUSI_EDITOR;
DROP TABLE IF EXISTS KONTRIBUSI_PENULIS;
DROP TABLE IF EXISTS BUKU;
DROP TABLE IF EXISTS DISTRIBUTOR;
DROP TABLE IF EXISTS PENULIS;
DROP TABLE IF EXISTS EDITOR;

-- ====================================================================
-- BAGIAN 1: PEMBUATAN STRUKTUR TABEL (TABLE SCHEMA)
-- ====================================================================

-- Tabel master untuk entitas utama
CREATE TABLE DISTRIBUTOR (
    ID_Distributor INT NOT NULL AUTO_INCREMENT,
    NamaDistributor VARCHAR(50) UNIQUE,
    TahunBerdiri INT,
    PRIMARY KEY (ID_Distributor)
);

CREATE TABLE EDITOR (
    ID_Editor INT NOT NULL AUTO_INCREMENT,
    NamaEditor VARCHAR(50) UNIQUE,
    Divisi VARCHAR(20),
    PRIMARY KEY (ID_Editor)
);

CREATE TABLE PENULIS (
    ID_Penulis INT NOT NULL AUTO_INCREMENT,
    NamaPenulis VARCHAR(50) UNIQUE,
    AlamatPenulis VARCHAR(100),
    EmailPenulis VARCHAR(50),
    PRIMARY KEY (ID_Penulis)
);

-- Tabel transaksional BUKU
CREATE TABLE BUKU (
    ISBN VARCHAR(50) NOT NULL,
    Judul VARCHAR(225),
    Kategori VARCHAR(20),
    TahunTerbit INT,
    Harga NUMERIC,
    StatusPublikasi VARCHAR(15),
    PRIMARY KEY (ISBN)
);

-- Tabel PENGHUBUNG (Junction Tables) untuk relasi Many-to-Many
CREATE TABLE KONTRIBUSI_PENULIS (
    ID_Penulis INT NOT NULL,
    ISBN VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_Penulis, ISBN),
    FOREIGN KEY (ID_Penulis) REFERENCES PENULIS(ID_Penulis) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ISBN) REFERENCES BUKU(ISBN) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE KONTRIBUSI_EDITOR (
    ID_Editor INT NOT NULL,
    ISBN VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_Editor, ISBN),
    FOREIGN KEY (ID_Editor) REFERENCES EDITOR(ID_Editor) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ISBN) REFERENCES BUKU(ISBN) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE KERJA_SAMA_DISTRIBUSI (
    ID_Distributor INT NOT NULL,
    ISBN VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_Distributor, ISBN),
    FOREIGN KEY (ID_Distributor) REFERENCES DISTRIBUTOR(ID_Distributor) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ISBN) REFERENCES BUKU(ISBN) ON UPDATE CASCADE ON DELETE CASCADE
);

-- ====================================================================
-- BAGIAN 2: PENGISIAN DATA AWAL (SEEDING)
-- ====================================================================

-- Data untuk tabel BUKU
INSERT INTO BUKU (ISBN, Judul, Kategori, TahunTerbit, Harga, StatusPublikasi) VALUES
('978-602-031-893-1', 'Laskar Pelangi', 'Fiksi', 2005, 85000, 'Cetak'),
('978-602-032-783-2', 'Bumi', 'Fiksi', 2014, 90000, 'Distribusi'),
('978-602-033-532-3', 'Supernova: Ksatria Putri', 'Fiksi', 2001, 75000, 'Cetak'),
('978-602-088-641-8', 'Sejarah Indonesia Modern', 'Non-Fiksi', 2022, 120000, 'Editing'),
('978-602-099-876-9', 'Filosofi Teras', 'Self Improvement', 2019, 65000, 'Distribusi');

-- Data untuk tabel PENULIS
INSERT INTO PENULIS (NamaPenulis, AlamatPenulis, EmailPenulis) VALUES
('Andrea Hirata', 'Jl. Belitung 12', 'andrea@gmail.com'),
('Tere Liye', 'Jl. Bengkulu 45', 'tereliye@gmail.com'),
('Dee Lestari', 'Jl. Cipaganti 5', 'dee@gmail.com'),
('Merle Ricklefs', 'Jl. Melbourne 1', 'merle@gmail.com'),
('Henry Manampiring', 'Jl. Surabaya 7', 'henrym@gmail.com');

-- Data untuk tabel EDITOR
INSERT INTO EDITOR (NamaEditor, Divisi) VALUES
('Dian Sastro', 'Fiksi'),
('Budi Santoso', 'Non-Fiksi'),
('Rina Wijaya', 'Akademik'),
('Teguh Afandi', 'Komik'),
('Sabrina Tjahjadi', 'Fiksi');

-- Data untuk tabel DISTRIBUTOR
INSERT INTO DISTRIBUTOR (NamaDistributor, TahunBerdiri) VALUES
('Tiga Serangkai', 1958),
('Surya Distribusi', 2000),
('Bukuku Network', 2004),
('Gramedia', 1970),
('Agromedia', 2001);

-- Data untuk tabel penghubung KONTRIBUSI_PENULIS
INSERT INTO KONTRIBUSI_PENULIS (ID_Penulis, ISBN) VALUES
((SELECT ID_Penulis FROM PENULIS WHERE NamaPenulis = 'Andrea Hirata'), '978-602-031-893-1'),
((SELECT ID_Penulis FROM PENULIS WHERE NamaPenulis = 'Tere Liye'), '978-602-032-783-2'),
((SELECT ID_Penulis FROM PENULIS WHERE NamaPenulis = 'Dee Lestari'), '978-602-033-532-3'),
((SELECT ID_Penulis FROM PENULIS WHERE NamaPenulis = 'Merle Ricklefs'), '978-602-088-641-8'),
((SELECT ID_Penulis FROM PENULIS WHERE NamaPenulis = 'Henry Manampiring'), '978-602-099-876-9');

-- Data untuk tabel penghubung KONTRIBUSI_EDITOR
INSERT INTO KONTRIBUSI_EDITOR (ID_Editor, ISBN) VALUES
((SELECT ID_Editor FROM EDITOR WHERE NamaEditor = 'Dian Sastro'), '978-602-031-893-1'),
((SELECT ID_Editor FROM EDITOR WHERE NamaEditor = 'Sabrina Tjahjadi'), '978-602-032-783-2'),
((SELECT ID_Editor FROM EDITOR WHERE NamaEditor = 'Sabrina Tjahjadi'), '978-602-033-532-3'),
((SELECT ID_Editor FROM EDITOR WHERE NamaEditor = 'Budi Santoso'), '978-602-088-641-8'),
((SELECT ID_Editor FROM EDITOR WHERE NamaEditor = 'Budi Santoso'), '978-602-099-876-9');

-- Data untuk tabel penghubung KERJA_SAMA_DISTRIBUSI
INSERT INTO KERJA_SAMA_DISTRIBUSI (ID_Distributor, ISBN) VALUES
((SELECT ID_Distributor FROM DISTRIBUTOR WHERE NamaDistributor = 'Tiga Serangkai'), '978-602-031-893-1'),
((SELECT ID_Distributor FROM DISTRIBUTOR WHERE NamaDistributor = 'Gramedia'), '978-602-032-783-2'),
((SELECT ID_Distributor FROM DISTRIBUTOR WHERE NamaDistributor = 'Surya Distribusi'), '978-602-033-532-3'),
((SELECT ID_Distributor FROM DISTRIBUTOR WHERE NamaDistributor = 'Bukuku Network'), '978-602-088-641-8'),
((SELECT ID_Distributor FROM DISTRIBUTOR WHERE NamaDistributor = 'Agromedia'), '978-602-099-876-9');


-- ====================================================================
-- BAGIAN 3: PEMBUATAN VIEW
-- ====================================================================

-- VIEW untuk menampilkan semua data gabungan
CREATE VIEW Buku_Lengkap AS
SELECT 
    B.ISBN,
    B.Judul,
    B.Kategori,
    B.TahunTerbit,
    B.Harga,
    B.StatusPublikasi,
    GROUP_CONCAT(DISTINCT P.NamaPenulis SEPARATOR ', ') AS Penulis,
    GROUP_CONCAT(DISTINCT E.NamaEditor SEPARATOR ', ') AS Editor,
    GROUP_CONCAT(DISTINCT D.NamaDistributor SEPARATOR ', ') AS Distributor
FROM BUKU B
LEFT JOIN KONTRIBUSI_PENULIS KP ON B.ISBN = KP.ISBN
LEFT JOIN PENULIS P ON KP.ID_Penulis = P.ID_Penulis
LEFT JOIN KONTRIBUSI_EDITOR KE ON B.ISBN = KE.ISBN
LEFT JOIN EDITOR E ON KE.ID_Editor = E.ID_Editor
LEFT JOIN KERJA_SAMA_DISTRIBUSI KSD ON B.ISBN = KSD.ISBN
LEFT JOIN DISTRIBUTOR D ON KSD.ID_Distributor = D.ID_Distributor
GROUP BY B.ISBN;
