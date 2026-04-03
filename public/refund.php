<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Return / Refund Policy | SmartCare</title>
  <style>
    :root {
      --bg: #f8fafc;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #475569;
      --accent: #0ea5e9;
      --accent-dark: #0369a1;
      --border: #e2e8f0;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text);
      background: linear-gradient(160deg, #e0f2fe 0%, #f8fafc 45%, #f1f5f9 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .wrap {
      width: min(860px, 92vw);
      margin: 3rem auto;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 2rem;
      box-shadow: 0 14px 40px rgba(2, 6, 23, 0.08);
    }

    h1 {
      margin-top: 0;
      font-size: clamp(1.5rem, 2.8vw, 2.2rem);
    }

    p {
      line-height: 1.7;
      color: var(--muted);
      margin: 0.75rem 0;
    }

    .meta {
      font-size: 0.92rem;
      color: #64748b;
      margin-bottom: 1.4rem;
    }

    .actions {
      margin-top: 1.8rem;
      display: flex;
      gap: 0.8rem;
      flex-wrap: wrap;
    }

    a.btn {
      text-decoration: none;
      color: #fff;
      background: var(--accent);
      padding: 0.62rem 1rem;
      border-radius: 10px;
      font-weight: 600;
      transition: background 0.2s ease;
    }

    a.btn:hover { background: var(--accent-dark); }

    footer {
      margin-top: auto;
      border-top: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(4px);
    }

    .footer-inner {
      width: min(860px, 92vw);
      margin: 0 auto;
      padding: 1rem 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .footer-links a {
      text-decoration: none;
      color: #334155;
      margin-right: 1rem;
      font-size: 0.95rem;
    }

    .footer-links a:hover { color: var(--accent-dark); }
  </style>
</head>
<body>
  <main class="wrap">
    <h1>Return / Refund Policy</h1>
    <div class="meta">Last updated: April 3, 2026</div>
    <p>This system uses a sandbox payment environment. No real payments are processed, and therefore refunds are not applicable.</p>
    <p>All transaction records are simulated to demonstrate booking-payment integration for coursework and project evaluation only.</p>

    <div class="actions">
      <a class="btn" href="index.php">Back to Home</a>
    </div>
  </main>

  <footer>
    <div class="footer-inner">
      <span>© 2026 SmartCare</span>
      <div class="footer-links">
        <a href="terms.php">Terms</a>
        <a href="privacy.php">Privacy</a>
        <a href="refund.php">Refund</a>
      </div>
    </div>
  </footer>
</body>
</html>
