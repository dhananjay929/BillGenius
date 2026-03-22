<?php
/**
 * Smart Biller - Main Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new BillController(__DIR__ . '/curl_functions');
    $consumerDetails = [];
    foreach ($_POST as $key => $value) {
        $consumerDetails[$key] = trim($value);
    }
    $result = $controller->fetchBill($consumerDetails);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Biller — Electricity Bill Fetcher</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ── CSS Variables ── */
        :root {
            --bg:        #0a0c10;
            --surface:   #111318;
            --surface2:  #181c24;
            --border:    #1f2430;
            --border2:   #2a3040;
            --accent:    #f0c040;
            --accent2:   #e8a020;
            --accent-glow: rgba(240,192,64,0.18);
            --green:     #3ecf8e;
            --green-dim: rgba(62,207,142,0.12);
            --red:       #ff5562;
            --red-dim:   rgba(255,85,98,0.12);
            --text:      #e8ecf2;
            --text-muted:#7a8499;
            --text-dim:  #4a5268;
            --radius:    12px;
            --radius-lg: 20px;
            --shadow:    0 24px 64px rgba(0,0,0,0.6);
        }

        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'Syne', sans-serif;
            overflow-x: hidden;
        }

        /* ── Background Grid ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(240,192,64,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(240,192,64,0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Glow blobs ── */
        body::after {
            content: '';
            position: fixed;
            top: -20%;
            left: -10%;
            width: 60%;
            height: 60%;
            background: radial-gradient(ellipse at center, rgba(240,192,64,0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Layout ── */
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 48px 20px 80px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 52px;
        }
        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(240,192,64,0.08);
            border: 1px solid rgba(240,192,64,0.2);
            border-radius: 100px;
            padding: 6px 16px;
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            color: var(--accent);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .header-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.7); }
        }

        h1 {
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.05;
            color: var(--text);
        }
        h1 span {
            color: var(--accent);
            position: relative;
        }
        .header-sub {
            margin-top: 14px;
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 520px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: slideUp 0.5s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            padding: 28px 32px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .card-icon {
            width: 44px;
            height: 44px;
            background: var(--accent-glow);
            border: 1px solid rgba(240,192,64,0.25);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .card-title {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .card-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
            font-family: 'Space Mono', monospace;
        }

        .card-body { padding: 28px 32px 32px; }

        /* ── Notification ── */
        .notification {
            border-radius: var(--radius);
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            line-height: 1.5;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notification.success {
            background: var(--green-dim);
            border: 1px solid rgba(62,207,142,0.25);
            color: var(--green);
        }
        .notification.error {
            background: var(--red-dim);
            border: 1px solid rgba(255,85,98,0.25);
            color: var(--red);
        }
        .notification-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .notification-text { flex: 1; }
        .notification-text a {
            color: inherit;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        .notification-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            opacity: 0.6;
            padding: 0;
            flex-shrink: 0;
        }
        .notification-close:hover { opacity: 1; }

        /* ── Form Elements ── */
        .field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }
        label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            font-family: 'Space Mono', monospace;
        }

        .select-wrapper {
            position: relative;
        }
        .select-wrapper::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid var(--text-muted);
            pointer-events: none;
        }

        select, input[type="text"], input[type="password"], input[type="number"] {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: var(--radius);
            padding: 13px 16px;
            font-size: 14px;
            font-family: 'Syne', sans-serif;
            font-weight: 500;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }
        select { padding-right: 40px; cursor: pointer; }
        select:focus,
        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        input::placeholder { color: var(--text-dim); }
        select option { background: var(--surface2); }

        /* ── Dynamic Fields ── */
        #extraFields {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.4s cubic-bezier(0.16,1,0.3,1), opacity 0.3s ease;
            opacity: 0;
        }
        #extraFields.visible {
            max-height: 600px;
            opacity: 1;
        }
        .extra-fields-inner {
            padding-top: 4px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* ── Divider ── */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 20px 0;
        }

        /* ── Buttons ── */
        .btn-group { display: flex; gap: 10px; margin-top: 24px; }

        .btn {
            flex: 1;
            padding: 13px 20px;
            border-radius: var(--radius);
            border: none;
            font-family: 'Syne', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn:active { transform: scale(0.97); }

        .btn-primary {
            background: var(--accent);
            color: #0a0c10;
            box-shadow: 0 4px 20px rgba(240,192,64,0.25);
        }
        .btn-primary:hover {
            box-shadow: 0 6px 28px rgba(240,192,64,0.4);
            background: #f5cc58;
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border2);
        }
        .btn-ghost:hover {
            border-color: var(--border2);
            color: var(--text);
            background: var(--surface2);
        }

        /* ── Loader ── */
        .loader {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            padding: 24px 0 8px;
        }
        .loader.visible { display: flex; }
        .loader-ring {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 3px solid var(--border2);
            border-top-color: var(--accent);
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loader-text {
            font-size: 13px;
            color: var(--text-muted);
            font-family: 'Space Mono', monospace;
            letter-spacing: 0.05em;
        }

        /* ── Stats Row ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 40px;
            width: 100%;
            max-width: 520px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 16px;
            text-align: center;
        }
        .stat-num {
            font-size: 26px;
            font-weight: 800;
            color: var(--accent);
            font-family: 'Space Mono', monospace;
            letter-spacing: -0.03em;
        }
        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 48px;
            text-align: center;
            font-size: 12px;
            color: var(--text-dim);
            font-family: 'Space Mono', monospace;
        }

        /* ── Responsive ── */
        @media (max-width: 560px) {
            .card-header, .card-body { padding-left: 20px; padding-right: 20px; }
            h1 { font-size: 32px; }
            .stats-row { grid-template-columns: repeat(3, 1fr); gap: 8px; }
            .stat-card { padding: 14px 10px; }
            .stat-num { font-size: 20px; }
        }
    </style>
</head>
<body>
<div class="page">

    <!-- Header -->
    <header class="header">
        <div class="header-badge">Smart Biller v2.0</div>
        <h1>Fetch Your<br><span>Electricity Bill</span></h1>
        <p class="header-sub">Select your DISCOM, enter your details, and download instantly.</p>
    </header>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon">⚡</div>
            <div>
                <div class="card-title">Bill Fetch Console</div>
                <div class="card-subtitle">// consumer portal access</div>
            </div>
        </div>

        <div class="card-body">

            <!-- Notification -->
            <?php if ($result !== null): ?>
            <div id="notification" class="notification <?= $result['success'] ? 'success' : 'error' ?>">
                <span class="notification-icon"><?= $result['success'] ? '✓' : '✕' ?></span>
                <span class="notification-text">
                    <?php if ($result['success']): ?>
                        Bill fetched successfully!
                        <a href="<?= htmlspecialchars($result['file_path']) ?>" target="_blank">View Bill &rarr;</a>
                    <?php else: ?>
                        <?= htmlspecialchars($result['message']) ?>
                    <?php endif; ?>
                </span>
                <button class="notification-close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" id="discom_form">
                <!-- DISCOM Selector -->
                <div class="field-group">
                    <label for="discom">Select DISCOM</label>
                    <div class="select-wrapper">
                        <select id="discom" name="discom_name" required>
                            <option value="">— Choose your electricity board —</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic Extra Fields -->
                <div id="extraFields">
                    <div class="extra-fields-inner" id="extraFieldsInner"></div>
                </div>

                <div class="divider"></div>

                <!-- Buttons -->
                <div class="btn-group">
                    <button type="submit" id="fetchBtn" class="btn btn-primary">
                        <span id="btnIcon">⚡</span>
                        <span id="btnText">Fetch Bill</span>
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-ghost">
                        ↺ Reset
                    </button>
                </div>

                <!-- Loader -->
                <div class="loader" id="loader">
                    <div class="loader-ring"></div>
                    <div class="loader-text">Connecting to DISCOM portal…</div>
                </div>
            </form>

        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num" id="statDiscoms">—</div>
            <div class="stat-label">DISCOMs</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">PDF</div>
            <div class="stat-label">Output Format</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">🔒</div>
            <div class="stat-label">Secure Fetch</div>
        </div>
    </div>

    <footer class="footer">
        Smart Biller v2.0 &mdash; Electricity Bill Automation System
    </footer>

</div>

<!-- Field Mappings from old project (only active DISCOMs) -->
<script>
const fieldMappings = {
    "APDCL": [
        { label: "Installation No", type: "text", name: "installation_no", placeholder: "Enter Installation No" },
        { label: "K No (Consumer ID)", type: "text", name: "consumer_id", placeholder: "Enter K No" }
    ],
    "ARPDOP": [
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "BESCOM": [
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "CESC": [
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "CESU": [
        { label: "Consumer No", type: "text", name: "consumer_id", placeholder: "Enter Consumer No" }
    ],
    "DHBVN": [
        { label: "Account No", type: "text", name: "consumer_id", placeholder: "Enter Account No" }
    ],
    "MESCOM": [
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "MPPKVVCL_CENTRAL": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "MPPKVVCL_CENTRAL_HT": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "MPPKVVCL_EAST": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "MPPKVVCL_EAST_HT": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "MPPKVVCL_WEST": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "MPPKVVCL_WEST_HT": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "NPCL": [
        { label: "Consumer No", type: "text", name: "consumer_id", placeholder: "Enter Consumer No" },
        { label: "Mobile No", type: "text", name: "mobile_no", placeholder: "Enter Mobile No" }
    ],
    "TPDDL": [
        { label: "BP Number / CA Number", type: "text", name: "consumer_id", placeholder: "Enter BP/CA Number" }
    ],
    "UHBVN": [
        { label: "Account No", type: "text", name: "consumer_id", placeholder: "Enter Account No" }
    ],
    "WBSEDCL": [
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "gujrat_new": [
        { label: "Username", type: "text", name: "username", placeholder: "Enter Username" },
        { label: "Password", type: "password", name: "password", placeholder: "Enter Password" },
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ],
    "haryana": [
        { label: "Account No", type: "text", name: "consumer_id", placeholder: "Enter Account No" }
    ],
    "karnataka_function": [
        { label: "Consumer ID", type: "text", name: "consumer_id", placeholder: "Enter Consumer ID" }
    ]
};

// Human-readable display names
const discomLabels = {
    "APDCL": "APDCL — Assam (Northeast)",
    "ARPDOP": "ARPDOP — Andhra Pradesh",
    "BESCOM": "BESCOM — Bangalore",
    "CESC": "CESC — Kolkata",
    "CESU": "CESU — Odisha Central",
    "DHBVN": "DHBVN — Haryana (South)",
    "MESCOM": "MESCOM — Mangalore",
    "MPPKVVCL_CENTRAL": "MPPKVVCL Central — MP",
    "MPPKVVCL_CENTRAL_HT": "MPPKVVCL Central HT — MP",
    "MPPKVVCL_EAST": "MPPKVVCL East — MP",
    "MPPKVVCL_EAST_HT": "MPPKVVCL East HT — MP",
    "MPPKVVCL_WEST": "MPPKVVCL West — MP",
    "MPPKVVCL_WEST_HT": "MPPKVVCL West HT — MP",
    "NPCL": "NPCL — Noida Power",
    "TPDDL": "TPDDL — Delhi (North/West)",
    "UHBVN": "UHBVN — Haryana (North)",
    "WBSEDCL": "WBSEDCL — West Bengal",
    "gujrat_new": "GUVNL — Gujarat",
    "haryana": "DHBVN/UHBVN — Haryana",
    "karnataka_function": "BESCOM/GESCOM — Karnataka"
};
</script>

<script>
(function () {
    const dropdown    = document.getElementById('discom');
    const extraFields = document.getElementById('extraFields');
    const extraInner  = document.getElementById('extraFieldsInner');
    const form        = document.getElementById('discom_form');
    const loader      = document.getElementById('loader');
    const fetchBtn    = document.getElementById('fetchBtn');
    const resetBtn    = document.getElementById('resetBtn');
    const statDiscoms = document.getElementById('statDiscoms');

    // ── Populate dropdown ──
    const keys = Object.keys(fieldMappings).sort();
    statDiscoms.textContent = keys.length;
    keys.forEach(key => {
        const opt = document.createElement('option');
        opt.value = key;
        opt.textContent = discomLabels[key] || key;
        dropdown.appendChild(opt);
    });

    // ── Build dynamic fields ──
    function buildFields(discom) {
        extraInner.innerHTML = '';
        const fields = fieldMappings[discom];
        if (!fields || fields.length === 0) {
            extraFields.classList.remove('visible');
            return;
        }
        fields.forEach(field => {
            const group = document.createElement('div');
            group.className = 'field-group';

            const lbl = document.createElement('label');
            lbl.textContent = field.label;

            const inp = document.createElement('input');
            inp.type = field.type;
            inp.name = field.name;
            inp.placeholder = field.placeholder;
            inp.required = true;
            inp.autocomplete = field.type === 'password' ? 'current-password' : 'off';

            group.appendChild(lbl);
            group.appendChild(inp);
            extraInner.appendChild(group);
        });
        extraFields.classList.add('visible');
    }

    // ── Restore saved form state ──
    function restoreState() {
        try {
            const saved = JSON.parse(localStorage.getItem('sb_formData') || 'null');
            if (!saved) return;
            if (saved.discom_name) {
                dropdown.value = saved.discom_name;
                buildFields(saved.discom_name);
                setTimeout(() => {
                    for (const [k, v] of Object.entries(saved)) {
                        const el = form.querySelector(`[name="${k}"]`);
                        if (el && el !== dropdown) el.value = v;
                    }
                }, 50);
            }
        } catch (e) {}
    }

    // ── Events ──
    dropdown.addEventListener('change', function () {
        buildFields(this.value);
    });

    form.addEventListener('submit', function () {
        // Save state
        const data = { discom_name: dropdown.value };
        form.querySelectorAll('input').forEach(inp => {
            if (inp.type !== 'password') data[inp.name] = inp.value;
        });
        try { localStorage.setItem('sb_formData', JSON.stringify(data)); } catch (e) {}

        // Show loader, disable button
        loader.classList.add('visible');
        fetchBtn.disabled = true;
        document.getElementById('btnText').textContent = 'Fetching…';
    });

    resetBtn.addEventListener('click', function () {
        form.reset();
        extraInner.innerHTML = '';
        extraFields.classList.remove('visible');
        try { localStorage.removeItem('sb_formData'); } catch (e) {}
        const notif = document.getElementById('notification');
        if (notif) notif.style.display = 'none';
    });

    window.addEventListener('load', function () {
        loader.classList.remove('visible');
        fetchBtn.disabled = false;
        document.getElementById('btnText').textContent = 'Fetch Bill';
    });

    restoreState();
})();
</script>
</body>
</html>
