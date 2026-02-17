# Tytuł roboczy / nazwa kodowa projektu  
**CodeLab Playground**

---

## Architektura
- SPA (Single Page Application)  
- Architektura modułowa (frontend + usługi backendowe)  
- REST API (dla kont użytkowników i projektów)  
- MVC (Model-View-Controller)  

---

## Stack technologiczny (języki, frameworki, biblioteki)
- HTML, CSS, JavaScript  
- JSON, Markdown  
- Node.js  
- Vite  
- marked.js (renderowanie Markdown)  
- Firebase (uwierzytelnianie i baza danych)  
- iframe API (podgląd w czasie rzeczywistym)  

---

## Logika biznesowa
Platforma edukacyjna umożliwiająca naukę programowania poprzez interaktywny edytor kodu, wyzwania oraz funkcje społecznościowe.  
Użytkownik tworzy projekty, rozwiązuje zadania, zdobywa osiągnięcia i może udostępniać swoją pracę innym.

---

## Przykładowe funkcjonalności i przepływy danych UX/UI

### **Faza 1: Basic Playground (Tydzień 1)**
- Edytor kodu z zakładkami HTML / CSS / JS  
- Podgląd w czasie rzeczywistym (iframe)  
- Zapisywanie i wczytywanie projektów (localStorage)  
- Gotowe zadania startowe  
- Podświetlanie składni  

**Przepływ:**  
Użytkownik wpisuje kod → system renderuje wynik w iframe → projekt może zostać zapisany lokalnie.

---

### **Faza 2: Challenges & Validation (Tydzień 2–3)**
- Ponad 20 prowadzonych wyzwań programistycznych  
- Automatyczne testy wizualne (porównywanie zrzutów ekranu)  
- Odznaki postępu i osiągnięcia  
- Galeria projektów z linkami do udostępniania  
- Narzędzia do testowania responsywności  

**Przepływ:**  
Użytkownik rozwiązuje zadanie → system waliduje wynik → przyznawana jest odznaka → projekt może trafić do galerii.

---

### **Faza 3: Social & Advanced (Tydzień 4+)**
- Konta użytkowników (Firebase Auth)  
- Publiczne udostępnianie projektów  
- Forkowanie / remix projektów innych użytkowników  
- Tablice wyników (leaderboards)  
- Eksport projektów do CodePen  

**Przepływ:**  
Użytkownik publikuje projekt → trafia on do publicznej galerii → inni mogą go forkować → aktywność wpływa na ranking.

---

## Grupa docelowa
**Odbiorcy:**  
- Początkujący programiści  
- Uczniowie i studenci  
- Osoby uczące się frontend developmentu  

**Potrzeby i oczekiwania:**  
- Nauka poprzez praktykę  
- Natychmiastowa informacja zwrotna  
- Możliwość budowania portfolio  
- Interakcja społecznościowa  

---

## Wymagania techniczne
- Nowoczesna przeglądarka (Chrome, Firefox, Edge, Safari)  
- Stabilne połączenie internetowe (dla funkcji online)  
- Responsywny interfejs (desktop + mobile)  

---

## Wymagania prawne
- Zgodność z RODO (cookies, przetwarzanie danych użytkowników)  
- Jasne warunki licencyjne dla publikowanych projektów  
- Poszanowanie praw autorskich  

---

## Standaryzacja
- WCAG 2.1 — dostępność cyfrowa  
- SEO — optymalizacja treści publicznych  

---

## Standardowe wymagania końcowe projektu
- Dokumentacja techniczna kodu (JSDoc, itp.)  
- Dokumentacja Markdown logiki biznesowej i architektury  
- Instrukcja uruchomienia projektu  
- Opis API  