# Smart Biller v2.0

Electricity bill fetcher — restructured for clarity and maintainability.

## Project Structure

```
smart_biller_new/
├── bootstrap.php                   ← Single bootstrap (replaces index.php + config.php)
├── check_bills.php                 ← Main entry point (form + POST handler)
├── .htaccess                       ← Security & routing
│
├── app/
│   ├── Controllers/
│   │   └── BillController.php     ← Auto-loads DISCOMs, dispatches fetch
│   └── Helpers/
│       └── functions.php          ← Lean utility functions (replaces 1500-line electricity_bill_function.php)
│
├── config/
│   └── app.php                    ← App-level constants
│
├── curl_functions/                 ← One file per DISCOM (unchanged, plug-and-play)
│   ├── APDCL.php
│   ├── CESC.php
│   ├── WBSEDCL.php
│   └── ...
│
├── assets/
│   ├── Html_Parser/               ← simple_html_dom.php (used by some DISCOMs)
│   └── SetaPDF/                   ← PDF library (used by some DISCOMs)
│
├── captcha/
│   └── 2captcha.php               ← Captcha solver
│
└── download/                      ← Fetched PDFs saved here (auto-created)
```

## What Changed vs Old Version

| Old | New |
|-----|-----|
| `index.php` — 150+ `include_once` lines | `BillController.php` auto-discovers curl_functions via `glob()` |
| `include/electricity_bill_function.php` — 1500 lines, mostly dead debug wrappers | `app/Helpers/functions.php` — ~200 lines, clean implementations |
| `check_bills.php` includes `index.php` which includes all DISCOMs | Clean bootstrap → controller → dispatch |
| No separation of concerns | MVC-inspired: controller handles logic, view handles HTML |
| Basic Tailwind UI | Polished dark-theme UI with animations |

## Adding a New DISCOM

1. Drop `YOUR_DISCOM.php` into `curl_functions/` — it gets auto-loaded.
2. Add its field definition to the `fieldMappings` object in `check_bills.php`.
3. Done — no other files to touch.

## Requirements

- PHP 7.4+
- `curl` extension enabled
- Web server with mod_rewrite (Apache) or equivalent
