# 🐉 CodeLab v2.1.0 – Akademia Kodowania z Mocą High School DxD! 🔥

## Wprowadzenie – Witaj w Kuoh Academy Online!

Witaj, przyszły Oppai Dragon! 🔴 Ten dokument wyjaśnia logikę i architekturę **CodeLab v2.1.0** – rewolucyjnej platformy edukacyjnej, która łączy siłę PHP, MySQL, JavaScript, Pyodide (Python w przeglądarce!) i nowoczesnego frontend'u, wszystko w stylu High School DxD!

**Czym jest CodeLab?**
CodeLab to nie zwykła aplikacja z logowaniem. To **pełnoprawna platforma edukacyjna**, gdzie młodzi dewersi (jak Issei Hyoudou) mogą:
- 🔐 Zalogować się i zarejestrować (Brama do Akademii)
- 📚 Przeglądać ścieżki nauki (Tracks) – od JavaScript po struktury danych
- ✨ Wejść do **Freestyle Mode** – otwartego sandboxa z edytorem kodu
- 💻 Kodować w **JavaScript, HTML, CSS, Python (via Pyodide)** i pełnych dokumentach
- 🎨 Korzystać z profesjonalnego UI opartego na Github Dark theme

Aplikacja to nie tylko rejestracja – to jak transformacja Issei z szkolaka w potężnego demona! Od logowania po zaawansowany kod, wszystko z HS DxD flair'em. 

