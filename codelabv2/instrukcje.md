# CodeLab v2.1.0 — Instrukcja instalacji i uruchomienia

## 1. Wymagania

- Zainstalowany **XAMPP** (Apache + MySQL)
- Przeglądarka internetowa (Chrome/Firefox zalecana)

## 2. Przygotowanie projektu

1. Skopiuj cały folder projektu `codelab` do katalogu:(lub odpowiednik na innym systemie)

2. Upewnij się, że w folderze `codelab` znajduje się katalog:(zawierający pliki Pyodide – jest wymagany do działania Pythona w Freestyle Mode)

## 3. Konfiguracja bazy danych

### Sposób A: Import pliku SQL (zalecany)

1. Uruchom **XAMPP** i włącz **Apache** oraz **MySQL**.
2. Wejdź do **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Utwórz nową bazę danych o nazwie **`CodeLabProject`**.
4. Wybierz utworzoną bazę z lewej strony.
5. Przejdź do zakładki **Import**.
6. Wybierz plik `CodeLabProject.sql` i kliknij **Importuj**.

### Sposób B: Ręczne utworzenie tabeli

Jeśli wolisz, wykonaj poniższe zapytanie SQL:

```sql
CREATE DATABASE IF NOT EXISTS CodeLabProject CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE CodeLabProject;

CREATE TABLE IF NOT EXISTS users (
 id          INT AUTO_INCREMENT PRIMARY KEY,
 email       VARCHAR(255) NOT NULL UNIQUE,
 login       VARCHAR(100) NOT NULL UNIQUE,
 password    VARCHAR(255) NOT NULL,
 created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;