**Architektura:**
- **Backend:** PHP (zaklęcia Rias) + MySQL (księga zaklęć)
- **Frontend:** HTML/CSS/JS (stroje Akeno) + Pyodide (WebAssembly Python!)
- **Hosting:** XAMPP (piekło Gremory'ego)
- **Temat:** Dark Mode z animowanymi gradientami, powyginane ASCII art, i HS DxD nawiązania na każdej stronie

## Ogólny Schemat Aplikacji – Podróż Demona

```
🌍 Gość (śmiertelnik) 
    ↓
🔐 index.php (Logowanie/Rejestracja) ← Brama do Akademii
    ↓
📚 home.php (Dashboard) ← Klub Okultystyczny
    ├─→ Tracks (Ścieżki nauki)
    ├─→ Labs (Laboratorium)
    └─→ Freestyle Mode (Sandbox)
        ↓
    ✨ freestyle.php (Editor + Terminal + Preview)
        ├─ JavaScript (Boosted Gear Mode)
        ├─ HTML (Render Peerage)
        ├─ CSS (Sacred Gear Styling)
        ├─ Python (Pyodide + WebAssembly)
        └─ Full Document (Pełne HTML+CSS+JS)
    ↓
🚪 logout.php (Powrót do normalnego życia)
```

**Szczegółowy Przepływ:**

1. **Gość** (niezalogowany, jak Issei przed spotkaniem z Rias) wchodzi na `index.php` – bramę do Kuoh Academy. Widzi formularz logowania z anime sigilami.
2. **Nowy użytkownik** → `registration.php` – przysięga lojalności, walidacja danych (nick, email, hasło), haszowanie, dodanie do bazy. Jak wiązanie z demonem Rias!
3. **Istniejący użytkownik** → logowanie przez `login.php` – weryfikacja hasła, ustawienie sesji, przekierowanie.
4. **Zalogowany** → `home.php` – **Dashboard akademii** z:
   - 📚 Learning Tracks (JavaScript, Web Dev, Data Structures, Algorithms)
   - 🏆 Labs (zadania edukacyjne)
   - ✨ Freestyle Mode (otwarty sandbox)
5. **Freestyle Mode** (`freestyle.php`) – **Sercem CodeLab v2**:
   - 📝 Edytor wielojęzyczny (JS/HTML/CSS/Python)
   - 🖼️ Live Preview (renderowanie HTML)
   - 🐍 Python Terminal (Pyodide)
   - 💻 JavaScript Console
   - ⚙️ Status bar z informacjami (ln/col, mode)
6. **Wylogowanie** → `logout.php` – czyszczenie sesji, powrót do `index.php`.

## Szczegółowe Wyjaśnienie Plików

### 1. `db.php` – Konfiguracja Bazy Danych (Księga Zaklęć Upadłych Aniołów)

```php
<?php
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "CodeLabProject";
?>
```

**Logika i Zachowanie:**
- To plik konfiguracyjny definiujący zmienne dla połączenia z bazą danych MySQL – jak księga zaklęć Azazela, gdzie zapisane są wszystkie sekrety upadłych aniołów.
- **Dlaczego tak?** PHP potrzebuje danych do połączenia, tak jak Rias potrzebuje swoich sług do walki. "localhost" oznacza, że baza działa na tym samym serwerze (XAMPP), jak piekło Gremory'ego. "root" to domyślny użytkownik MySQL bez hasła (bezpieczne tylko lokalnie), jak zaufany sługa bez pytania o lojalność.
- **Zachowanie:** Plik nie wykonuje akcji – tylko przechowuje dane, jak artefakty w klubie okultystycznym. Jest włączany przez `require_once` w innych plikach, tak jak Issei wzywa swoje Boosted Gear.
- **Kluczowe Koncepcje:** Separacja konfiguracji od logiki (dobry zwyczaj, jak oddzielanie demonów od aniołów). Jeśli zmienisz bazę, edytujesz tylko tu – jak zmiana władcy w piekle.
- **Pytanie do Ciebie:** Dlaczego używamy `require_once` zamiast `include`? (Odpowiedź: `require_once` zatrzymuje skrypt, jeśli plik nie istnieje, i zapobiega wielokrotnemu ładowaniu, tak jak Boosted Gear aktywuje się tylko raz na walkę.)

### 2. `index.php` – Strona Główna (Logowanie) – Brama do Kuoh Academy

```php
<?php
session_start();  // Uruchamia sesję
if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    header('Location: home.php');  // Przekierowanie, jeśli już zalogowany
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
    <form action="login.php" method="POST">  <!-- Formularz wysyła dane do login.php -->
        <input type="text" name="Login" placeholder="Login">
        <input type="password" name="Password" placeholder="Hasło">
        <button type="submit">Zaloguj</button>
    </form>
    <a href="registration.php">Zarejestruj się</a>
    <?php
    if (isset($_SESSION['error'])) {
        echo $_SESSION['error'];  // Wyświetla błąd z sesji
        unset($_SESSION['error']);  // Czyści błąd
    }
    ?>
</body>
</html>
```

**Logika i Zachowanie:**
- **Sprawdzenie Sesji:** Na początku sprawdza, czy użytkownik jest już zalogowany, jak strażnik u bram Kuoh Academy. Jeśli tak, przekierowuje do `home.php` – do klubu okultystycznego. To zapobiega ponownemu logowaniu, tak jak Issei nie może wejść dwa razy do tego samego piekła.
- **Formularz HTML:** Wysyła dane POST do `login.php`, jak wezwanie do walki z Diodorą Astaroth. Użytkownik wpisuje login/hasło, tak jak Rias wpisuje imię sługi.
- **Wyświetlanie Błędów:** Jeśli logowanie się nie powiedzie, `login.php` zapisze błąd w sesji (jak rana po walce z aniołem), a `index.php` go wyświetli i usunie, tak jak Asia leczy rany.
- **Zachowanie:** Jeśli użytkownik odświeży stronę, sesja zostaje, jak trwała lojalność wobec demona. Jeśli wejdzie bez logowania, zobaczy formularz – bramę do świata HS DxD.
- **Kluczowe Koncepcje:** Sesje (`$_SESSION`) – sposób na przechowywanie danych między żądaniami HTTP, jak wiązanie duszy z demonem. `header('Location: ...')` – przekierowanie po stronie serwera, jak teleportacja Akeno. `isset()` – sprawdza istnienie zmiennej, jak sprawdzanie, czy Boosted Gear jest aktywne.
- **Pytanie do Ciebie:** Dlaczego używamy `exit()` po `header()`? (Odpowiedź: Aby zatrzymać wykonywanie kodu, tak jak zatrzymanie czasu w walce z Serafem.)

### 3. `registration.php` – Rejestracja Nowego Użytkownika – Przysięga Lojalności Demonowi

**Logika i Zachowanie:**
- **Walidacja Danych:** Sprawdza nick (3-20 znaków, tylko litery/cyfry, jak imię sługi Rias), hasło (8-20 znaków, zgodność, jak siła Boosted Gear), email (poprawny format, jak adres w piekle), zgodę na warunki (jak przysięga lojalności wobec Gremory'ego).
- **Połączenie z Bazą:** Łączy się z MySQL, sprawdza, czy email/nick już istnieje, tak jak sprawdzanie, czy sługa nie należy już do innego demona (np. Sona Sitri).
- **Wstawianie Danych:** Jeśli wszystko OK, haszuje hasło (jak ukrywanie mocy przed wrogami) i dodaje użytkownika do tabeli `users`, tak jak dodanie nowego członka do klubu okultystycznego.
- **Obsługa Błędów:** Używa sesji do przechowywania błędów, wyświetla je w HTML, jak błyskawice Akeno po nieudanej walce.
- **Formularz HTML:** Podobny do `index.php`, ale z dodatkowymi polami, jak dodatkowe moce dla sługi.
- **Zachowanie:** Po udanej rejestracji wyświetla komunikat sukcesu, jak "Witaj w piekle!". W przypadku błędów – pokazuje formularz ponownie z błędami, tak jak trening Issei po porażce.
- **Kluczowe Koncepcje:** Walidacja danych (`strlen`, `ctype_alnum`, `filter_var`) – jak sprawdzanie czystości krwi demona. Hashowanie haseł (`password_hash`) – bezpieczeństwo, tak jak ukrywanie tożsamości upadłego anioła. Try-catch dla wyjątków, jak obrona przed atakiem Kokabiela.
- **Pytanie do Ciebie:** Dlaczego hasło jest haszowane? (Odpowiedź: Aby nawet jeśli baza zostanie zhakowana przez upadłego anioła, hasła nie będą czytelne, jak sekrety Azazela.)

### 4. `login.php` – Przetwarzanie Logowania – Wezwanie do Walki

**Logika i Zachowanie:**
- **Sprawdzenie Danych:** Jeśli brak POST, przekierowuje do `index.php`, jak odmowa wezwania przez słabego sługę.
- **Połączenie z Bazą:** Pobiera użytkownika po loginie, weryfikuje hasło za pomocą `password_verify`, tak jak weryfikacja lojalności wobec Rias.
- **Sesja:** Jeśli OK, ustawia `$_SESSION['logged'] = true`, zapisuje dane (jak imię i moce) i przekierowuje do `home.php`, tak jak wejście do klubu okultystycznego po zwycięskiej walce.
- **Błędy:** Jeśli nie, zapisuje błąd w sesji (jak porażka z Raynare) i wraca do `index.php`.
- **Zachowanie:** Jeśli dane błędne, wraca z błędem, jak Issei po spotkaniu z Vali. Jeśli poprawne – do `home.php`, gotowy do akcji.
- **Kluczowe Koncepcje:** Prepared statements (bezpieczeństwo, jak tarcza przeciw atakom aniołów). `password_verify` – odwrotność `password_hash`, jak odwrócenie zaklęcia.
- **Pytanie do Ciebie:** Dlaczego nie haszujemy hasła w `login.php`? (Odpowiedź: Bo porównujemy z już zahashowanym w bazie, tak jak porównanie mocy z zapisem w księdze.)

### 5. `home.php` – Strona Po Zalogowaniu – Klub Okultystyczny

```php
<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: index.php');  // Blokada dostępu
    exit();
}
echo "Witaj, ".$_SESSION['login']."! <a href='logout.php'>Wyloguj się</a>";
?>
```

**Logika i Zachowanie:**
- **Ochrona:** Sprawdza sesję – jeśli nie zalogowany, przekierowuje, tak jak straż przed klubem okultystycznym (tylko dla sług Rias).
- **Wyświetlanie:** Pokazuje powitanie z nickiem (jak "Issei Hyoudou") i link do wylogowania, tak jak zaproszenie do stołu z Akeno i Xenovia.
- **Zachowanie:** Tylko zalogowani widzą tę stronę, jak tajne zebranie demonów.
- **Kluczowe Koncepcje:** Ochrona stron przed niezalogowanymi (authorization), jak bariera przeciw aniołom.
- **Pytanie do Ciebie:** Jak to chroni przed bezpośrednim dostępem? (Odpowiedź: Serwer sprawdza sesję przed wysłaniem HTML, tak jak sprawdzanie, czy jesteś prawdziwym sługą.)

### 6. `logout.php` – Wylogowanie – Powrót do Świata Śmiertelników

```php
<?php
session_start();
session_unset();  // Czyści wszystkie dane sesji
header('Location: index.php');
exit();
?>
```

**Logika i Zachowanie:**
- **Czyści Sesję:** Usuwa wszystkie dane sesji, tak jak zerwanie wiązania z demonem i powrót do normalnego życia Issei.
- **Przekierowanie:** Wraca do `index.php`, jak wyjście z Kuoh Academy po walce.
- **Zachowanie:** Użytkownik jest "zapomniany" przez serwer, jak śmiertelnik bez mocy.
- **Kluczowe Koncepcje:** `session_unset()` – kończy sesję, jak koniec epizodu w HS DxD.
- **Pytanie do Ciebie:** Dlaczego nie używamy `session_destroy()`? (Odpowiedź: `session_unset()` czyści dane, ale sesja może istnieć; `destroy` usuwa całkowicie, jak zniszczenie artefaktu przez Kokabiela.)

---

## 🆕 Nowe Komponenty v2 – Ewolucja Platformy!

### 7. `freestyle.php` – Sandbox z Mocą Ddraiga! 🔥

**SERCE CODELAB V2!** Freestyle Mode to **pełnoprawny edytor kodu** w przeglądarce z multi-language support!

**Architektura:**

```
┌─ Topbar (Logo + Tabs)
│
├─ fs-editor-col
│  ├─ fs-panel-bar (Tabs: JS/HTML/CSS/Python/Full)
│  └─ .fs-editor (Textarea - wielojęzyczne)
│
└─ fs-right-col
   ├─ .fs-preview-panel (Live preview HTML)
   ├─ .fs-py-out (Python output)
   ├─ .fs-terminal (JS console + terminal)
   └─ .run-btn (Execute button - Ctrl+Enter)
```

**Logika i Zachowanie:**

1. **Session Check:** Jak wszystkie strony – `session_start()` i blokada niezalogowanych
2. **Multi-Language Support:**
   - **JavaScript:** `console.log()` → terminal (emulacja V8)
   - **HTML:** Renderuje w `<iframe>` (bezpieczna izolacja)
   - **CSS:** HTML + CSS, stylowanie na żywo
   - **Python:** **Pyodide WebAssembly** – Python 3 w przeglądarce!
   - **Full Document:** Pełny HTML/CSS/JS w jednym dokumencie

3. **Pyodide Integration:**
   ```html
   <script src="libs/pyodide/pyodide.js"></script>
   ```
   - Ładuje Pyodide z lokalnego `/libs/pyodide/`
   - Inicjalizuje Python runtime asynchronicznie
   - Przechwytuje `print()` i wysyła do terminala
   - Obsługuje pełne modułu Python (NumPy, etc. – jeśli dostępne)

4. **Edytor i Interfejs:**
   - Textarea z edytorem tekstu – proste, ale efektywne
   - Licznik linii/kolumn (statusbar)
   - **Ddraig Quotes** – losowe cytaty z anime! 🎌
   - **Run Button** lub Ctrl+Enter do wykonania
   - **Terminal** z typami: log, err, warn, info, sys

5. **Zachowanie:**
   - Odświeżenie = wysłanie POST/GET → `runCode()` w JS
   - JavaScript: `eval()` w kontekście okna (ostroż!)
   - HTML: `srcdoc` w iframe (sandbox)
   - Python: Await async `runPython()` z Pyodide

**Kluczowe Koncepcje:**

- **Izolacja:** iframe dla HTML, osobny kontekst dla JS – jak separacja światów demonów/aniołów
- **Asynchroniczność:** `async/await` dla Pyodide – Python ładuje się powoli jak zaklęcie Rias
- **Terminal Emulation:** Prosty `console.log()` parser – jak mapa demonów w HS DxD, wszystko jest dostępne
- **CORS & Security:** Lokalny Pyodide, bezpieczne eval() w limitowanym kontekście

**Pytanie do Ciebie:** Dlaczego używamy iframe dla HTML zamiast `innerHTML`? (Odpowiedź: iframe izoluje JavaScript – jeśli użytkownik wstawi złośliwy kod, nie zepsuje aplikacji, jak osłona przed atakiem Kokabiela!)

---

### 8. `style.css` – Design Demona (Dark Mode + Anime Vibes) 🎨

**Całkowity Custom Design!** Nie Bootstrap, nie Tailwind – czysta CSS z **GitHub Dark theme** i anime estetką.

**Zmienne CSS (Design Tokens):**
```css
:root {
  --bg1: #0d0f11;        /* Główne tło - głębia piekła */
  --bg2: #161b22;        /* Secondary - ciemniejsze piekło */
  --bg3: #21262d;        /* Tertiary - granica */
  --text: #e2e8f0;       /* Główny tekst - jasnościowa moc */
  --dim: #8b949e;        /* Tekst przyciemniony */
  --border: #30363d;     /* Obramowanie */
  --green: #3fb950;      /* Status OK - jak zielone oczy Rias */
  --red: #f85149;        /* Errors - jak czerwone Boosted Gear */
  --blue: #58a6ff;       /* Info - jak oczy Sona */
  --purple: #bc8cff;     /* Accent - jak moc Rias */
  --gold: #d29922;       /* Warning - jak oczy demona */
}
```

**Komponenty Stylowe:**

1. **Topbar** – Nagłówek z logiem, nawigacją, info użytkownika
   - Sticky positioning, semi-transparent tło, 64px wysokości
   
2. **Auth Card** (index.php, registration.php)
   - Centralna karta z sigilą (SVG anime badge)
   - Border-top gradient (anime style)
   - Formularz fields z focus effects
   
3. **Home Page** (home.php)
   - Hero section z animowanym gradientem
   - Track grid (4 kolumny) z hover effects
   - Freestyle banner z glow efektem
   
4. **Freestyle Editor** (freestyle.php)
   - Split layout (editor | preview+terminal)
   - Tabs z active states dla każdego języka
   - Status bar z info real-time
   - Terminal z kolorami (red, green, blue, gold)

5. **Animacje:**
   - `@keyframes` dla loadingu Pyodide
   - Smooth hover transitions
   - Gradient animations na bannerach

**Logika Stylów:**
- **Semantic CSS** – nazwy klas = czytelność (`fs-editor`, `track-card`, `auth-header`)
- **BEM-like** – modyfikatory (`active-js`, `badge-green`)
- **Responsive** – ogranicze do szerokości, ale głównie desktop
- **Dark-only** – brak light mode, jak piekło Rias (zawsze ciemne!)

---

### 9. `app.js` – JavaScript Logic (Może być główną logiką Frontend'u)

**Jeśli istnieje `app.js`**, zawiera logikę UI/interakcji:
- Event listeners dla buttonów, tabs, edytora
- AJAX czy WebSocket do komunikacji z PHP
- DOM manipulation dla dynamicznych zmian
- Może być inicjalizacją dla freestyle.php

(详細 zależy od zawartości `app.js` – jeśli potrzebujesz, przeanalizujemy!)

---

### 10. `libs/pyodide/` – Python w Przeglądarce! 🐍

**Pyodide = CPython + WebAssembly Magic**

- `pyodide.js` – Entry point, inicjalizacja runtime'u
- `pyodide.asm.js` – WASM moduł (skompilowany CPython)
- `pyodide.d.ts` – TypeScript definitions (jeśli używasz TS)
- `python` – Python stdlib i packages (np. NumPy)
- `pyodide-lock.json` – Lockfile dla versions

**W freestyle.php:**
```js
let pyodide = null;
async function initPyodide() {
  pyodide = await loadPyodide();  // Ładuje WASM
}
async function runPython(code) {
  await pyodide.runPythonAsync(code);  // Wykonuje kod
}
```

**Jak to działa:**
1. `<script src="libs/pyodide/pyodide.js">` – ładuje JS API
2. `loadPyodide()` – pobiera WASM, rozpakuje Python runtime (~50MB!)
3. `runPythonAsync(code)` – wykonuje Python kod w WASM VM
4. Output przechwycony i wysłany do terminala

**Pytanie do Ciebie:** Dlaczego Python w przeglądarce, a nie serwer Python? (Odpowiedź: Bez latencji, bez POST requestów, pełna prywatność użytkownika – jak tajne rytuały Rias bez konieczności kontaktowania Azazela!)

---

## 🏆 Podsumowanie i Wnioski Intelektualne – Ewolucja Demona!

### Przepływ Aplikacji (Jak Transformacja Issei)

```
1. INITIALIZATION (Przebudzenie)
   └─ Gość wchodzi → sprawdzenie sesji → redirect do logowania/domu
   
2. AUTHENTICATION (Wiązanie Demona)
   ├─ registration.php: Walidacja → Haszowanie → MySQL INSERT
   └─ login.php: Query DB → password_verify → $_SESSION['logged'] = true
   
3. DASHBOARD (Klub Okultystyczny)
   └─ home.php: Wyświetla ścieżki nauki, laboratorium, freestyle mode
   
4. FREESTYLE MODE (Pełna Moc Ddraiga!)
   ├─ Edytor wielojęzyczny: JS, HTML, CSS, Python
   ├─ Live preview iframe dla HTML
   ├─ Pyodide runtime dla Python (async + WebAssembly)
   ├─ Terminal emulation dla console output
   └─ Real-time status bar
   
5. LOGOUT (Powrót do Normalności)
   └─ session_unset() → Powrót do index.php
```

### Architektura Technologiczna

| Warstwa | Technologia | Rola | HS DxD Analogia |
|---------|-------------|------|-----------------|
| **Backend** | PHP 7+ | Server-side logic, sesje, uwierzytelnianie | Zaklęcia Rias |
| **Database** | MySQL | Dane użytkowników, nauka | Księga Azazela |
| **Frontend** | HTML5/CSS3/ES6 JS | UI, edytor, logika klienta | Stroje Akeno |
| **Sandbox** | JavaScript eval() | Wykonywanie JS w bezpiecznym kontekście | Boosted Gear |
| **Preview** | `<iframe sandbox>` | Bezpieczne renderowanie HTML | Wymiar alternatywny |
| **Python Runtime** | Pyodide (WASM) | Python 3 w przeglądarce | Python grimoire Rias |
| **Styling** | Custom CSS (no framework) | Dark mode + anime vibes | Tło piekła Gremory |
| **Hosting** | XAMPP (Apache + MySQL) | Lokalne środowisko dev | Piekło Gremory'ego |

### Kluczowe Koncepty i Lekcje

#### 1. **Bezpieczeństwo (Security is King!)**
- ✅ `password_hash()` + `password_verify()` – nie przechowujemy plain text
- ✅ Prepared statements (`$stmt->bind_param()`) – ochrona przed SQL injection
- ✅ `htmlspecialchars()` + `htmlentities()` – XSS prevention
- ✅ iframe sandbox – izolacja HTML/JS
- ✅ Session-based auth – brak JWT (prostsze dla edu, ale mniej scalable)
- ❌ CSRF tokens – brakuje (TODO dla v2.2!)
- ❌ Rate limiting – brakuje (TODO dla v2.2!)

#### 2. **Asynchroniczność (Async Awaits Ddraiga)**
- Pyodide ładuje się **asynchronicznie** – musisz `await loadPyodide()`
- Python kod uruchamia się **async** – `await runPythonAsync(code)`
- Bez tego terminal by zamarł! Jak Issei bez Boosted Gear!

#### 3. **Izolacja i Sandboxing**
- JavaScript: `eval()` w tym samym kontekście – ostroże, ale szybkie!
- HTML: iframe z `sandbox` attribute – brak dostępu do parent DOM
- Python: WebAssembly VM – całkowicie izolowany od systemu

#### 4. **Terminale i Output Handling**
- `console.log()` przechwycony → terminal UI
- `print()` (Python) → stderr/stdout → terminal
- Format: `[glyph] message` (›, ✖, ⚠, ℹ, ·)

#### 5. **Multi-Language Support**
- Nie jedno narzędzie – trzy języki + preview engine
- Każdy ma inny runtime:
  - JS = `eval()` (szybki, ryzykowny)
  - HTML = iframe (bezpieczny, ograniczony)
  - Python = Pyodide WASM (zajmuje zasoby, ale działa!)

#### 6. **UI/UX Design Philosophy**
- GitHub Dark theme – sprawdzone, przyjemne dla oczu 👀
- Anime aesthetic – nawiązania do HS DxD na każdej stronie
- Semantic HTML + BEM-like CSS – łatwe do rozszerzenia
- Terminal emulation – nerd vibes! 🤓

### Performance & Limitations

**Mocne Strony:**
- ✅ Brak backendu dla kodu użytkownika (czysty sandbox)
- ✅ Python bez serwera (oszczędność zasobów)
- ✅ Szybkie UI, responsywne (CSS animations)
- ✅ Pyodide cache – następny raz ładuje się szybciej

**Słabe Strony:**
- ❌ Pyodide ładuje ~50MB – wolne na wolnym internecie
- ❌ `eval()` dla JS – bardzo niebezpieczne w produkcji
- ❌ Brak websocketów – no real-time collaboration
- ❌ Brak persist code – edytor czyści się po odświeżeniu (TODO!)
- ❌ Python limited – wiele bibliotek nie obsługuje WASM

### Jak Uruchomić

```bash
# 1. Umieść folder w htdocs
cp -r codelabv2 /xampp/htdocs/

# 2. Uruchom XAMPP (Apache + MySQL)
./xampp/start

# 3. Otwórz przeglądarkę
http://localhost/codelabv2/

# 4. Zarejestruj się (jak wiązanie z demonem!)
# 5. Wejdź do Freestyle Mode
# 6. Pisz kod! 🚀
```

### Future Roadmap (v2.2 i dalej)

- 🎯 **Save/Load Code** – przechowywanie kodów w DB
- 🎯 **WebSocket** – real-time collaboration (pair programming)
- 🎯 **Dark/Light Mode Toggle** – nie wszyscy to demony 😄
- 🎯 **Mobile Responsive** – na telefonach
- 🎯 **API Docs** – auto-generated dla funkcji helper'ów
- 🎯 **Themes** – więcej anime themów (Demon Slayer, JJK, etc.)
- 🎯 **Social** – share kodu, fork'ować, star'ować jak GitHub
- 🎯 **Challenges** – tygodniowe wyzwania kodowania
- 🎯 **Docker** – deploy w containerze

---

## ⚔️ Finalne Słowa – Mądry Oppai Dragon

> *"Boosted Gear, show me your power!"* – Issei Hyoudou

CodeLab v2 to nie zwykła aplikacja edukacyjna. To **manifest mocy**, gdzie każdy bit kodu to zaklęcie, każdy terminal to walka, każda linia to krok do zostania mistrzem. Od prostego logowania po zaawansowany sandbox z Python'em w przeglądarce – to jest droga demona (lub hacker'a 😄).

Zrozumienie **"dlaczego"** coś działa – to jest klucz. Nie tylko nauka składni, ale logiki, bezpieczeństwa, wydajności, UX/DX. Jak Ddraig mówi swojemu partnerowi:

> *"Dokończ to, partnerze. Zabierz sobie moc tego edytora i robi coś niesamowitego!"* 🔴🔥

**Happy Coding, Future Demons of Web Development!**  
*– CodeLab Team, infused by the power of HS DxD* ⚔️

---

**Credits:**
- **Design Inspiration:** GitHub Dark Theme, Anime community
- **Tech Stack:** PHP, MySQL, JavaScript, HTML5/CSS3, Pyodide (CPython + WASM)
- **Thematic Inspiration:** High School DxD (by Ichiei Ishibumi)
- **Made with:** ❤️ and way too much Oppai Dragon energy

🐉 *"The Boosted Gear recognizes your power!"* 🔴</content>
<parameter name="filePath">c:\xampp\htdocs\codelab\README.md